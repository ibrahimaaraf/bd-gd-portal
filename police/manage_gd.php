<?php
require_once __DIR__ . '/../includes/gd_helpers.php';
$user = require_login('police');
$status = $_GET['status'] ?? '';
$search = trim($_GET['q'] ?? '');

$sql = 'SELECT gd.*, u.name citizen_name FROM general_diaries gd JOIN users u ON u.id = gd.user_id WHERE 1=1';
$params = [];
if (in_array($status, GD_STATUSES, true)) {
    $sql .= ' AND gd.status = ?';
    $params[] = $status;
}
if ($search !== '') {
    $sql .= ' AND (gd.reference_no LIKE ? OR gd.subject LIKE ? OR u.name LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
$sql .= ' ORDER BY gd.created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$gds = $stmt->fetchAll();

$pageTitle = 'Manage GD';
$activeNav = 'manage';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
    <h1 class="h2 mb-0">Manage GD</h1>
    <form class="row g-2" method="get">
        <div class="col-sm"><input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Reference, subject, citizen"></div>
        <div class="col-sm">
            <select class="form-select" name="status">
                <option value="">All statuses</option>
                <?php foreach (GD_STATUSES as $item): ?>
                    <option value="<?= e($item) ?>" <?= $status === $item ? 'selected' : '' ?>><?= e(ucwords(str_replace('_', ' ', $item))) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-auto"><button class="btn btn-outline-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button></div>
    </form>
</div>
<div class="app-card table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Reference</th><th>Citizen</th><th>Subject</th><th>Type</th><th>Status</th><th>Submitted</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($gds as $gd): ?>
                <tr>
                    <td><?= e($gd['reference_no']) ?></td>
                    <td><?= e($gd['citizen_name']) ?></td>
                    <td><?= e($gd['subject']) ?></td>
                    <td><?= e(ucwords(str_replace('_', ' ', $gd['gd_type']))) ?></td>
                    <td><?= gd_status_badge($gd['status']) ?></td>
                    <td><?= e(date('M d, Y', strtotime($gd['created_at']))) ?></td>
                    <td><a class="btn btn-sm btn-primary" href="<?= e(app_url('police/view_gd.php?id=' . $gd['id'])) ?>">Review</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$gds): ?>
                <tr><td colspan="7" class="text-center text-secondary py-4">No GD records found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
