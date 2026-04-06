# Task Management API System

A RESTful API-based Task Management built using **Laravel 13** and **PHP 8.4**, allowing users to register, login, and manage personal tasks. Every API interaction is logged daily for better tracking.

---

## 🛠 Technical Stack

- **Backend:** PHP 8.4.12, Laravel 13.3.0  
- **Database:** MySQL / PostgreSQL  
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

## 🔧 Installation

1. **Clone repository**
```bash
git clone git@github.com:DevSunilShaw/taskapi.git
cd taskapi