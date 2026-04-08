# BeeFit

A fitness tracking web app built with Laravel 12. Users create exercise routines, log workout sessions with real-time set/rep/weight tracking, earn XP and level up, unlock achievements, view personal records, share workouts on a social feed, and follow other users.

## Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.x |
| Frontend | Livewire 4, Alpine.js, TailwindCSS 3, Vite |
| Auth | Laravel Breeze + Sanctum |
| Payments | Stripe PHP SDK |
| Real-time | Laravel Reverb (WebSockets) + Laravel Echo |
| Testing | Pest PHP 3 |
| Database | MySQL 8 (Docker) / SQLite (local) |
| Queue / Cache / Sessions | Database driver |

## Features

### Workouts
- Create routines with exercises, sets, reps and weights
- Live workout session tracking (pause, resume, cancel, finish)
- Add exercises mid-session
- Personal records auto-calculated on completion (max weight, max reps, estimated 1RM via Epley formula)
- Free plan limited to 3 routines; Premium/Trainer/Admin unlimited

### Exercise Library
- Browse exercises with search and filters (equipment, muscle group)
- Multilingual exercise names (Portuguese, English, Spanish)
- Exercise history and personal records per exercise

### Social
- Social feed showing posts from followed users
- Post workouts with photos
- Like and comment on posts
- Follow / unfollow users (private accounts require approval)
- Remove followers from your own profile
- Friend suggestions based on mutual follows
- Real-time notifications (follow requests, likes, comments) via WebSockets
- User profiles at `/social/profile/{username}`

### Subscriptions
- Free, Premium and Trainer plans via Stripe Checkout
- Stripe Customer Portal for self-service billing
- Webhook handler for subscription lifecycle events

### Notifications
- In-app notification bell with real-time delivery (Laravel Reverb)
- Accept / reject follow requests directly from notifications
- Notifications for: follow requests, follow accepted, post liked, post commented

### Gamification (RPG)
- XP awarded on every completed workout: 50 base + 5/set + duration bonuses + new-exercise bonuses
- 50-level system with exponential thresholds and 8 title tiers (Beginner → Immortal)
- 18 achievements (first workout, streaks, personal records, heavy lifter, marathon, early bird, …)
- XP progress bar and achievement badges on user profiles
- Global and friends leaderboard ranked by total XP (`/leaderboard`)
- Post-workout modal showing XP earned and newly unlocked achievements
- Level badge displayed next to usernames in the social feed

### Internationalisation
- UI available in Portuguese, English and Spanish
- User selects language at registration; can change in profile settings

## Getting Started

### Docker (recommended)

```bash
docker-compose up
```

| Service | URL |
|---|---|
| App | http://localhost:8000 |
| Mailpit | http://localhost:8025 |
| Reverb (WebSockets) | ws://localhost:8080 |

### Local development

```bash
cd src
composer install && npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
composer run dev   # starts server + queue + logs + Vite concurrently
```

## Commands

All commands run from `src/` unless using Docker.

```bash
# Linting
./vendor/bin/pint

# Tests
composer test
php artisan test --filter=TestName

# Frontend build
npm run build

# Database reset
php artisan migrate:fresh --seed
```

## Architecture

### Key Models

| Model | Description |
|---|---|
| `User` | Roles (`admin`, `trainer`, `premium`, `user`), plans, Stripe fields, bio metrics, username, locale, theme |
| `Routine → RoutineExercise → RoutineSet` | Workout template structure |
| `Exercise` | Equipment, primary muscle, media, translations via `HasTranslations` |
| `Workout → WorkoutExercise → WorkoutSet` | Live session data; statuses: `active`, `paused`, `completed`, `cancelled` |
| `PersonalRecord` | Auto-calculated on workout completion |
| `Follow` | Follower/following relationships with `pending` / `accepted` status |
| `Post → PostComment → PostLike` | Social feed content |
| `Equipment`, `Muscle` | Translated via `*_translations` tables |
| `Achievement` | Key, icon, XP reward; unlocked per user via `user_achievements` pivot |

### Livewire Components

| Component | Path |
|---|---|
| Dashboard | `Dashboard` — calendar of completed workouts |
| Library | `Library/LibraryPanel`, `ExerciseViewer`, `ExerciseHistory` |
| Routines | `Routine/RoutineManager`, `RoutineEditor`, `RoutineList` |
| Workouts | `Workout/WorkoutSession`, `WorkoutShow`, `ActiveWorkoutBanner` |
| Statistics | `Statistics` — personal records overview |
| Social | `Social/SocialFeed`, `UserProfile`, `CreatePost` |
| Notifications | `NotificationBell` — real-time bell with accept/reject |
| Leaderboard | `Leaderboard` — global and friends tabs ranked by XP |
| Admin | `Admin/Dashboard`, `UserManager`, `ExerciseManager`, `CatalogManager`, `AchievementManager` |

### Real-time (Reverb)

Notifications are broadcast over a private WebSocket channel (`App.Models.User.{id}`) via Laravel Reverb. The browser subscribes via Laravel Echo on page load and dispatches a Livewire event to refresh the notification bell without polling.

### Docker Services

| Service | Container | Port |
|---|---|---|
| PHP / Apache | `beeFit` | 8000 |
| Laravel Reverb | `beeFit-reverb` | 8080 |
| MySQL 8 | `beeFit_mysql` | 3307 |
| Redis | `beeFit_redis` | 6379 |

### XP & Achievement Flow

`XpService::processWorkout(User, Workout)` is called inside `WorkoutSession::finishWorkout()` after personal records are computed. It returns `['xp' => int, 'achievements' => Achievement[]]` which drives the post-workout modal. XP is added permanently via `User::addXp()`. Achievements are checked and attached once via `user_achievements` pivot.

The streak counter (`currentStreak`) reads all completed workouts ordered by `finished_at` descending, groups by calendar date, and walks backwards counting consecutive days.

### Subscription Flow

```
GET  /plans                   → pricing page
POST /subscription/checkout   → Stripe Checkout session
GET  /subscription/success    → post-payment redirect, update plan
GET  /subscription/portal     → Stripe Customer Portal
POST /stripe/webhook          → customer.subscription.* events
```

## Environment

Key `.env` variables beyond the Laravel defaults:

```env
# Stripe
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

# Reverb (WebSockets)
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST=reverb          # Docker service name (PHP → Reverb)
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST=localhost   # Browser → Reverb
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Required for immediate broadcast delivery
QUEUE_CONNECTION=sync
```

## License

MIT
