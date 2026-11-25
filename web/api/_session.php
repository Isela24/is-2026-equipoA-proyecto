<?php
/** Sesiones de RITMO: cookie HttpOnly y controles básicos contra fijación/CSRF. */
declare(strict_types=1);

function start_ritmo_session(): void {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_name('RITMO_SESSION');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/', 'secure' => $secure,
        'httponly' => true, 'samesite' => 'Lax'
    ]);
    session_start();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

function is_logged_in(): bool {
    return isset($_SESSION['ritmo_uid']) && is_int($_SESSION['ritmo_uid']) && $_SESSION['ritmo_uid'] > 0;
}

function require_login_page(): void {
    start_ritmo_session();
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function csrf_valid(): bool {
    $token = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    return isset($_SESSION['csrf']) && $token !== '' && hash_equals((string)$_SESSION['csrf'], $token);
}

function rotate_after_login(int $userId, string $name, string $email): void {
    session_regenerate_id(true);
    $_SESSION['ritmo_uid'] = $userId;
    $_SESSION['ritmo_name'] = $name;
    $_SESSION['ritmo_email'] = $email;
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
    unset($_SESSION['login_failures'], $_SESSION['login_block_until']);
}

function destroy_ritmo_session(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $cookie = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 3600, 'path' => $cookie['path'],
            'domain' => $cookie['domain'], 'secure' => $cookie['secure'],
            'httponly' => $cookie['httponly'], 'samesite' => $cookie['samesite'] ?? 'Lax'
        ]);
    }
    session_destroy();
}
