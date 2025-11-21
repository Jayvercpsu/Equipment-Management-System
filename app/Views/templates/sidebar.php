<?php 
$session = session();
$currentPath = uri_string();
$roleName = $session->get('role_name');
?>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h4><i class="bi bi-gear-fill me-2"></i>ITSO Equipment</h4>
        <small class="d-block mt-1 text-white-50"><?= esc($session->get('first_name')) ?> <?= esc($session->get('last_name')) ?></small>
    </div>
    
    <nav class="nav flex-column">
        <div class="nav-section">
            <div class="nav-section-title">Main</div>
            <a href="<?= base_url('admin/dashboard') ?>" class="nav-link <?= strpos($currentPath, 'dashboard') !== false ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <?php if ($roleName === 'itso_personnel'): ?>
        <div class="nav-section">
            <div class="nav-section-title">Administration</div>
            <a href="<?= base_url('admin/users') ?>" class="nav-link <?= strpos($currentPath, 'admin/users') !== false ? 'active' : '' ?>">
                <i class="bi bi-people"></i>
                <span>Users</span>
            </a>
        </div>
        <?php endif; ?>
        
        <div class="nav-section">
            <div class="nav-section-title">Equipment</div>
            <a href="<?= base_url('admin/equipment') ?>" class="nav-link <?= strpos($currentPath, 'admin/equipment') !== false ? 'active' : '' ?>">
                <i class="bi bi-laptop"></i>
                <span>Equipment List</span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Transactions</div>
            <a href="<?= base_url('admin/borrow') ?>" class="nav-link <?= strpos($currentPath, 'admin/borrow') !== false ? 'active' : '' ?>">
                <i class="bi bi-arrow-right-circle"></i>
                <span>Borrowing</span>
            </a>
            <a href="<?= base_url('admin/return') ?>" class="nav-link <?= strpos($currentPath, 'admin/return') !== false ? 'active' : '' ?>">
                <i class="bi bi-arrow-left-circle"></i>
                <span>Returns</span>
            </a>
            <?php if ($roleName !== 'student'): ?>
            <a href="<?= base_url('admin/reservation') ?>" class="nav-link <?= strpos($currentPath, 'admin/reservation') !== false ? 'active' : '' ?>">
                <i class="bi bi-calendar-check"></i>
                <span>Reservations</span>
            </a>
            <?php endif; ?>
        </div>
        
        <?php if ($roleName === 'itso_personnel'): ?>
        <div class="nav-section">
            <div class="nav-section-title">Reports</div>
            <a href="<?= base_url('admin/reports/active-equipment') ?>" class="nav-link <?= strpos($currentPath, 'reports/active-equipment') !== false ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-text"></i>
                <span>Active Equipment</span>
            </a>
            <a href="<?= base_url('admin/reports/unusable-equipment') ?>" class="nav-link <?= strpos($currentPath, 'reports/unusable-equipment') !== false ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-x"></i>
                <span>Unusable Equipment</span>
            </a>
            <a href="<?= base_url('admin/reports/user-history') ?>" class="nav-link <?= strpos($currentPath, 'reports/user-history') !== false ? 'active' : '' ?>">
                <i class="bi bi-clock-history"></i>
                <span>User History</span>
            </a>
        </div>
        <?php endif; ?>
        
        <div class="nav-section">
            <div class="nav-section-title">Account</div>
            <a href="<?= base_url('profile') ?>" class="nav-link <?= strpos($currentPath, 'profile') !== false ? 'active' : '' ?>">
                <i class="bi bi-person-circle"></i>
                <span>Profile</span>
            </a>
            <a href="<?= base_url('auth/logout') ?>" class="nav-link">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>
</div>