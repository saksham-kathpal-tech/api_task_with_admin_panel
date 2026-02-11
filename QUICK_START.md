# Quick Start Guide - Laravel JWT Admin Panel

## Installation (5 minutes)

### 1. Prerequisites
- XAMPP with Apache + MySQL
- PHP 8.2+
- Composer

### 2. Setup Database
```bash
# Ensure MySQL is running in XAMPP
# Create database
mysql -u root
> CREATE DATABASE api_task;
> EXIT;
```

### 3. Configure .env
```bash
# The .env file is already configured
# Update database credentials if needed:
DB_DATABASE=api_task
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Install & Run
```bash
cd c:\xampp\htdocs\api_task_with_admin_panel

# Install dependencies
composer install

# Generate app key (if needed)
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed admin user
php artisan db:seed

# Start development server
php artisan serve
```

## Access Points

**Admin Panel Login**: http://localhost:8000/admin/login
- Email: `ravinder@possibilitysolutions.com`
- Password: `123456`

**API Base URL**: http://localhost:8000/api

## API Quick Test

### 1. Register a User
```bash
POST http://localhost:8000/api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### 2. Login User
```bash
POST http://localhost:8000/api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

### 3. Admin Login
```bash
POST http://localhost:8000/api/login
Content-Type: application/json

{
  "email": "ravinder@possibilitysolutions.com",
  "password": "123456"
}
```

### 4. View All Users (Admin Only)
```bash
GET http://localhost:8000/api/users
Authorization: Bearer {admin_token}
```

### 5. Block User (Admin Only)
```bash
POST http://localhost:8000/api/users/{user_id}/block
Authorization: Bearer {admin_token}
```

## Admin Panel Features

1. **Dashboard**
   - View total users
   - View active users
   - View blocked users
   - Quick access links

2. **Users Management**
   - View all registered users
   - Edit user information
   - Block/Unblock users
   - Delete users
   - Add new users

## Important Notes

- All tokens are stored in browser localStorage
- Blocked users cannot login
- Admin account cannot be deleted via normal registration
- All API responses include success flag and appropriate HTTP status codes

## Default Credentials

**Admin**:
- Email: ravinder@possibilitysolutions.com
- Password: 123456
- Role: admin

## Troubleshooting

### Port 8000 already in use
```bash
php artisan serve --port=8001
```

### Database connection error
- Ensure MySQL is running
- Check .env database credentials
- Run `php artisan migrate --force`

### JWT Secret not set
```bash
php artisan jwt:secret --force
```

### Token expired
- JWTs expire after 1 hour
- Login again to get a new token

## Next Steps

1. Import Postman collection: `Laravel_JWT_Admin_Panel_API.postman_collection.json`
2. Test each endpoint systematically
3. Use admin panel for manual testing
4. Review code in `app/Http/Controllers/API/`

## Support

For detailed documentation, see `README_API_SETUP.md`
