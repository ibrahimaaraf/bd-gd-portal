<?php
require_once __DIR__ . '/../includes/gd_helpers.php';
$user = require_login('police');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $gdId = (int) ($_POST['gd_id'] ?? 0) ?: null;
    if ($name === '') {
        flash('error', 'POI name is required.');
    } else {
        $stmt = db()->prepare('INSERT INTO poi_records (gd_id, name, phone, address, notes, created_by) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$gdId, $name, $phone, $address, $notes, $user['id']]);
        flash('success', 'POI record added.');
        redirect('police/poi_tracker.php');
    }
}

$pois = db()->query('SELECT p.*, gd.reference_no FROM poi_records p LEFT JOIN general_diaries gd ON gd.id = p.gd_id ORDER BY p.created_at DESC')->fetchAll();
$gds = db()->query('SELECT id, reference_no, subject FROM general_diaries ORDER BY created_at DESC LIMIT 100')->fetchAll();

$pageTitle = 'POI Tracker';
$activeNav = 'poi';
require __DIR__ . '/../includes/header.php';
?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="app-card">
            <h1 class="h4 mb-3">Add Person of Interest</h1>
            <form method="post" class="vstack gap-3">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input class="form-control" name="name" placeholder="Name" required>
                <input class="form-control" name="phone" placeholder="Phone">
                <textarea class="form-control" name="address" rows="2" placeholder="Address"></textarea>
                <select class="form-select" name="gd_id">
                    <option value="">Link GD record</option>
                    <?php foreach ($gds as $gd): ?>
                        <option value="<?= e((string) $gd['id']) ?>"><?= e($gd['reference_no'] . ' - ' . $gd['subject']) ?></option>
                    <?php endforeach; ?>
                </select>
                <textarea class="form-control" name="notes" rows="4" placeholder="Notes"></textarea>
                <button class="btn btn-primary">Save POI</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="app-card table-card">
            <h2 class="h5 mb-3">POI records</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Name</th><th>Phone</th><th>GD</th><th>Notes</th><th>Date</th></tr></thead>
                    <tbody>
                    <?php foreach ($pois as $poi): ?>
                        <tr>
                            <td><?= e($poi['name']) ?></td>
                            <td><?= e($poi['phone']) ?></td>
                            <td><?= e($poi['reference_no'] ?? '-') ?></td>
                            <td><?= e($poi['notes']) ?></td>
                            <td><?= e(date('M d, Y', strtotime($poi['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
