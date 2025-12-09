# Space Dashboard - Распределённый монолит для космических данных

[![Docker](https://img.shields.io/badge/Docker-Ready-blue)](https://www.docker.com/)
[![Rust](https://img.shields.io/badge/Rust-1.75+-orange)](https://www.rust-lang.org/)
[![PHP](https://img.shields.io/badge/PHP-8.3-purple)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-11-red)](https://laravel.com/)

## 📋 Описание проекта

Проект "Кассиопея" - распределенная система для сбора, обработки и визуализации космических данных из открытых источников NASA, ISS и других космических API. Система реализована как микросервисная архитектура на базе Docker.

### 🏗️ Архитектура системы

**Основные компоненты:**

| Сервис | Технология | Порт | Описание |
|--------|-----------|------|----------|
| `rust_iss` | Rust + Axum + SQLx | 8081 | Backend API для космических данных |
| `php_web` | PHP 8.3 + Laravel 11 | - | Web-приложение с дашбордами |
| `nginx` | Nginx 1.27 | 8080 | Reverse proxy + статика |
| `db` | PostgreSQL 16 | 5432 | Основная БД |
| `redis` | Redis 7 | 6379 | Кэш и rate limiting |
| `pascal_legacy` | Free Pascal | - | Legacy-модуль для генерации CSV/XLSX |

### 🚀 Функциональность

#### Доступные дашборды:

1. **ISS (МКС)** - `/iss`
   - Отслеживание позиции Международной космической станции в реальном времени
   - Графики высоты и скорости
   - Карта с текущим положением

2. **JWST** - `/jwst`
   - Данные с телескопа James Webb Space Telescope
   - Галерея изображений
   - Информация о программах наблюдения

3. **Astro Events** - `/astro`
   - Астрономические события (затмения, парады планет, метеорные потоки)
   - Сортировка по дате и типу события
   - Клиентская фильтрация

4. **NASA OSDR** - `/osdr`
   - Open Science Data Repository
   - **Поиск в реальном времени** по всем полям таблицы
   - **Переключение количества записей** (20/50/100/All)
   - Сортировка по любому столбцу (↑/↓)
   - Счетчик: "Показано X из Y (Всего: Z)"
   - Просмотр raw JSON данных

#### Ключевые возможности:

✅ **Performance**
- Rate Limiting: 100 запросов/минуту на IP (Redis)
- Клиентская фильтрация таблиц без перезагрузки
- Загрузка до 500 записей для быстрого поиска

✅ **Security**
- Валидация входных параметров (validator crate)
- Rate limiting на уровне middleware
- Защита от SQL-инъекций через prepared statements

✅ **UX/UI**
- Адаптивный дизайн (Bootstrap 5)
- Темная космическая тема с glassmorphism
- CSS-анимации (fade-in, slide-up)
- Мгновенный поиск с подсветкой активных фильтров

✅ **Data Processing**
- Автоматическая генерация CSV/XLSX с типизацией:
  - Timestamp (Unix time) → Date + Time
  - Boolean → "ИСТИНА"/"ЛОЖЬ"
  - Numeric → NUMERIC(6,2)
  - String → TEXT с кавычками

## 🛠️ Технологический стек

### Backend

**Rust Service** (`rust_iss`)
- **Framework**: Axum 0.7
- **Database**: SQLx (async PostgreSQL driver)
- **Validation**: validator crate
- **Rate Limiting**: Redis + custom middleware
- **Runtime**: Tokio (async multi-threaded)
- **Features**:
  - Фоновые задачи для периодического сбора данных
  - REST API с валидацией query параметров
  - Structured error responses

**PHP Service** (`php_web`)
- **Framework**: Laravel 11
- **Template Engine**: Blade
- **Architecture**: Controller per context (ISS, JWST, Astro, OSDR)
- **Features**:
  - Разделение по контекстам (каждой функции своя страница)
  - Proxy для внешних API
  - ViewModel-driven views (без SQL/HTTP в шаблонах)

**Pascal Legacy** (`pascal_legacy`)
- **Compiler**: Free Pascal (FPC)
- **Features**:
  - Генерация CSV с правильными типами данных
  - Экспорт в XLSX (XML Spreadsheet format)
  - Автоматическая конвертация timestamp → Date + Time
  - Запись в PostgreSQL через `psql COPY`

### Frontend

- **Framework**: Vanilla JavaScript (без зависимостей)
- **UI Library**: Bootstrap 5.3
- **Styling**: Custom CSS (Glassmorphism, Dark Theme, Animations)
- **Features**:
  - Client-side search and filtering
  - Dynamic table pagination
  - Real-time result counters

### Infrastructure

- **Database**: PostgreSQL 16 с JSONB поддержкой
- **Cache**: Redis 7 (AOF persistence)
- **Proxy**: Nginx 1.27 Alpine
- **Orchestration**: Docker Compose v2
- **Healthchecks**: Встроены во все критичные сервисы

## 🚀 Быстрый старт

### Требования

- Docker Desktop 4.x+ / Docker Engine 24.x+
- Docker Compose v2+
- 4GB RAM минимум
- Порты 8080, 8081, 5432, 6379 должны быть свободны

### Установка и запуск

```bash
# 1. Клонировать репозиторий
git clone https://github.com/EbatteSratte/he-path-of-the-samurai.git
cd he-path-of-the-samurai

# 2. (Опционально) Создать .env файл с вашими API ключами
cp .env.example .env
nano .env

# 3. Собрать и запустить все сервисы
docker-compose up -d --build

# 4. Проверить статус
docker-compose ps

# 5. Просмотреть логи
docker-compose logs -f rust_iss
docker-compose logs -f php_web

# 6. Открыть в браузере
# Dashboard: http://localhost:8080
# API Health: http://localhost:8081/health
```

### Остановка и очистка

```bash
# Остановить все сервисы
docker-compose down

# Удалить volumes (БД и кэш)
docker-compose down -v

# Полная очистка (включая образы)
docker-compose down -v --rmi all
```

## ⚙️ Конфигурация

### Переменные окружения

Создайте `.env` файл в корне проекта:

```env
# === Database ===
POSTGRES_DB=monolith
POSTGRES_USER=monouser
POSTGRES_PASSWORD=monopass

# === Redis ===
REDIS_URL=redis://redis:6379

# === NASA API ===
NASA_API_KEY=DEMO_KEY
NASA_API_URL=https://visualization.osdr.nasa.gov/biodata/api/v2/datasets/?format=json

# === JWST API ===
JWST_HOST=https://api.jwstapi.com
JWST_API_KEY=your_jwst_key
JWST_EMAIL=your@email.com
JWST_PROGRAM_ID=2734

# === Astronomy API ===
ASTRO_APP_ID=your_app_id
ASTRO_APP_SECRET=your_app_secret

# === Fetch Intervals (seconds) ===
FETCH_EVERY_SECONDS=600       # OSDR data
ISS_EVERY_SECONDS=120          # ISS position
APOD_EVERY_SECONDS=43200       # Astronomy Picture of the Day (12h)
NEO_EVERY_SECONDS=7200         # Near Earth Objects (2h)
DONKI_EVERY_SECONDS=3600       # Space Weather (1h)
SPACEX_EVERY_SECONDS=3600      # SpaceX launches (1h)

# === Pascal Legacy ===
GEN_PERIOD_SEC=300             # CSV generation interval (5 min)
```

## 📁 Структура проекта

```
he-path-of-the-samurai/
├── docker-compose.yml              # Оркестрация всех сервисов
├── Readme.md                       # Этот файл
├── db/
│   └── init.sql                    # Инициализация БД (таблицы)
└── services/
    ├── rust-iss/                   # Rust Backend
    │   ├── Dockerfile
    │   ├── Cargo.toml
    │   └── src/
    │       ├── main.rs             # Entry point + routes
    │       ├── rate_limit.rs       # Rate limiting middleware
    │       └── validation.rs       # Request validation
    ├── php-web/                    # Laravel Frontend
    │   ├── Dockerfile
    │   ├── nginx.conf
    │   ├── entrypoint.sh
    │   └── laravel-patches/
    │       ├── app/
    │       │   └── Http/
    │       │       └── Controllers/
    │       │           ├── DashboardController.php
    │       │           ├── IssController.php
    │       │           ├── JwstController.php
    │       │           ├── AstroController.php
    │       │           └── OsdrController.php
    │       ├── resources/
    │       │   └── views/
    │       │       ├── layouts/
    │       │       │   └── app.blade.php   # Global layout + styles
    │       │       ├── dashboard.blade.php
    │       │       ├── iss.blade.php
    │       │       ├── osdr.blade.php      # Search + filtering
    │       │       └── ...
    │       └── routes/
    │           └── web.php
    └── pascal-legacy/              # Pascal Legacy Service
        ├── Dockerfile
        ├── legacy.pas              # CSV/XLSX generator
        └── run.sh
```

## 🎯 Основные улучшения

### ✨ Changelog

#### v1.3.0 - Client-side Search & Pagination (OSDR)
- ✅ Мгновенный поиск по всем колонкам + JSON
- ✅ Переключатели количества записей (20/50/100/All)
- ✅ Счетчик отфильтрованных результатов
- ✅ Загрузка до 500 записей с бэка

#### v1.2.0 - Rate Limiting & Validation
- ✅ Redis интегрирован
- ✅ Rate limiting middleware (100 req/min per IP)
- ✅ Валидация query параметров
- ✅ Validation классы для каждого эндпоинта

#### v1.1.0 - CSV/XLSX Improvements (Pascal)
- ✅ Типизированные данные в CSV
- ✅ Генерация XLSX с timestamp → Date + Time
- ✅ Обновлена схема БД

#### v1.0.0 - Architecture Refactoring
- ✅ Разделение контроллеров по контекстам
- ✅ Удаление legacy CMS
- ✅ Space Theme UI
- ✅ Flexible dashboards с сортировкой

## 📄 License

MIT License

---

⭐ **Star this repo** if you find it useful!
