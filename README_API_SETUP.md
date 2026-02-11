# Laravel 10+ JWT Authentication System with Admin Panel

A production-ready Laravel REST API application with JWT authentication, role-based access control (Admin & User roles), and a fully functional Admin Panel UI.

## Overview

This project implements a complete authentication and user management system with the following features:

- **JWT-based Authentication**: Stateless authentication using `tymon/jwt-auth`
- **Role-Based Access Control**: Two roles - Admin and User
- **Admin Panel**: Full-featured web interface for managing users
- **REST API**: Complete API endpoints for all operations
- **Security**: Custom middleware for role enforcement, proper validation

## Features

### Authentication
- User registration via API
- JWT-based login (prevents blocked users from logging in)
- User logout with token invalidation
- Admin login
- Token-based authentication for all protected routes

### User Management (Admin Only)
- View all registered users
- View single user details
- Edit user information (name, email)
- Block/Unblock users
- Delete users
- Create new users via admin panel

### Admin Panel UI
- Responsive admin dashboard
- Login page
- User management page
- Real-time user statistics
- AJAX-based operations
- localStorage for JWT token storage

## Technology Stack

- **Laravel**: 12.0
- **PHP**: 8.2+
- **MySQL**: Database
- **JWT Auth**: `tymon/jwt-auth` v2.0+
- **Bootstrap**: 5.x (Admin Template)
- **JavaScript**: Vanilla JS for API calls

## Installation & Setup

### 1. Prerequisites
- PHP 8.2 or higher
- MySQL Server running
- Composer installed
- Apache/Nginx server

### 2. Clone & Install Dependencies
```bash
cd c:\xampp\htdocs\api_task_with_admin_panel
composer install
npm install
```

### 3. Environment Configuration
The `.env` file is already configured with:
```env
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=api_task
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Database Setup
```bash
# Run migrations
php artisan migrate --force

# Seed default admin user
php artisan db:seed
```

**Default Admin Credentials:**
- Email: `ravinder@possibilitysolutions.com`
- Password: `123456`

### 5. Start Development Server
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/API/
│   │   │   ├── AuthController.php      # Authentication logic
│   │   │   └── UserController.php      # User management logic
│   │   ├── Middleware/
│   │   │   └── AdminMiddleware.php     # Admin role verification
│   │   └── Requests/
│   │       ├── UserLoginRequest.php    # Login validation
│   │       ├── UserRegisterRequest.php # Registration validation
│   │       └── UserEditRequest.php     # Edit validation
│   └── Models/
│       └── User.php                    # User model with JWT support
├── config/
│   ├── auth.php                        # Auth guards configuration
│   └── jwt.php                         # JWT configuration
├── database/
│   ├── migrations/
│   │   └── 0001_01_01_000000_create_users_table.php
│   └── seeders/
│       └── CreateAdminSeeder.php       # Admin creation seeder
├── resources/views/admin/
│   ├── layout.blade.php                # Main admin layout
│   ├── login.blade.php                 # Admin login page
│   ├── dashboard.blade.php             # Dashboard
│   ├── users.blade.php                 # Users management page
│   └── partials/                       # Layout partials
│       ├── navbar.blade.php
│       ├── sidebar.blade.php
│       ├── settings-panel.blade.php
│       └── footer.blade.php
├── routes/
│   ├── api.php                         # API routes
│   └── web.php                         # Web routes
└── public/admin/                       # Admin template assets
    ├── css/
    ├── js/
    ├── vendors/
    ├── images/
    └── fonts/
```

## API Endpoints

### Authentication Routes (Public)

#### 1. User Registration
```
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}

Response (201):
{
  "success": true,
  "message": "User registered successfully",
  "user": { ... }
}
```

#### 2. User Login
```
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}

Response (200):
{
  "success": true,
  "message": "Login successful",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": { ... }
}
```

#### 3. Admin Login
```
POST /api/login
Content-Type: application/json

{
  "email": "ravinder@possibilitysolutions.com",
  "password": "123456"
}

Response (200):
{
  "success": true,
  "message": "Login successful",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "ravinder@possibilitysolutions.com",
    "role": "admin",
    "status": true
  }
}
```

### Protected Routes (Require JWT Token)

#### 4. Get Current User
```
GET /api/me
Authorization: Bearer {token}

Response (200):
{
  "success": true,
  "user": { ... }
}
```

#### 5. Logout
```
POST /api/logout
Authorization: Bearer {token}

Response (200):
{
  "success": true,
  "message": "Logged out successfully"
}
```

### Admin Only Routes (Admin Middleware Required)

#### 6. Get All Users
```
GET /api/users
Authorization: Bearer {admin_token}

Response (200):
{
  "success": true,
  "users": [
    {
      "id": 2,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "user",
      "status": true,
      "created_at": "2026-02-11T10:20:30.000000Z"
    }
  ]
}
```

