# Tinder Backend API

A Laravel-based RESTful API for a Tinder-like mobile application with swipe functionality, pagination, and automated notifications.

## Features

- **People Profiles**: Manage person data (name, age, pictures, location)
- **Like/Dislike System**: Track user preferences with swipe actions
- **Smart Recommendations**: Paginated list excluding already swiped profiles
- **Liked List API**: View all people a user has liked
- **Automated Notifications**: Hourly cronjob emails admin when person gets 50+ likes
- **API Documentation**: Interactive Swagger/OpenAPI documentation

## Tech Stack

- **Framework**: Laravel 12.0
- **Language**: PHP 8.2+
- **Database**: SQLite (RDBMS with foreign keys & constraints)
- **API Docs**: Swagger/OpenAPI 3.0 (darkaonline/l5-swagger)

## Project Structure

```
tinder-backend/
├── app/
│   ├── Console/Commands/
│   │   └── CheckPopularPeople.php      # Cronjob for 50+ likes notification
│   ├── Http/Controllers/Api/
│   │   └── PersonController.php        # All API endpoints
│   ├── Mail/
│   │   └── PopularPersonNotification.php  # Email notification
│   └── Models/
│       ├── Person.php                  # Person profile model
│       ├── Like.php                    # Like tracking
│       └── Dislike.php                 # Dislike tracking
├── database/
│   ├── migrations/                     # Database schema
│   └── seeders/                        # Test data (53 people)
├── routes/
│   ├── api.php                         # API routes
│   └── console.php                     # Scheduled commands
└── resources/views/emails/
    └── popular-person.blade.php        # Email template
```

## Database Schema

### People Table
| Column | Type | Description |
|--------|------|-------------|
| id | Primary Key | Auto-increment |
| name | String | Person's name |
| age | Integer | Person's age |
| pictures | JSON | Array of image URLs |
| location | String | City, State format |
| like_count | Integer | Tracks total likes (for threshold) |
| admin_notified | Boolean | Prevents duplicate notifications |

### Likes Table
| Column | Type | Description |
|--------|------|-------------|
| id | Primary Key | Auto-increment |
| user_id | Foreign Key → users.id | Who liked |
| person_id | Foreign Key → people.id | Who was liked |
| unique(user_id, person_id) | Constraint | Prevents duplicates |

### Dislikes Table
| Column | Type | Description |
|--------|------|-------------|
| id | Primary Key | Auto-increment |
| user_id | Foreign Key → users.id | Who disliked |
| person_id | Foreign Key → people.id | Who was disliked |
| unique(user_id, person_id) | Constraint | Prevents duplicates |

## Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- SQLite

### Installation

```bash
# Clone repository
git clone https://github.com/yourusername/tinder-backend.git
cd tinder-backend

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations & seed database
php artisan migrate --force
php artisan db:seed --force

# Generate Swagger documentation
php artisan l5-swagger:generate

# Start server
php artisan serve
```

Server runs at: `http://localhost:8000`

## API Endpoints

### Base URL
```
http://localhost:8000/api/v1
```

### 1. Get Recommended People
```http
GET /people/recommended
```

**Query Parameters:**
- `page` (optional) - Page number (default: 1)
- `per_page` (optional) - Items per page (default: 10)
- `user_id` (optional) - Excludes already liked/disliked people

**Response:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "Emma Watson",
      "age": 28,
      "pictures": ["https://..."],
      "location": "New York, NY"
    }
  ],
  "per_page": 10,
  "total": 53
}
```

### 2. Like a Person
```http
POST /people/{id}/like
```

**Request Body:**
```json
{
  "user_id": 1
}
```

**Response:**
```json
{
  "message": "Person liked successfully",
  "like": { "id": 1, "user_id": 1, "person_id": 1 }
}
```

### 3. Dislike a Person
```http
POST /people/{id}/dislike
```

**Request Body:**
```json
{
  "user_id": 1
}
```

**Response:**
```json
{
  "message": "Person disliked successfully",
  "dislike": { "id": 1, "user_id": 1, "person_id": 2 }
}
```

### 4. Get Liked People
```http
GET /people/liked?user_id=1
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Emma Watson",
      "age": 28,
      "pictures": ["https://..."],
      "location": "New York, NY",
      "liked_at": "2025-11-09 08:16:44"
    }
  ]
}
```

## Interactive API Documentation

Access Swagger UI at:
```
http://localhost:8000/api/documentation
```

Test all endpoints interactively with built-in request/response examples.

## Scheduled Tasks (Cronjob)

The application includes an hourly scheduled task:

```bash
# Manual execution
php artisan people:check-popular

# Run scheduler (for production)
php artisan schedule:work
```

**What it does:**
1. Checks for people with `like_count > 50`
2. Sends email to `ADMIN_EMAIL` (set in .env)
3. Marks person as `admin_notified` to prevent duplicates

**Email Configuration:**
```env
# .env
MAIL_MAILER=log  # Logs emails to storage/logs/laravel.log
ADMIN_EMAIL=admin@example.com
```

For production, configure SMTP:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

## Testing

### Test with cURL

```bash
# Get recommended people
curl "http://localhost:8000/api/v1/people/recommended?per_page=5"

# Like a person
curl -X POST "http://localhost:8000/api/v1/people/1/like" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 1}'

# Get liked people
curl "http://localhost:8000/api/v1/people/liked?user_id=1"
```

### Test Cronjob

```bash
# Create a person with 50+ likes
php artisan tinker --execute="Person::first()->update(['like_count' => 51]);"

# Run the cronjob
php artisan people:check-popular

# Check email logs
tail storage/logs/laravel.log
```

##  Deployment
Deployed on heroku : https://tinder-backend-muskan-bb1ddfe30637.herokuapp.com/api/documentation

##  Author

Developed with ♥️ by muskan

---

