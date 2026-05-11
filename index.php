<?php
$pageTitle = 'Digital GD Portal';
$activeNav = 'home';
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="hero-panel">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="eyebrow mb-3">Bangladesh General Diary Service</div>
                <h1 class="display-5 fw-bold mb-3">Submit, track, and verify GD records online.</h1>
                <p class="lead text-secondary mb-4">A responsive citizen and police portal for filing General Diary applications, managing investigation status, tracking persons of interest, and verifying GD documents.</p>
                <div class="d-flex flex-wrap gap-2">
                    <?php if ($user): ?>
                        <a class="btn btn-primary btn-lg" href="<?= e(app_url(dashboard_path($user['role']))) ?>"><i class="bi bi-speedometer2 me-2"></i>Open Dashboard</a>
                        <a class="btn btn-outline-primary btn-lg" href="<?= e(app_url('verify.php')) ?>"><i class="bi bi-qr-code-scan me-2"></i>Verify GD QR</a>
                    <?php else: ?>
                        <a class="btn btn-primary btn-lg" href="<?= e(app_url('auth/register.php')) ?>"><i class="bi bi-person-plus me-2"></i>Create Account</a>
                        <a class="btn btn-outline-primary btn-lg" href="<?= e(app_url('auth/login.php')) ?>"><i class="bi bi-box-arrow-in-right me-2"></i>Login</a>
                        <a class="btn btn-outline-primary btn-lg" href="<?= e(app_url('verify.php')) ?>"><i class="bi bi-qr-code-scan me-2"></i>Verify GD QR</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="stat-card">
                            <i class="bi bi-file-earmark-text fs-2 text-primary"></i>
                            <h2 class="h6 mt-3">Online GD</h2>
                            <p class="small text-secondary mb-0">Structured complaint and incident reporting.</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <i class="bi bi-shield-check fs-2 text-primary"></i>
                            <h2 class="h6 mt-3">Verification</h2>
                            <p class="small text-secondary mb-0">Public QR/token based authenticity checks.</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <i class="bi bi-search fs-2 text-primary"></i>
                            <h2 class="h6 mt-3">Tracking</h2>
                            <p class="small text-secondary mb-0">Transparent progress updates for citizens.</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <i class="bi bi-graph-up-arrow fs-2 text-primary"></i>
                            <h2 class="h6 mt-3">Analytics</h2>
                            <p class="small text-secondary mb-0">Admin reports by status, type, and date.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
