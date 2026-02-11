# Laravel JWT Admin Panel - Complete Authentication System

A production-ready Laravel 12 REST API with JWT authentication, role-based access control, and a fully functional admin panel.

## 📚 Documentation

- **[Quick Start Guide](QUICK_START.md)** - Get started in 5 minutes
- **[Full API Documentation](README_API_SETUP.md)** - Complete API reference
- **[Postman Collection](Laravel_JWT_Admin_Panel_API.postman_collection.json)** - Ready to test

## ✨ Features

- ✅ JWT-based stateless authentication
- ✅ Two roles: Admin and User
- ✅ Admin-only endpoints with middleware
- ✅ User blocking/unblocking
- ✅ Full admin panel UI
- ✅ User management system
- ✅ Real-time statistics
- ✅ Responsive design with Bootstrap 5

## 🚀 Quick Start

```bash
# Install dependencies
composer install

# Run migrations
php artisan migrate --force

# Seed admin user
php artisan db:seed

# Start development server
php artisan serve
```

**Admin Login**: http://localhost:8000/admin/login
- Email: `ravinder@possibilitysolutions.com`
- Password: `123456`

## 📋 API Endpoints

### Public Routes
- `POST /api/register` - Register new user
- `POST /api/login` - Login user/admin

### Protected Routes (JWT Required)
- `GET /api/me` - Get current user
- `POST /api/logout` - Logout user

### Admin Only Routes
- `GET /api/users` - List all users
- `GET /api/users/{id}` - Get single user
- `PUT /api/users/{id}` - Edit user
- `POST /api/users/{id}/block` - Block user
- `POST /api/users/{id}/unblock` - Unblock user
- `DELETE /api/users/{id}` - Delete user

## 🛠 Tech Stack

- Laravel 12.0
- PHP 8.2+
- MySQL
- JWT Auth (tymon/jwt-auth)
- Bootstrap 5
- Vanilla JavaScript (AJAX)

## 📁 Project Structure

```
├── app/Http/
│   ├── Controllers/API/
│   │   ├── AuthController.php
│   │   └── UserController.php
│   ├── Middleware/
│   │   └── AdminMiddleware.php
│   └── Requests/
│       ├── UserLoginRequest.php
│       ├── UserRegisterRequest.php
│       └── UserEditRequest.php
├── resources/views/admin/
│   ├── login.blade.php
│   ├── dashboard.blade.php
│   └── users.blade.php
├── routes/
│   ├── api.php
│   └── web.php
└── database/
    ├── migrations/
    └── seeders/
        └── CreateAdminSeeder.php
```

## 🔐 Security

- Bcrypt password hashing
- JWT token validation
- Role-based access control
- Blocked users cannot login
- Input validation on all endpoints
- CSRF token for web routes

## 📦 Default Admin

- Email: `ravinder@possibilitysolutions.com`
- Password: `123456`
- Role: `admin`
- Created via seeder (cannot be user-registered)

## 🧪 Testing

### Using Postman
1. Import `Laravel_JWT_Admin_Panel_API.postman_collection.json`
2. Set base URL to `http://localhost:8000`
3. Test each endpoint

### Using cURL
```bash
# Register
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Jane","email":"jane@example.com","password":"pass123","password_confirmation":"pass123"}'

# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"jane@example.com","password":"pass123"}'
```

## 📝 Admin Panel Routes

- `/admin/login` - Admin login page
- `/admin/dashboard` - Dashboard with statistics
- `/admin/users` - User management page

## ⚙️ Configuration

JWT configuration is in `config/jwt.php`:
- Algorithm: HS256
- Expiration: 1 hour (3600 seconds)
- Secret in `.env`: `JWT_SECRET`

## 🐛 Troubleshooting

**Port 8000 in use?**
```bash
php artisan serve --port=8001
```

**Database error?**
```bash
php artisan migrate:reset --force
php artisan migrate --force
php artisan db:seed
```

**JWT secret not set?**
```bash
php artisan jwt:secret --force
```

## 📄 License

MIT

## 🤝 Support

Refer to:
- [Full Documentation](README_API_SETUP.md)
- [Quick Start Guide](QUICK_START.md)

---

**Created**: February 2026
**Laravel**: 12.0+
**PHP**: 8.2+
