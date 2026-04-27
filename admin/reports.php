<?php
require_once __DIR__ . '/../includes/gd_helpers.php';
$user = require_login('admin');
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');

$stmt = db()->prepare('SELECT gd.*, u.name citizen_name FROM general_diaries gd JOIN users u ON u.id = gd.user_id WHERE DATE(gd.created_at) BETWEEN ? AND ? ORDER BY gd.created_at DESC');
$stmt->execute([$from, $to]);
$records = $stmt->fetchAll();

$pageTitle = 'Reports';
$activeNav = 'reports';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
    <h1 class="h2 mb-0">Reports</h1>
    <form class="row g-2" method="get">
        <div class="col"><input class="form-control" type="date" name="from" value="<?= e($from) ?>"></div>
        <div class="col"><input class="form-control" type="date" name="to" value="<?= e($to) ?>"></div>
        <div class="col-auto"><button class="btn btn-outline-primary">Apply</button></div>
        <div class="col-auto"><button type="button" class="btn btn-primary" onclick="window.print()">Print</button></div>
    </form>
</div>
<div class="app-card table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Reference</th><th>Citizen</th><th>Type</th><th>Subject</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach ($records as $gd): ?>
                <tr>
                    <td><?= e($gd['reference_no']) ?></td>
                    <td><?= e($gd['citizen_name']) ?></td>
                    <td><?= e(ucwords(str_replace('_', ' ', $gd['gd_type']))) ?></td>
                    <td><?= e($gd['subject']) ?></td>
                    <td><?= gd_status_badge($gd['status']) ?></td>
                    <td><?= e(date('M d, Y', strtotime($gd['created_at']))) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
