# Task Management API System

A RESTful API-based Task Management built using **Laravel 13** and **PHP 8.4**, allowing users to register, login, and manage personal tasks. Every API interaction is logged daily for better tracking.

---

## 🛠 Technical Stack

- **Backend:** PHP 8.4.12, Laravel 13.3.0  
- **Database:** MySQL  
- **Authentication:** Laravel Sanctum (Token-based)  
- **Architecture:** MVC  
- **ORM:** Eloquent  
- **Version Control:** Git  
- **Logging:** Daily API request logs with timestamps  

---

## 📌 Features

### 1. User Authentication
- **Register** a new user
- **Login** with email and password
- **Logout** with token invalidation
- Token-based authentication using Laravel Sanctum
- All login attempts are logged (excluding passwords for security)

### 2. Task Management
- **Create** tasks
- **View all tasks** for a user
- **View a single task**
- **Update tasks**
- **Delete tasks** (Soft delete enabled)
- **Task fields:**  
  - `title`  
  - `description`  
  - `status` (`pending`, `in-progress`, `completed`)  
  - `due_date`
- Daily logs track all API hits for task creation, updates, deletion, and retrieval
- Soft-deleted tasks are not shown in normal queries but can be restored if needed

### 3. Logging & Tracking
- Every API hit (login, register, task CRUD) is logged with:
  - **Date and time**
  - **User ID**
  - **Action details** (e.g., task created, updated)
- Logs are stored in **daily log files** automatically

---

## 🔧 Installation & API Testing

Follow these steps to set up, seed, and test the **Task Management API**:

---

### 1. Clone the repository
```bash
git clone git@github.com:DevSunilShaw/taskapi.git
cd taskapi
2. Install dependencies
composer install
3. Copy .env file and update DB credentials
cp .env.example .env
php artisan key:generate

Update the .env file with your database credentials.

4. Run migrations and seed database
php artisan migrate:fresh --seed

This will:

Create users and tasks tables
Seed a default user:
Email: dev.sunil.shaw@gmail.com
Password: 8700166471
Seed multiple tasks for all users

Note: If any migration fails, check your DB connection and credentials.

5. Run the Laravel server
php artisan serve

The server will start at http://127.0.0.1:8000.

6. Import Postman Collection
Open Postman
Go to File > Import > Upload Files
Import task-api.postman_collection.json
The collection contains all endpoints:
Register
Login
Logout
Task CRUD (Create, Read, Update, Delete)
7. Set Authorization Token
Login using seeded credentials:
Email: dev.sunil.shaw@gmail.com
Password: 8700166471
Copy the token from the response
Set it as Postman global variable {{token}}
All task APIs use this token automatically for authentication
8. Test APIs
Create a new task
Get all tasks
Get single task
Update a task
Delete a task
Logout

All logs are stored daily in storage/logs/laravel-YYYY-MM-DD.log for auditing and debugging.