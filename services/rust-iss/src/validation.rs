use serde::{Deserialize, Serialize};
use validator::Validate;

#[derive(Debug, Deserialize, Validate)]
pub struct IssQueryParams {
    #[validate(range(min = 1, max = 1000))]
    pub limit: Option<i32>,
}

#[derive(Debug, Deserialize, Validate)]
pub struct OsdrQueryParams {
    #[validate(range(min = 1, max = 5000))]
    pub limit: Option<i32>,
}

#[derive(Debug, Deserialize, Validate)]
pub struct ApodQueryParams {
    #[validate(range(min = 1, max = 100))]
    pub limit: Option<i32>,
}

#[derive(Debug, Deserialize, Validate)]
pub struct NeoQueryParams {
    #[validate(range(min = 1, max = 500))]
    pub limit: Option<i32>,
}

#[derive(Debug, Deserialize, Validate)]
pub struct DonkiQueryParams {
    #[validate(range(min = 1, max = 500))]
    pub limit: Option<i32>,
}

#[derive(Debug, Deserialize, Validate)]
pub struct SpaceXQueryParams {
    #[validate(range(min = 1, max = 500))]
    pub limit: Option<i32>,
}

#[derive(Debug, Serialize)]
pub struct ValidationError {
    pub field: String,
    pub message: String,
}

pub fn validation_errors_to_vec(errors: validator::ValidationErrors) -> Vec<ValidationError> {
    errors
        .field_errors()
        .into_iter()
        .flat_map(|(field, errors)| {
            errors.iter().map(move |error| ValidationError {
                field: field.to_string(),
                message: error.message.as_ref()
                    .map(|m| m.to_string())
                    .unwrap_or_else(|| format!("Validation failed for {}", field)),
            })
        })
        .collect()
}
