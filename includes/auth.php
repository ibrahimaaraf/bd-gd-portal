<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    static $user = null;
    if ($user !== null && (int) $user['id'] === (int) $_SESSION['user_id']) {
        return $user;
    }

    $stmt = db()->prepare('SELECT id, name, email, phone, address, role, status, created_at FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch() ?: null;

    if (!$user || $user['status'] !== 'active') {
        logout_user();
        return null;
    }

    return $user;
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_role'] = $user['role'];
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function require_login(?string $role = null): array
{
    $user = current_user();
    if (!$user) {
        flash('error', 'Please login to continue.');
        redirect('auth/login.php');
    }

    if ($role !== null && $user['role'] !== $role) {
        flash('error', 'You are not authorized to access that page.');
        redirect(match ($user['role']) {
            'admin' => 'admin/dashboard.php',
            'police' => 'police/dashboard.php',
            default => 'citizen/dashboard.php',
        });
    }

    return $user;
}

function require_any_role(array $roles): array
{
    $user = current_user();
    if (!$user) {
        flash('error', 'Please login to continue.');
        redirect('auth/login.php');
    }

    if (!in_array($user['role'], $roles, true)) {
        flash('error', 'You are not authorized to access that page.');
        redirect('index.php');
    }

    return $user;
}

function dashboard_path(string $role): string
{
    return match ($role) {
        'admin' => 'admin/dashboard.php',
        'police' => 'police/dashboard.php',
        default => 'citizen/dashboard.php',
    };
}
