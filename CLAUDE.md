# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

BeeFit is a Laravel 12 fitness tracking web app. Users create exercise routines, log workout sessions with real-time set/rep/weight tracking, and view personal records. Premium tiers are gated via Stripe subscriptions.

## Commands

All commands run from inside the `src/` directory unless using Docker.

### Docker (recommended)
```bash
# Start all services (PHP, Nginx, MySQL, Redis, Mailpit)
docker-compose up

# Access: http://localhost:8000 | Mailpit: http://localhost:8025
```

### Local development
```bash
cd src
composer install && npm install
php artisan key:generate
php artisan migrate --seed

# Run everything concurrently (server + queue + logs + Vite)
composer run dev

# Or individually:
php artisan serve
npm run dev
```

### Build & lint
```bash
cd src
npm run build          # Production frontend build
./vendor/bin/pint      # PHP linting/auto-fix (Laravel Pint)
```

### Tests
```bash
cd src
composer test          # Runs Pest PHP test suite
php artisan test --filter=TestName   # Single test
```

### Database
```bash
cd src
php artisan migrate
php artisan db:seed
php artisan migrate:fresh --seed   # Full reset
```

## Architecture

### Stack
- **Backend**: Laravel 12, PHP 8.x
- **Frontend**: Livewire 4 (reactive components), Alpine.js, TailwindCSS 3, Vite
- **Auth**: Laravel Breeze + Sanctum
- **Payments**: Stripe PHP SDK
- **Testing**: Pest PHP 3
- **Database**: MySQL 8 (Docker) / SQLite (default local)
- **Queue/Cache/Sessions**: Database driver

### Key Models (`src/app/Models/`)
- `User` — roles (`admin`, `trainer`, `premium`, `user`), plans (`free`, `premium`, `trainer`), Stripe fields, bio metrics (height, weight, birthdate, gender), theme preferences, locale
- `Routine` → `RoutineExercise` → `RoutineSet` — template structure for workouts
- `Exercise` — equipment, primary muscle, thumbnail/video, custom flag; uses `HasTranslations` trait
- `Workout` → `WorkoutExercise` → `WorkoutSet` — live session data; status: `active`, `paused`, `completed`, `cancelled`
- `PersonalRecord` — auto-calculated on workout completion (max weight, max reps, estimated 1RM via Epley formula)
- `Equipment`, `Muscle` — both have translation tables (`equipment_translations`, `muscle_translations`)

### Multi-language
Exercises, equipment, and muscles are translated via dedicated `*_translations` tables with a `locale` column (`pt`, `en`, `es`). The `HasTranslations` trait handles lookups. Users select locale at registration.

### Livewire Components (`src/app/Livewire/`)
- **`Dashboard`** — calendar view of completed workouts
- **`Library/LibraryPanel`** — exercise browser with search + equipment/muscle filters
- **`Library/ExerciseViewer`** — exercise detail with translation
- **`Library/ExerciseHistory`** — personal records per exercise
- **`Routine/RoutineManager`** — create routines; enforces plan limits (free: max 3)
- **`Routine/RoutineEditor`** — add/reorder exercises in a routine
- **`Routine/RoutineList`** — list all user routines
- **`Workout/WorkoutSession`** — live workout tracking: update sets, pause/resume/cancel, add exercises mid-session, finish & compute PRs
- **`Workout/WorkoutShow`** — completed workout summary
- **`Workout/ActiveWorkoutBanner`** — persistent banner when a workout is in progress
- **`Statistics`** — personal records overview

### Subscription Flow (`src/app/Http/Controllers/Web/SubscriptionController.php`)
- `GET /plans` → pricing page
- `POST /subscription/checkout` → initiate Stripe Checkout session
- `GET /subscription/success` → handle post-payment redirect, update user plan
- `GET /subscription/portal` → Stripe Customer Portal
- `POST /stripe/webhook` → handle `customer.subscription.*` and `invoice.payment_failed` events

### Plan Limits
Enforced in `RoutineManager` and other Livewire components:
- `free`: max 3 routines
- `premium`, `trainer`, `admin`: unlimited

### Routes (`src/routes/web.php`)
All routes require authentication except auth routes. Main route groups:
- `/dashboard`, `/routines`, `/library`, `/statistics` — main app pages
- `/routines/{routine}` → `RoutineEditor` Livewire component
- `/workouts/{workout}/session` → `WorkoutSession` Livewire component
- `/plans`, `/subscription/*`, `/stripe/webhook` — Stripe integration

### Docker Services
| Service | Container | Port |
|---------|-----------|------|
| PHP/Apache | beeFit | — |
| Nginx | beeFit_nginx | 8000 |
| MySQL 8 | beeFit_mysql | 3307 |
| Redis | beeFit_redis | 6379 |
| Mailpit | mailpit | 8025 |

Credentials: DB `beeFit` / `beeFit` / `secret` (from root `.env`).
