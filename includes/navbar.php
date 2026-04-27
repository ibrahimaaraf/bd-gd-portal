<?php
$user = $user ?? current_user();
?>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top app-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= e(app_url('index.php')) ?>">
            <span class="brand-mark">GD</span> Digital GD Portal
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link <?= $activeNav === 'home' ? 'active' : '' ?>" href="<?= e(app_url('index.php')) ?>">Home</a></li>
                <?php if ($user): ?>
                    <li class="nav-item"><a class="nav-link <?= $activeNav === 'dashboard' ? 'active' : '' ?>" href="<?= e(app_url(dashboard_path($user['role']))) ?>">Dashboard</a></li>
                    <?php if ($user['role'] === 'citizen'): ?>
                        <li class="nav-item"><a class="nav-link <?= $activeNav === 'submit' ? 'active' : '' ?>" href="<?= e(app_url('citizen/submit_gd.php')) ?>">Submit GD</a></li>
                        <li class="nav-item"><a class="nav-link <?= $activeNav === 'track' ? 'active' : '' ?>" href="<?= e(app_url('citizen/track_gd.php')) ?>">Track</a></li>
                    <?php elseif ($user['role'] === 'police'): ?>
                        <li class="nav-item"><a class="nav-link <?= $activeNav === 'manage' ? 'active' : '' ?>" href="<?= e(app_url('police/manage_gd.php')) ?>">Manage GD</a></li>
                        <li class="nav-item"><a class="nav-link <?= $activeNav === 'poi' ? 'active' : '' ?>" href="<?= e(app_url('police/poi_tracker.php')) ?>">POI</a></li>
                    <?php elseif ($user['role'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link <?= $activeNav === 'users' ? 'active' : '' ?>" href="<?= e(app_url('admin/manage_users.php')) ?>">Users</a></li>
                        <li class="nav-item"><a class="nav-link <?= $activeNav === 'reports' ? 'active' : '' ?>" href="<?= e(app_url('admin/reports.php')) ?>">Reports</a></li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><?= e($user['name']) ?></a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text small text-muted text-capitalize"><?= e($user['role']) ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= e(app_url('auth/logout.php')) ?>">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link <?= $activeNav === 'login' ? 'active' : '' ?>" href="<?= e(app_url('auth/login.php')) ?>">Login</a></li>
                    <li class="nav-item"><a class="btn btn-light btn-sm ms-lg-2" href="<?= e(app_url('auth/register.php')) ?>">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
