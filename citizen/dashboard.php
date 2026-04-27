<?php
require_once __DIR__ . '/../includes/gd_helpers.php';
$user = require_login('citizen');

$stmt = db()->prepare('SELECT status, COUNT(*) total FROM general_diaries WHERE user_id = ? GROUP BY status');
$stmt->execute([$user['id']]);
$counts = array_fill_keys(GD_STATUSES, 0);
foreach ($stmt->fetchAll() as $row) {
    $counts[$row['status']] = (int) $row['total'];
}
$stmt = db()->prepare('SELECT COUNT(*) FROM general_diaries WHERE user_id = ?');
$stmt->execute([$user['id']]);
$counts['submitted'] = (int) $stmt->fetchColumn();

$stmt = db()->prepare('SELECT * FROM general_diaries WHERE user_id = ? ORDER BY created_at DESC LIMIT 5');
$stmt->execute([$user['id']]);
$recent = $stmt->fetchAll();

$pageTitle = 'Citizen Dashboard';
$activeNav = 'dashboard';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
    <div>
        <div class="eyebrow">Citizen</div>
        <h1 class="h2 mb-0">Dashboard</h1>
    </div>
    <a class="btn btn-primary align-self-md-center" href="<?= e(app_url('citizen/submit_gd.php')) ?>"><i class="bi bi-plus-circle me-2"></i>Submit New GD</a>
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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">Recent GDs</h2>
        <a class="link-primary" href="<?= e(app_url('citizen/track_gd.php')) ?>">View all</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Reference</th><th>Type</th><th>Status</th><th>Date</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($recent as $gd): ?>
                <tr>
                    <td><?= e($gd['reference_no']) ?></td>
                    <td><?= e(str_replace('_', ' ', $gd['gd_type'])) ?></td>
                    <td><?= gd_status_badge($gd['status']) ?></td>
                    <td><?= e(date('M d, Y', strtotime($gd['created_at']))) ?></td>
                    <td><a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('citizen/download_gd.php?id=' . $gd['id'])) ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$recent): ?>
                <tr><td colspan="5" class="text-center text-secondary py-4">No GD submitted yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