#### 7. Get Single User
```
GET /api/users/{id}
Authorization: Bearer {admin_token}

Response (200):
{
  "success": true,
  "user": { ... }
}
```

#### 8. Edit User
```
PUT /api/users/{id}
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "name": "Jane Doe",
  "email": "jane@example.com"
}

Response (200):
{
  "success": true,
  "message": "User updated successfully",
  "user": { ... }
}
```

#### 9. Block User
```
POST /api/users/{id}/block
Authorization: Bearer {admin_token}

Response (200):
{
  "success": true,
  "message": "User blocked successfully",
  "user": { "id": 2, "status": false, ... }
}
```

#### 10. Unblock User
```
POST /api/users/{id}/unblock
Authorization: Bearer {admin_token}

Response (200):
{
  "success": true,
  "message": "User unblocked successfully",
  "user": { "id": 2, "status": true, ... }
}
```

#### 11. Delete User
```
DELETE /api/users/{id}
Authorization: Bearer {admin_token}

Response (200):
{
  "success": true,
  "message": "User deleted successfully"
}
```

## HTTP Status Codes

- `200`: Success
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized (Invalid token or not logged in)
- `403`: Forbidden (Not admin when admin required)
- `404`: Not Found
- `422`: Validation Error

## Admin Panel Routes

- **Login**: `http://localhost:8000/admin/login`
- **Dashboard**: `http://localhost:8000/admin/dashboard`
- **Users Management**: `http://localhost:8000/admin/users`

## Database Schema

### Users Table
```sql
CREATE TABLE users (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  email_verified_at TIMESTAMP NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'user') DEFAULT 'user',
  status BOOLEAN DEFAULT true,
  remember_token VARCHAR(100) NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

## JWT Configuration

JWT secret is stored in `.env`:
```env
JWT_SECRET=i93JeQN2mSdBHhaHmIkw8uB4UdwfeBNcvUwR4tIA6S5oCOjlgpJIwA5kb0XznZFl
```

JWT claims include:
- `sub`: User ID
- `iss`: Issuer
- `aud`: Audience
- `exp`: Expiration
- `iat`: Issued At
- `role`: User Role (admin/user)
- `status`: User Status (true/false)

## Security Features

1. **Password Hashing**: Using bcrypt (Laravel default)
2. **JWT Tokens**: Stateless authentication
3. **Role-Based Middleware**: Admin-only routes protected
4. **Validation**: Request validation with custom rules
5. **Blocked User Prevention**: Blocked users cannot login
6. **Token Invalidation**: Logout invalidates tokens

## Testing with Postman

1. Import the provided `Laravel_JWT_Admin_Panel_API.postman_collection.json` into Postman
2. Set the base URL to `http://localhost:8000`
3. For admin operations, first login as admin and store the token in the `admin_token` variable
4. All subsequent admin requests will use the token from the variable

### Quick Test Flow:
1. Register a new user via `/api/register`
2. Login as user via `/api/login`
3. Login as admin via `/api/login` (use admin credentials)
4. View users via `/api/users`
5. Block a user via `/api/users/{id}/block`
6. Edit a user via `/api/users/{id}` (PUT)
7. Delete a user via `/api/users/{id}` (DELETE)

## Key Implementation Details

### Authentication Flow
1. User provides email & password
2. System verifies credentials
3. Check if user is blocked (admin can block users)
4. If blocked, return 403 Forbidden
5. If valid, JWT token is generated
6. Token contains user id, role, and status

### Admin Middleware
```php
// Only users with 'admin' role can access admin routes
Route::middleware('admin')->group(function () {
    // Admin-only routes
});
```

### Validation
- All inputs are validated
- Email uniqueness is enforced (on registration & edit)
- Passwords require confirmation on registration
- Custom validation messages provided

## Common Issues & Solutions

### Issue: "jwt-auth secret not set"
**Solution**: Run `php artisan jwt:secret --force`

### Issue: "Table users doesn't exist"
**Solution**: Run `php artisan migrate --force`

### Issue: "Cannot login as admin"
**Solution**: Ensure seeder ran - Run `php artisan db:seed`

### Issue: "Admin middleware not working"
**Solution**: Ensure admin guard is configured in `config/auth.php`

### Issue: "CORS errors in browser"
**Solution**: The API is on the same domain, so CORS shouldn't be an issue. Clear browser cache.

## Deployment Notes

For production deployment:

1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
2. Ensure `APP_KEY` is unique per environment
3. Update `APP_URL` to your production domain
4. Use strong database passwords
5. Store JWT_SECRET securely
6. Enable HTTPS
7. Set proper permissions on `storage/` and `bootstrap/cache/`

## Support & Documentation

- Laravel Documentation: https://laravel.com/docs
- JWT Auth Documentation: https://jwt-auth.readthedocs.io
- Admin Template: Star Admin 2 Free

## License

This project is provided as-is for evaluation purposes.

## Author

Developer Name
Created: February 2026
