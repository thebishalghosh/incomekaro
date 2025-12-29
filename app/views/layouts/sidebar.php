<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo url('dashboard/super_admin'); ?>" class="sidebar-brand">
            <img src="<?php echo asset('images/logo.png'); ?>" alt="<?php echo SITE_NAME; ?>" style="max-height: 40px;">
        </a>
    </div>
    <ul class="sidebar-menu">

        <!-- SUPER ADMIN MENU -->
        <?php if (isset($_SESSION['role_code']) && $_SESSION['role_code'] == 'SUPER_ADMIN'): ?>
            <li>
                <a href="<?php echo url('dashboard/super_admin'); ?>" class="<?php echo (strpos($_GET['url'] ?? '', 'dashboard') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li class="menu-header small text-uppercase text-white-50 px-4 py-2 mt-2">Management</li>
            <li>
                <a href="<?php echo url('white_label/index'); ?>" class="<?php echo (strpos($_GET['url'] ?? '', 'white_label') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-building"></i> White Labels
                </a>
            </li>
            <li>
                <a href="<?php echo url('partner/index'); ?>" class="<?php echo (strpos($_GET['url'] ?? '', 'partner') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-handshake"></i> Partners
                </a>
            </li>
            <li>
                <a href="<?php echo url('user/index'); ?>" class="<?php echo (strpos($_GET['url'] ?? '', 'user') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>

            <li class="menu-header small text-uppercase text-white-50 px-4 py-2 mt-2">Operations</li>
            <li>
                <a href="<?php echo url('application/index'); ?>" class="<?php echo (strpos($_GET['url'] ?? '', 'application') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i> All Applications
                </a>
            </li>
            <li>
                <a href="<?php echo url('withdrawal/index'); ?>" class="<?php echo (strpos($_GET['url'] ?? '', 'withdrawal') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-wallet"></i> Withdrawals
                </a>
            </li>

            <li class="menu-header small text-uppercase text-white-50 px-4 py-2 mt-2">System</li>
            <li>
                <a href="<?php echo url('subscription/index'); ?>" class="<?php echo (strpos($_GET['url'] ?? '', 'subscription') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-tags"></i> Subscription Plans
                </a>
            </li>
            <li>
                <a href="<?php echo url('service/index'); ?>" class="<?php echo (strpos($_GET['url'] ?? '', 'service') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-box-open"></i> Master Services
                </a>
            </li>
            <li>
                <a href="#" class="<?php echo (strpos($_GET['url'] ?? '', 'report') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i> Global Reports
                </a>
            </li>

        <!-- WHITE LABEL MENU -->
        <?php elseif (isset($_SESSION['role_code']) && $_SESSION['role_code'] == 'WHITE_LABEL'): ?>
            <li>
                <a href="<?php echo url('dashboard/white_label'); ?>" class="<?php echo (strpos($_GET['url'] ?? '', 'dashboard') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li class="menu-header small text-uppercase text-white-50 px-4 py-2 mt-2">Business</li>
            <li>
                <a href="#" class="<?php echo (strpos($_GET['url'] ?? '', 'partner') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-handshake"></i> My Partners
                </a>
            </li>
            <li>
                <a href="#" class="<?php echo (strpos($_GET['url'] ?? '', 'application') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i> Applications
                </a>
            </li>
            <li class="menu-header small text-uppercase text-white-50 px-4 py-2 mt-2">Configuration</li>
            <li>
                <a href="#" class="<?php echo (strpos($_GET['url'] ?? '', 'service') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-cogs"></i> Service Settings
                </a>
            </li>
            <li>
                <a href="#" class="<?php echo (strpos($_GET['url'] ?? '', 'settings') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-paint-brush"></i> Branding & Domain
                </a>
            </li>
        <?php else: ?>
            <li class="px-4 py-2 text-warning">
                Role: <?php echo $_SESSION['role_code'] ?? 'None'; ?>
            </li>
        <?php endif; ?>

        <!-- COMMON LOGOUT -->
        <li class="mt-4">
            <a href="<?php echo url('auth/logout'); ?>" class="text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>
