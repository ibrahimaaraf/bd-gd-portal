<?php
require_once __DIR__ . '/../includes/gd_helpers.php';
$user = require_login('police');

$counts = array_fill_keys(GD_STATUSES, 0);
$rows = db()->query('SELECT status, COUNT(*) total FROM general_diaries GROUP BY status')->fetchAll();
foreach ($rows as $row) {
    $counts[$row['status']] = (int) $row['total'];
}
$latest = db()->query('SELECT gd.*, u.name citizen_name FROM general_diaries gd JOIN users u ON u.id = gd.user_id ORDER BY gd.created_at DESC LIMIT 8')->fetchAll();

$pageTitle = 'Police Dashboard';
$activeNav = 'dashboard';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
    <div>
        <div class="eyebrow">Police</div>
        <h1 class="h2 mb-0">Dashboard</h1>
    </div>
    <a class="btn btn-primary align-self-md-center" href="<?= e(app_url('police/manage_gd.php')) ?>"><i class="bi bi-kanban me-2"></i>Manage GD</a>
</div>
<div class="row g-3 mb-4">
    <?php foreach ($counts as $status => $total): ?>
        <div class="col-6 col-lg">
            <div class="stat-card">
                <div class="stat-value"><?= $total ?></div>
                <div class="text-secondary text-capitalize"><?= e(str_replace('_', ' ', $status)) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div class="app-card table-card">
    <h2 class="h5 mb-3">Latest submissions</h2>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Reference</th><th>Citizen</th><th>Subject</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($latest as $gd): ?>
                <tr>
                    <td><?= e($gd['reference_no']) ?></td>
                    <td><?= e($gd['citizen_name']) ?></td>
                    <td><?= e($gd['subject']) ?></td>
                    <td><?= gd_status_badge($gd['status']) ?></td>
                    <td><a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('police/view_gd.php?id=' . $gd['id'])) ?>">Open</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
