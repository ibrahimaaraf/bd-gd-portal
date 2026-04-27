<?php
require_once __DIR__ . '/../includes/gd_helpers.php';
$user = require_login('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $userId = (int) ($_POST['user_id'] ?? 0);
    $role = $_POST['role'] ?? '';
    $status = $_POST['status'] ?? '';
    if ($userId === (int) $user['id']) {
        flash('error', 'You cannot change your own account here.');
    } elseif (!in_array($role, ['citizen', 'police', 'admin'], true) || !in_array($status, ['active', 'inactive'], true)) {
        flash('error', 'Invalid role or status.');
    } else {
        $stmt = db()->prepare('UPDATE users SET role = ?, status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$role, $status, $userId]);
        flash('success', 'User updated.');
    }
    redirect('admin/manage_users.php');
}

$users = db()->query('SELECT id, name, email, phone, role, status, created_at FROM users ORDER BY created_at DESC')->fetchAll();
$pageTitle = 'Manage Users';
$activeNav = 'users';
require __DIR__ . '/../includes/header.php';
?>
<h1 class="h2 mb-4">Manage Users</h1>
<div class="app-card table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Joined</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($users as $row): ?>
                <tr>
                    <td><?= e($row['name']) ?></td>
                    <td><?= e($row['email']) ?></td>
                    <td><?= e($row['phone']) ?></td>
                    <td class="text-capitalize"><?= e($row['role']) ?></td>
                    <td class="text-capitalize"><?= e($row['status']) ?></td>
                    <td><?= e(date('M d, Y', strtotime($row['created_at']))) ?></td>
                    <td>
                        <?php if ((int) $row['id'] !== (int) $user['id']): ?>
                            <form class="d-flex flex-column flex-md-row gap-2" method="post">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="user_id" value="<?= e((string) $row['id']) ?>">
                                <select class="form-select form-select-sm" name="role">
                                    <?php foreach (['citizen', 'police', 'admin'] as $role): ?>
                                        <option value="<?= e($role) ?>" <?= $row['role'] === $role ? 'selected' : '' ?>><?= e(ucfirst($role)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select class="form-select form-select-sm" name="status">
                                    <?php foreach (['active', 'inactive'] as $status): ?>
                                        <option value="<?= e($status) ?>" <?= $row['status'] === $status ? 'selected' : '' ?>><?= e(ucfirst($status)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-sm btn-primary">Save</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
