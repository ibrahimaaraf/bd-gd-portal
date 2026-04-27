<?php
require_once __DIR__ . '/../includes/auth.php';
if (current_user()) {
    redirect(dashboard_path(current_user()['role']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM users WHERE email = ? AND status = "active" LIMIT 1');
    $stmt->execute([$email]);
    $foundUser = $stmt->fetch();

    if ($foundUser && password_verify($password, $foundUser['password_hash'])) {
        login_user($foundUser);
        redirect(dashboard_path($foundUser['role']));
    }
    flash('error', 'Invalid email or password.');
}

$pageTitle = 'Login';
$activeNav = 'login';
require __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-5">
        <div class="app-card">
            <h1 class="h3 mb-3">Login</h1>
            <form method="post" class="vstack gap-3">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div>
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email" required>
                </div>
                <div>
                    <label class="form-label">Password</label>
                    <input class="form-control" type="password" name="password" required>
                </div>
                <button class="btn btn-primary btn-lg">Login</button>
                <p class="small text-secondary mb-0">Demo accounts after SQL import: admin@gd.test, police@gd.test, citizen@gd.test. Password: password</p>
            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
