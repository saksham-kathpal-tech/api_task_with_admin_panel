<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item nav-category">User Management</li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.users') }}">
                <i class="menu-icon mdi mdi-account-multiple"></i>
                <span class="menu-title">Users</span>
            </a>
        </li>
        <li class="nav-item nav-category">System</li>
        <li class="nav-item">
            <a class="nav-link" onclick="logout()">
                <i class="menu-icon mdi mdi-logout"></i>
                <span class="menu-title">Logout</span>
            </a>
        </li>
    </ul>
</nav>
