<?php
require_once __DIR__ . '/../includes/gd_helpers.php';
$user = require_login('citizen');
$search = trim($_GET['q'] ?? '');

$sql = 'SELECT * FROM general_diaries WHERE user_id = ?';
$params = [$user['id']];
if ($search !== '') {
    $sql .= ' AND (reference_no LIKE ? OR subject LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
$sql .= ' ORDER BY created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$gds = $stmt->fetchAll();

$pageTitle = 'Track GD';
$activeNav = 'track';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
    <h1 class="h2 mb-0">Track GD</h1>
    <form class="d-flex gap-2" method="get">
        <input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Reference or subject">
        <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
    </form>
</div>
<div class="app-card table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Reference</th><th>Subject</th><th>Type</th><th>Status</th><th>Submitted</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($gds as $gd): ?>
                <tr>
                    <td><?= e($gd['reference_no']) ?></td>
                    <td><?= e($gd['subject']) ?></td>
                    <td><?= e(ucwords(str_replace('_', ' ', $gd['gd_type']))) ?></td>
                    <td><?= gd_status_badge($gd['status']) ?></td>
                    <td><?= e(date('M d, Y', strtotime($gd['created_at']))) ?></td>
                    <td><a class="btn btn-sm btn-primary" href="<?= e(app_url('citizen/download_gd.php?id=' . $gd['id'])) ?>">Details</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$gds): ?>
                <tr><td colspan="6" class="text-center text-secondary py-4">No matching GD found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
