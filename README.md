# Task Management API System

A RESTful API-based Task Management built using **Laravel 13** and **PHP 8.4**, allowing users to register, login, and manage personal tasks. Every API interaction is logged daily for better tracking.

---

## Technical Stack

- **Backend:** PHP 8.4.12, Laravel 13.3.0  
- **Database:** MySQL  
- **Operating System / Environment:** Ubuntu or Debian
- **Authentication:** Laravel Sanctum (Token-based)  
- **Architecture:** MVC  
- **ORM:** Eloquent  
- **Version Control:** Git  
- **Logging:** Daily API request logs with timestamps  

---

## Features

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

## Installation

## 1. Clone the Repository

```bash
git clone git@github.com:DevSunilShaw/taskapi.git
cd taskapi
```

## 2. Install Dependencies

```bash
composer install
```

## 3. Configure Environment

Copy the example `.env` file and generate the application key:

```bash
cp .env.example .env
php artisan key:generate
```

Update the `.env` file with your **database credentials**.

---

## 4. Run Migrations and Seed Database

### a) Run Migrations Only

```bash
php artisan migrate
```

- This will create all database tables according to the migration files.  
- **No data will be seeded.**

### b) Run Migrations Fresh with Seeding

```bash
php artisan migrate:fresh --seed
```

- Drops all existing tables and recreates them.  
- Seeds initial data including a default user and sample tasks.

**Seeded Default User Credentials (for testing):**
- **Email:** dev.sunil.shaw@gmail.com  
- **Password:** 8700166471

> Use these credentials for the login API in Postman or to set the global `{{token}}` variable.

**Postman Registered User (example):**
- **Email:** dev.sunil.shaw02@gmail.com  
- **Password:** 8700166471

> You can use this user to test registration and login APIs separately from the seeded user.


> **Note:** If any migration fails, check your DB connection and credentials.

---

## 5. Start the Laravel Server

```bash
php artisan serve
```

The server will start at: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 6. Import Postman Collection

1. Open Postman  
2. Go to **File > Import > Upload Files**  
3. Import `task-api.postman_collection.json`

The collection includes all endpoints:

- Register  
- Login  
- Logout  
- Task CRUD (Create, Read, Update, Delete)

---

## 7. Set Authorization Token

1. Login using the seeded credentials:  
   - **Email:** dev.sunil.shaw@gmail.com  
   - **Password:** 8700166471  
2. Copy the token from the response  
3. Set it as a Postman global variable: `{{token}}`

> All task APIs will automatically use this token for authentication.

---

## 8. Test APIs

- Create a new task  
- Get all tasks  
- Get a single task  
- Update a task  
- Delete a task  
- Logout

---

## 9. Logs

All logs are stored daily in:

```text
storage/logs/laravel-YYYY-MM-DD.log
```

Use these for auditing and debugging.

---

### 10. Unit & Feature Testing

This project includes **basic unit and feature tests** to verify core API functionality.

#### a) Run All Tests

```bash
php artisan test
```

- Runs all tests located in `tests/Unit` and `tests/Feature`.  


- Runs all tests located in `tests/Unit` and `tests/Feature`.  
- Example output:

```text
PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response                        0.06s  

   PASS  Tests\Feature\TaskApiTest
  ✓ user can register                                                    0.42s  
  ✓ user can login with valid credentials                                0.01s  
  ✓ authenticated user can create task                                   0.01s  
  ✓ authenticated user can fetch tasks                                   0.01s  
  ✓ authenticated user can update task                                   0.01s  
  ✓ authenticated user can delete task                                   0.01s  
  ✓ unauthenticated user cannot access tasks                             0.01s  

  Tests:    9 passed (23 assertions)
  Duration: 0.59s

```

#### b) Example Test Files

1. **`tests/Feature/AuthTest.php`** – tests user registration and login  
2. **`tests/Feature/TaskApiTest.php`** – tests task CRUD operations

#### c) Sample Feature Test (Task Creation)

```php
public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'secret123'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['success', 'token']);
        
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    /** @test */
    public function test_user_can_login_with_valid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'token']);
    }

    /** @test */
    public function test_authenticated_user_can_create_task()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Task description',
            'status' => 'pending',
            'due_date' => now()->addDays(5)->toDateString()
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Test Task']);
        
        $this->assertDatabaseHas('tasks', ['title' => 'Test Task', 'user_id' => $this->user->id]);
    }

    /** @test */
    public function test_authenticated_user_can_fetch_tasks()
    {
        Task::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/tasks');

        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'message', 'data']);
    }

    /** @test */
    public function test_authenticated_user_can_update_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id, 'status' => 'pending']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->putJson("/api/tasks/{$task->id}", [
            'status' => 'completed'
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => 'completed']);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'completed']);
    }

    /** @test */
    public function test_authenticated_user_can_delete_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Task deleted successfully']);

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function test_unauthenticated_user_cannot_access_tasks()
    {
        $response = $this->getJson('/api/tasks');

        $response->assertStatus(401);
    }
```

> This test ensures that an **authenticated user can create a task** and that the task is correctly stored in the database.

#### d) Notes

- You can create more tests for **update, delete, view single task, and task filtering**.  
- Tests run using **in-memory SQLite** or your MySQL database. Update `.env.testing` if needed.


### Preview

## Screenshots

| Screenshot 1 | Screenshot 2 | Screenshot 3 | Screenshot 4 |
|--------------|--------------|--------------|--------------|
| ![16-40-35](Screenshot%20from%202026-04-06%2016-40-35.png) | ![16-41-01](Screenshot%20from%202026-04-06%2016-41-01.png) | ![16-41-11](Screenshot%20from%202026-04-06%2016-41-11.png) | ![16-42-03](Screenshot%20from%202026-04-06%2016-42-03.png) |

| Screenshot 5 | Screenshot 6 | Screenshot 7 | Screenshot 8 |
|--------------|--------------|--------------|--------------|
| ![16-42-57](Screenshot%20from%202026-04-06%2016-42-57.png) | ![16-43-44](Screenshot%20from%202026-04-06%2016-43-44.png) | ![16-44-33](Screenshot%20from%202026-04-06%2016-44-33.png) | ![16-45-06](Screenshot%20from%202026-04-06%2016-45-06.png) |

| Screenshot 9 | Screenshot 10 |
|--------------|---------------|
| ![16-45-09](Screenshot%20from%202026-04-06%2016-45-09.png) | ![16-45-18](Screenshot%20from%202026-04-06%2016-45-18.png) |