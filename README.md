# Bottles Project

Laravel application for managing a **water bottle catalog**: different **water types**, **bottle sizes**, **ingredients** (with price per millilitre), and **compositions** that define how much of each ingredient goes into each water type and bottle size. The app validates that ingredient volumes fit each bottle’s capacity, exposes a **Filament** admin panel, and a **JSON API** for the catalog (including cost breakdowns).

## Tech stack

- Laravel 13, PHP 8.3+
- Filament 5 (admin at `/admin`)
- MySQL (default in `.env.example`; configurable)
- Laravel Sail for Docker-based local development

---

## Database

Domain tables model the catalog and how recipes are built per water type and bottle size.

### `ingredients`

| Column           | Type            | Notes                          |
|------------------|-----------------|--------------------------------|
| `id`             | bigint          | Primary key                    |
| `name`           | string          | Ingredient name                |
| `price_per_ml`   | decimal(12,6)   | Unit price per 1 ml            |
| `created_at` / `updated_at` | timestamps |                    |

### `water_types`

| Column           | Type            | Notes                          |
|------------------|-----------------|--------------------------------|
| `id`             | bigint          | Primary key                    |
| `name`           | string          | Water product name             |
| `created_at` / `updated_at` | timestamps |                    |

Each water type can have many **compositions** (one row per ingredient per bottle size used for that type).

### `bottle_sizes`

| Column           | Type            | Notes                          |
|------------------|-----------------|--------------------------------|
| `id`             | bigint          | Primary key                    |
| `name`           | string          | Label (e.g. small / large)     |
| `capacity_ml`    | unsigned int    | Total bottle volume in ml      |
| `created_at` / `updated_at` | timestamps |                    |

### `compositions`

Links a **water type**, a **bottle size**, and an **ingredient** with a volume in ml.

| Column           | Type            | Notes                          |
|------------------|-----------------|--------------------------------|
| `id`             | bigint          | Primary key                    |
| `water_type_id`  | FK → `water_types` | `cascadeOnDelete`           |
| `bottle_size_id` | FK → `bottle_sizes` | `cascadeOnDelete`          |
| `ingredient_id`  | FK → `ingredients` | `cascadeOnDelete`           |
| `amount_ml`      | decimal(12,3)   | Volume of this ingredient      |
| `created_at` / `updated_at` | timestamps |                    |

**Constraint:** unique on `(water_type_id, bottle_size_id, ingredient_id)` so you cannot duplicate the same ingredient twice for the same water type and bottle size.

Application logic ensures the sum of `amount_ml` for a given water type + bottle size does not exceed that bottle size’s `capacity_ml`.

### Other tables

Standard Laravel tables: `users` (Filament admin), `cache`, `jobs`, sessions, etc., as per default migrations.

---

## Install and run with Laravel Sail

Prerequisites: **Docker** (Docker Desktop or Docker Engine + Compose).

### 1. Clone and install PHP dependencies

```bash
git clone <repository-url> BottlesProject
cd BottlesProject
composer install
```

### 2. Environment file

```bash
cp .env.example .env
```

Choose the services you need (at minimum **mysql** to match `.env.example`). Sail will write `docker-compose.yml` and adjust `.env` with `DB_HOST=mysql` and related variables.

### 3. Application key

```bash
php artisan key:generate
```

Or after containers are up:

```bash
./vendor/bin/sail artisan key:generate
```

### 4. Start containers

```bash
./vendor/bin/sail up -d
```

Optional alias (add to your shell profile):

```bash
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

### 5. Database: migrate and seed

```bash
./vendor/bin/sail artisan migrate --seed
```

This creates tables and loads demo catalog data plus an admin user.

### 7. Frontend assets (Vite)

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

For local development with hot reload:

```bash
./vendor/bin/sail npm run dev
```

### 8. Open the app

- Set `APP_URL` in `.env` to match how you reach the app (Sail often uses `http://localhost` if port 80 is mapped).
- **Filament admin:** `{APP_URL}/admin/login`  
  - Email: `admin@bottles.local`  
  - Password: `password`  
  Change these in production.
- **API (paginated catalog):** `GET {APP_URL}/api/water-bottles`  
  (Default page size is 3; use `?page=2`, etc.)

### Run tests inside Sail

```bash
./vendor/bin/sail artisan test --compact
```

---

## Local development without Sail

Use a local PHP 8.3+ runtime and MySQL (or change `DB_*` in `.env`), then:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

---

## License

MIT (same as Laravel skeleton unless otherwise stated).
