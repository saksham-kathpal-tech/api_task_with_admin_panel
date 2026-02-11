@extends('admin.layout')

@section('title', 'Users Management')

@section('extra_css')
<style>
    .action-buttons {
        display: flex;
        gap: 5px;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    .modal-form {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }
    .modal-content {
        background-color: #fefefe;
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
        border-radius: 5px;
    }
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    .close:hover {
        color: black;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title">Users Management</h5>
                    <button class="btn btn-success btn-sm" onclick="openAddUserModal()">Add New User</button>
                </div>

                <div id="alert-message" style="display: none;"></div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="users-tbody">
                            <tr>
                                <td colspan="7" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editModal" class="modal-form">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h4>Edit User</h4>
        <form id="edit-form" onsubmit="handleEditUser(event)">
            <div class="form-group mb-3">
                <label for="edit-id">User ID</label>
                <input type="text" id="edit-id" class="form-control" readonly>
            </div>
            <div class="form-group mb-3">
                <label for="edit-name">Name</label>
                <input type="text" id="edit-name" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label for="edit-email">Email</label>
                <input type="email" id="edit-email" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeEditModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Add User Modal -->
<div id="addModal" class="modal-form">
    <div class="modal-content">
        <span class="close" onclick="closeAddUserModal()">&times;</span>
        <h4>Add New User</h4>
        <form id="add-form" onsubmit="handleAddUser(event)">
            <div class="form-group mb-3">
                <label for="add-name">Name</label>
                <input type="text" id="add-name" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label for="add-email">Email</label>
                <input type="email" id="add-email" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label for="add-password">Password</label>
                <input type="password" id="add-password" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <button type="submit" class="btn btn-primary btn-sm">Create</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeAddUserModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('extra_js')
<script>
    // const API_URL = document.querySelector('meta[name="api-url"]').getAttribute('content');

    const API_URL = window.location.origin + '/api';

    function checkAuth() {
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '{{ route("admin.login") }}';
            return false;
        }
        return true;
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

    async function loadUsers() {
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

            const tbody = document.getElementById('users-tbody');
            tbody.innerHTML = '';

            if (users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">No users found</td></tr>';
                return;
            }

            users.forEach(user => {
                const createdAt = new Date(user.created_at).toLocaleDateString();
                const statusBadge = user.status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Blocked</span>';
                const statusBtn = user.status ? 
                    `<button class="btn btn-danger btn-sm" onclick="blockUser(${user.id})">Block</button>` :
                    `<button class="btn btn-warning btn-sm" onclick="unblockUser(${user.id})">Unblock</button>`;

                const row = `
                    <tr>
                        <td>${user.id}</td>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${user.role}</td>
                        <td>${statusBadge}</td>
                        <td>${createdAt}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-primary btn-sm" onclick="editUser(${user.id})">Edit</button>
                                ${statusBtn}
                                <button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id})">Delete</button>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });

            // Update navbar
            const user = JSON.parse(localStorage.getItem('user'));
            document.getElementById('user-name').innerText = user.name;
            document.getElementById('user-email').innerText = user.email;
            document.getElementById('user-role').innerText = user.role;
        } catch (error) {
            console.error('Error:', error);
            const tbody = document.getElementById('users-tbody');
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading users</td></tr>';
        }
    }

    async function editUser(userId) {
        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`${API_URL}/admin/users/${userId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch user');
            }

            const data = await response.json();
            const user = data.user;

            document.getElementById('edit-id').value = user.id;
            document.getElementById('edit-name').value = user.name;
            document.getElementById('edit-email').value = user.email;

            document.getElementById('editModal').style.display = 'block';
        } catch (error) {
            alert('Error fetching user details');
            console.error('Error:', error);
        }
    }

    async function handleEditUser(event) {
        event.preventDefault();

        const userId = document.getElementById('edit-id').value;
        const name = document.getElementById('edit-name').value;
        const email = document.getElementById('edit-email').value;

        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`${API_URL}/admin/users/${userId}`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name: name,
                    email: email
                })
            });

            const data = await response.json();

            if (data.success) {
                showAlert('success', 'User updated successfully');
                closeEditModal();
                loadUsers();
            } else {
                showAlert('danger', data.message || 'Failed to update user');
            }
        } catch (error) {
            showAlert('danger', 'An error occurred');
            console.error('Error:', error);
        }
    }

    async function blockUser(userId) {
        if (!confirm('Are you sure you want to block this user?')) return;

        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`${API_URL}/admin/users/${userId}/block`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showAlert('success', 'User blocked successfully');
                loadUsers();
            } else {
                showAlert('danger', data.message || 'Failed to block user');
            }
        } catch (error) {
            showAlert('danger', 'An error occurred');
            console.error('Error:', error);
        }
    }

    async function unblockUser(userId) {
        if (!confirm('Are you sure you want to unblock this user?')) return;

        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`${API_URL}/admin/users/${userId}/unblock`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showAlert('success', 'User unblocked successfully');
                loadUsers();
            } else {
                showAlert('danger', data.message || 'Failed to unblock user');
            }
        } catch (error) {
            showAlert('danger', 'An error occurred');
            console.error('Error:', error);
        }
    }

    async function deleteUser(userId) {
        if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) return;

        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`${API_URL}/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            const data = await response.json();

            if (data.success) {
                showAlert('success', 'User deleted successfully');
                loadUsers();
            } else {
                showAlert('danger', data.message || 'Failed to delete user');
            }
        } catch (error) {
            showAlert('danger', 'An error occurred');
            console.error('Error:', error);
        }
    }

    async function handleAddUser(event) {
        event.preventDefault();

        const name = document.getElementById('add-name').value;
        const email = document.getElementById('add-email').value;
        const password = document.getElementById('add-password').value;

        try {
            const response = await fetch(`${API_URL}/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name: name,
                    email: email,
                    password: password,
                    password_confirmation: password
                })
            });

            const data = await response.json();

            if (data.success) {
                showAlert('success', 'User created successfully');
                closeAddUserModal();
                document.getElementById('add-form').reset();
                loadUsers();
            } else {
                showAlert('danger', data.message || 'Failed to create user');
            }
        } catch (error) {
            showAlert('danger', 'An error occurred');
            console.error('Error:', error);
        }
    }

    function openAddUserModal() {
        document.getElementById('addModal').style.display = 'block';
    }

    function closeAddUserModal() {
        document.getElementById('addModal').style.display = 'none';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function showAlert(type, message) {
        const alertDiv = document.getElementById('alert-message');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.innerText = message;
        alertDiv.style.display = 'block';

        setTimeout(() => {
            alertDiv.style.display = 'none';
        }, 3000);
    }

    // Click outside modal to close
    window.onclick = function(event) {
        let modal = document.getElementById('editModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
        modal = document.getElementById('addModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // Load users when page loads
    window.addEventListener('DOMContentLoaded', function() {
        if (checkAuth()) {
            loadUsers();
        }
    });
</script>
@endsection
