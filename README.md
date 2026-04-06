# Task Management API

## Setup
1. Clone repo
2. composer install
3. cp .env.example .env
4. php artisan key:generate
5. Setup database
6. php artisan migrate
7. php artisan serve

## API Endpoints

POST /api/register
POST /api/login
POST /api/logout
GET /api/tasks
POST /api/tasks
GET /api/tasks/{id}
PUT /api/tasks/{id}
DELETE /api/tasks/{id}

## Auth Flow
1. Register/Login
2. Copy token
3. Use Bearer Token in header