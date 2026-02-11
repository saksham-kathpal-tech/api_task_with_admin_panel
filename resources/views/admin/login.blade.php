<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin Login</title>
    <meta name="api-url" content="{{ env('APP_URL') }}/api">

    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('admin/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('admin/css/vertical-layout-light/style.css') }}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('admin/images/favicon.png') }}" />
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo">
                                <img src="{{ asset('admin/images/logo.svg') }}" alt="logo">
                            </div>
                            <h4>Admin Login</h4>
                            <h6 class="fw-light">Sign in to continue.</h6>

                            <div id="alert-message" style="display: none;"></div>

                            <form class="pt-3" id="login-form" onsubmit="handleLogin(event)">
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-lg" id="email" placeholder="Email" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-lg" id="password" placeholder="Password" required>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">SIGN IN</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="{{ asset('admin/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- inject:js -->
    <script src="{{ asset('admin/js/off-canvas.js') }}"></script>
    <script src="{{ asset('admin/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('admin/js/template.js') }}"></script>
    <script src="{{ asset('admin/js/settings.js') }}"></script>
    <script src="{{ asset('admin/js/todolist.js') }}"></script>
    <!-- endinject -->

    <script>
        // Use the page origin to avoid CORS issues when hostnames differ (e.g. localhost vs 127.0.0.1)
        const API_URL = window.location.origin + '/api';

        async function handleLogin(event) {
            event.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const alertDiv = document.getElementById('alert-message');

            try {
                const response = await fetch(`${API_URL}/admin/login`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password
                    })
                });
                let data = {};
                try {
                    data = await response.json();
                } catch (e) {
                    // Non-JSON response (HTML) — provide a generic message
                    data.message = 'An unexpected server response was received.';
                }

                if (response.ok && data.success) {
                    // Ensure only admin users can log into admin panel
                    const returnedUser = data.user || {};
                    if (returnedUser.role !== 'admin') {
                        alertDiv.className = 'alert alert-danger';
                        alertDiv.innerText = 'Access denied. Admin credentials required.';
                        alertDiv.style.display = 'block';
                        return;
                    }

                    // Store token in localStorage
                    localStorage.setItem('token', data.token);
                    localStorage.setItem('user', JSON.stringify(returnedUser));

                    // Show success message
                    alertDiv.className = 'alert alert-success';
                    alertDiv.innerText = 'Login successful! Redirecting...';
                    alertDiv.style.display = 'block';

                    // Redirect to dashboard after 1 second
                    setTimeout(() => {
                        window.location.href = '{{ route("admin.dashboard") }}';
                    }, 1000);
                } else {
                    alertDiv.className = 'alert alert-danger';
                    // Show validation errors if present
                    if (data.errors) {
                        // Join first-level error messages
                        const messages = Object.values(data.errors).map(arr => Array.isArray(arr) ? arr.join(' ') : arr).join(' ');
                        alertDiv.innerText = messages || data.message || 'Login failed';
                    } else {
                        alertDiv.innerText = data.message || 'Login failed';
                    }
                    alertDiv.style.display = 'block';
                }
            } catch (error) {
                alertDiv.className = 'alert alert-danger';
                alertDiv.innerText = 'An error occurred. Please try again.';
                alertDiv.style.display = 'block';
                console.error('Error:', error);
            }
        }

        // Check if user is already logged in
        window.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('token');
            if (token) {
                window.location.href = '{{ route("admin.dashboard") }}';
            }
        });
    </script>
</body>

</html>
