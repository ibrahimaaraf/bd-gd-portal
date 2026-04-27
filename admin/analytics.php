<?php
require_once __DIR__ . '/../includes/gd_helpers.php';
$user = require_login('admin');

$byStatus = db()->query('SELECT status label, COUNT(*) total FROM general_diaries GROUP BY status')->fetchAll();
$byType = db()->query('SELECT gd_type label, COUNT(*) total FROM general_diaries GROUP BY gd_type')->fetchAll();

$pageTitle = 'Analytics';
$activeNav = 'reports';
require __DIR__ . '/../includes/header.php';
?>
<h1 class="h2 mb-4">Analytics</h1>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="app-card">
            <h2 class="h5 mb-3">By status</h2>
            <?php foreach ($byStatus as $row): ?>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-capitalize"><?= e(str_replace('_', ' ', $row['label'])) ?></span>
                    <strong><?= e((string) $row['total']) ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="app-card">
            <h2 class="h5 mb-3">By type</h2>
            <?php foreach ($byType as $row): ?>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-capitalize"><?= e(str_replace('_', ' ', $row['label'])) ?></span>
                    <strong><?= e((string) $row['total']) ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
