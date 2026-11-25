<?php
/** Autenticación contra la tabla Usuarios del schema.sql real de GestorHabitos. */
declare(strict_types=1);
require __DIR__ . '/_common.php';

function auth_error(string $msg, int $status = 400): never { fail($msg, $status); }

function valid_email(mixed $value): string {
    $email = strtolower(trim((string)$value));
    if (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        auth_error('Ingresa un correo electrónico válido.');
    }
    return $email;
}

function valid_password(mixed $value): string {
    if (!is_string($value) || strlen($value) < 10 || strlen($value) > 72) {
        auth_error('La contraseña debe tener entre 10 y 72 bytes.');
    }
    return $value;
}

try {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if ($method === 'GET') {
        send_json([
            'csrf' => $_SESSION['csrf'],
            'autenticado' => is_logged_in(),
            'usuario' => is_logged_in()
                ? ['nombre' => $_SESSION['ritmo_name'] ?? '', 'correo' => $_SESSION['ritmo_email'] ?? '']
                : null
        ]);
    }

    if ($method !== 'POST') auth_error('Método no permitido.', 405);
    if (!csrf_valid()) auth_error('Sesión vencida. Actualiza la página y vuelve a intentarlo.', 403);

    $data = input_json();
    $action = (string)($data['accion'] ?? '');
    if ($action === 'salir') {
        destroy_ritmo_session();
        send_json(null, 200, 'Sesión cerrada.');
    }
    if (!in_array($action, ['ingresar', 'registrar'], true)) auth_error('Acción no permitida.');
    if (is_logged_in()) auth_error('Ya tienes una sesión iniciada.', 409);

    $email = valid_email($data['correo'] ?? '');
    $db = db();

    if ($action === 'registrar') {
        $name = validated_name($data['nombre'] ?? '', 100);
        $password = valid_password($data['contrasena'] ?? null);
        if ($password !== ($data['confirmar'] ?? null)) auth_error('Las contraseñas no coinciden.');

        $existing = query_all($db, 'SELECT id_usuario FROM Usuarios WHERE correo = ? LIMIT 1', [$email]);
        if ($existing) auth_error('Ya existe una cuenta con ese correo.', 409);

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare('INSERT INTO Usuarios (nombre, correo, password_hash, activo) VALUES (?, ?, ?, 1)');
        $stmt->execute([$name, $email, $hash]);
        $uid = (int)$db->lastInsertId();

        rotate_after_login($uid, $name, $email);
        send_json(['usuario' => ['id' => $uid, 'nombre' => $name, 'correo' => $email]], 201, 'Cuenta creada.');
    }

    $now = time();
    if ($now < (int)($_SESSION['login_block_until'] ?? 0)) {
        auth_error('Demasiados intentos. Espera unos minutos antes de volver a probar.', 429);
    }

    $password = (string)($data['contrasena'] ?? '');
    $rows = query_all(
        $db,
        'SELECT id_usuario, nombre, correo, password_hash, activo FROM Usuarios WHERE correo = ? LIMIT 1',
        [$email]
    );
    $account = $rows[0] ?? null;
    if (!$account || !password_verify($password, (string)$account['password_hash'])) {
        $_SESSION['login_failures'] = (int)($_SESSION['login_failures'] ?? 0) + 1;
        if ($_SESSION['login_failures'] >= 5) {
            $_SESSION['login_block_until'] = $now + 300;
            $_SESSION['login_failures'] = 0;
        }
        auth_error('Correo o contraseña incorrectos.', 401);
    }
    if ((int)$account['activo'] !== 1) auth_error('Esta cuenta está desactivada.', 403);

    if (password_needs_rehash((string)$account['password_hash'], PASSWORD_DEFAULT)) {
        $db->prepare('UPDATE Usuarios SET password_hash = ? WHERE id_usuario = ?')
            ->execute([password_hash($password, PASSWORD_DEFAULT), (int)$account['id_usuario']]);
    }

    rotate_after_login((int)$account['id_usuario'], (string)$account['nombre'], (string)$account['correo']);
    send_json([
        'usuario' => [
            'id' => (int)$account['id_usuario'],
            'nombre' => (string)$account['nombre'],
            'correo' => (string)$account['correo']
        ]
    ], 200, 'Sesión iniciada.');
} catch (PDOException $e) {
    error_log('[RITMO AUTH DB] ' . $e->getMessage());
    if ((string)$e->getCode() === '23000') fail('Ya existe una cuenta con ese correo.', 409);
    fail('No se pudo consultar MariaDB. Revisa config/database.php y la tabla Usuarios.', 500);
} catch (Throwable $e) {
    error_log('[RITMO AUTH] ' . $e->getMessage());
    fail($e instanceof DomainException ? $e->getMessage() : 'Error del servidor. Consulta el registro de PHP.', $e instanceof DomainException ? 422 : 500);
}
