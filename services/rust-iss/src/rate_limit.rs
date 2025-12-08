use axum::{
    body::Body,
    http::{Request, StatusCode},
    middleware::Next,
    response::Response,
    Extension,
};
use redis::{aio::ConnectionManager, AsyncCommands};
use std::sync::Arc;
use std::time::{SystemTime, UNIX_EPOCH};

const RATE_LIMIT_WINDOW: i64 = 60; // 1 minute
const MAX_REQUESTS: u32 = 100;      // 100 requests per minute

#[derive(Clone)]
pub struct RateLimiter {
    redis: Arc<ConnectionManager>,
}

impl RateLimiter {
    pub fn new(redis: ConnectionManager) -> Self {
        Self {
            redis: Arc::new(redis),
        }
    }
}

pub async fn rate_limit_middleware(
    Extension(limiter): Extension<RateLimiter>,
    req: Request<Body>,
    next: Next,
) -> Result<Response, StatusCode> {
    let client_ip = req
        .headers()
        .get("x-forwarded-for")
        .and_then(|v| v.to_str().ok())
        .unwrap_or("unknown");

    let key = format!("rate_limit:{}", client_ip);
    let now = SystemTime::now()
        .duration_since(UNIX_EPOCH)
        .unwrap()
        .as_secs();

    let mut conn = limiter.redis.as_ref().clone();

    // Get current count
    let count: Option<u32> = conn.get(&key).await.ok();

    if let Some(c) = count {
        if c >= MAX_REQUESTS {
            return Err(StatusCode::TOO_MANY_REQUESTS);
        }
    }

    // Increment
    let _: () = conn.incr(&key, 1).await.map_err(|_| StatusCode::INTERNAL_SERVER_ERROR)?;
    
    // Set expiry on first request
    if count.is_none() {
        let _: () = conn.expire(&key, RATE_LIMIT_WINDOW).await
            .map_err(|_| StatusCode::INTERNAL_SERVER_ERROR)?;
    }

    Ok(next.run(req).await)
}
