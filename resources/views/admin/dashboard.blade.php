@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="home-tab">
            <div class="tab-content tab-content-basic">
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="statistics-details d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="statistics-title">Total Users</p>
                                    <h3 class="rate-percentage" id="total-users">0</h3>
                                </div>
                                <div>
                                    <p class="statistics-title">Active Users</p>
                                    <h3 class="rate-percentage" id="active-users">0</h3>
                                </div>
                                <div>
                                    <p class="statistics-title">Blocked Users</p>
                                    <h3 class="rate-percentage" id="blocked-users">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" style="margin-top: 20px;">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Quick Actions</h5>
                <div>
                    <a href="{{ route('admin.users') }}" class="btn btn-primary btn-sm me-2">Manage Users</a>
                    <button class="btn btn-secondary btn-sm" onclick="refreshStats()">Refresh Stats</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_js')
<script>
    // const API_URL = document.querySelector('meta[name="api-url"]').getAttribute('content');

    const API_URL = window.location.origin + '/api';

    async function checkAuth() {
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '{{ route("admin.login") }}';
            return false;
        }

        // Verify token and role with API
        try {
            const response = await fetch(`${API_URL}/me`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                window.location.href = '{{ route("admin.login") }}';
                return false;
            }

            const data = await response.json();
            if (!data.success || !data.user || data.user.role !== 'admin') {
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                window.location.href = '{{ route("admin.login") }}';
                return false;
            }

            // Update stored user to ensure role is current
            localStorage.setItem('user', JSON.stringify(data.user));
            return true;
        } catch (err) {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '{{ route("admin.login") }}';
            return false;
        }
    }

    function logout() {
        if (confirm('Are you sure you want to logout?')) {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '{{ route("admin.login") }}';
        }
    }

    function viewProfile() {
        const user = JSON.parse(localStorage.getItem('user'));
        alert(`Name: ${user.name}\nEmail: ${user.email}\nRole: ${user.role}`);
    }

    async function loadStats() {
        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`${API_URL}/admin/users`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) {
                if (response.status === 401) {
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    window.location.href = '{{ route("admin.login") }}';
                }
                throw new Error('Failed to fetch users');
            }

            const data = await response.json();
            const users = data.users || [];

            const totalUsers = users.length;
            const activeUsers = users.filter(u => u.status).length;
            const blockedUsers = users.filter(u => !u.status).length;

            document.getElementById('total-users').innerText = totalUsers;
            document.getElementById('active-users').innerText = activeUsers;
            document.getElementById('blocked-users').innerText = blockedUsers;

            // Update navbar
            const user = JSON.parse(localStorage.getItem('user'));
            document.getElementById('user-name').innerText = user.name;
            document.getElementById('user-email').innerText = user.email;
            document.getElementById('user-role').innerText = user.role;
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function refreshStats() {
        loadStats();
    }

    // Check authentication and load stats when page loads
    window.addEventListener('DOMContentLoaded', function() {
        if (checkAuth()) {
            loadStats();
        }
    });
</script>
@endsection
