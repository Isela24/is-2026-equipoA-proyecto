<?php
/** RITMO: API autenticada, cada solicitud opera sobre el usuario de su sesión. */
declare(strict_types=1);
require_once __DIR__ . '/_session.php';
start_ritmo_session();
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function send_json(mixed $data = null, int $status = 200, string $message = 'OK'): never {
    http_response_code($status);
    echo json_encode(['success' => $status < 400, 'message' => $message, 'data' => $data], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
function fail(string $message, int $status = 400): never { send_json(null, $status, $message); }
function run_api(callable $handler): void {
    try {
        if (!is_logged_in()) fail('Inicia sesión para continuar.', 401);
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'], true)) fail('Método no permitido.', 405);
        if ($method !== 'GET' && !csrf_valid()) fail('Sesión vencida. Actualiza la página antes de continuar.', 403);
        $database = db();
        // La sesión no concede acceso si la cuenta fue eliminada después de iniciar sesión.
        $uid = (int) $_SESSION['ritmo_uid'];
        $exists = query_all($database,
            'SELECT id_usuario FROM Usuarios WHERE id_usuario = ? AND activo = 1 LIMIT 1',
            [$uid]
        );
        if (!$exists) {
            destroy_ritmo_session();
            fail('Tu sesión ya no está disponible. Vuelve a iniciar sesión.', 401);
        }
        $handler($database, $method);
    } catch (PDOException $exception) {
        error_log('[RITMO DB] ' . $exception->getMessage());
        fail('No se pudo consultar la base de datos. Revisa la configuración y la estructura de Usuarios.', 500);
    } catch (Throwable $exception) {
        error_log('[RITMO] ' . $exception->getMessage());
        fail($exception instanceof DomainException ? $exception->getMessage() : 'Error interno. Consulta el registro de PHP.', $exception instanceof DomainException ? 422 : 500);
    }
}
function db(): PDO {
    $file = __DIR__ . '/../config/database.php';
    if (!file_exists($file)) throw new DomainException('Copia config/database.example.php como config/database.php y configura MariaDB.');
    $c = require $file;
    if (!extension_loaded('pdo_mysql')) throw new DomainException('Activa la extensión pdo_mysql en php.ini de XAMPP.');
    return new PDO(
        'mysql:host=' . $c['host'] . ';port=' . (int)$c['port'] . ';dbname=' . $c['database'] . ';charset=utf8mb4',
        $c['user'], $c['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
    );
}
function ident(string $name): string {
    if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $name)) throw new DomainException('Identificador de base de datos no permitido.');
    return '`' . $name . '`';
}
function columns(PDO $db, string $table): array {
    static $cache = [];
    $key = spl_object_id($db) . ':' . $table;
    if (!isset($cache[$key])) {
        $res = $db->query('SHOW COLUMNS FROM ' . ident($table))->fetchAll();
        $cache[$key] = array_column($res, 'Field');
    }
    return $cache[$key];
}
function col(PDO $db, string $table, array $alternatives, bool $required = true): ?string {
    $present = columns($db, $table);
    $normalized = fn(string $s): string => strtolower((string)preg_replace('/[^a-z0-9]/i', '', $s));
    foreach ($alternatives as $choice) {
        foreach ($present as $found) {
            if ($normalized($choice) === $normalized($found)) return $found;
        }
    }
    if ($required) throw new DomainException('Falta una columna compatible con [' . implode(', ', $alternatives) . '] en la tabla ' . $table . '. Columnas actuales: ' . implode(', ', $present));
    return null;
}
function idcol(PDO $db, string $table): string {
    return col($db, $table, match ($table) {
        'usuarios' => ['id_usuario', 'idUsuario'],
        'categorias' => ['id_categoria', 'idCategoria'],
        'etiquetas' => ['id_etiqueta', 'idEtiqueta'],
        'habitos' => ['id_habito', 'idHabito'],
        'cumplimientos' => ['id_cumplimiento', 'idCumplimiento'],
        default => ['id']
    });
}
function fkcol(PDO $db, string $table, string $entity): ?string {
    return col($db, $table, ['id_' . $entity, 'id' . ucfirst($entity)], false);
}
function input_json(): array {
    $raw = file_get_contents('php://input');
    if (strlen($raw) > 30000) fail('Solicitud demasiado grande.', 413);
    $data = json_decode($raw ?: '{}', true);
    if (!is_array($data)) fail('Se esperaba un objeto JSON.');
    return $data;
}
function checked_id(mixed $value, string $name = 'identificador'): int {
    $id = filter_var($value, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) fail('El ' . $name . ' debe ser positivo.');
    return (int)$id;
}
function text_length(string $value): int {
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : (int)preg_match_all('/./us', $value);
}
function validated_name(mixed $value, int $length = 150): string {
    $name = trim((string)$value);
    if ($name === '' || text_length($name) > $length) fail('El nombre es obligatorio y debe tener máximo ' . $length . ' caracteres.');
    return $name;
}
function validated_date(mixed $value): string {
    $value = (string)$value;
    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    if (!$d || $d->format('Y-m-d') !== $value) fail('Fecha inválida: usa AAAA-MM-DD.');
    return $value;
}
function query_all(PDO $db, string $query, array $params = []): array {
    $s = $db->prepare($query); $s->execute($params); return $s->fetchAll();
}
function user_id(PDO $db): int {
    if (!is_logged_in()) fail('Inicia sesión para continuar.', 401);
    return (int) $_SESSION['ritmo_uid'];
}
function owns_habit(PDO $db, int $id, int $uid): bool {
    $pk = idcol($db, 'habitos');
    $owner = col($db, 'habitos', ['id_usuario','idUsuario']);
    return (bool)query_all($db, 'SELECT ' . ident($pk) . ' FROM `habitos` WHERE ' . ident($pk) . '=? AND ' . ident($owner) . '=? LIMIT 1', [$id, $uid]);
}
function category_list(PDO $db): array {
    $pk = idcol($db, 'categorias'); $name = col($db, 'categorias', ['nombre']);
    $rows = query_all($db, 'SELECT ' . ident($pk) . ' AS id, ' . ident($name) . ' AS nombre FROM `categorias` ORDER BY ' . ident($name));
    return array_map(fn($r) => ['id'=>(int)$r['id'], 'nombre'=>(string)$r['nombre']], $rows);
}
function tag_list(PDO $db): array {
    $pk = idcol($db, 'etiquetas'); $name = col($db, 'etiquetas', ['nombre']);
    $rows = query_all($db, 'SELECT ' . ident($pk) . ' AS id, ' . ident($name) . ' AS nombre FROM `etiquetas` ORDER BY ' . ident($name));
    return array_map(fn($r) => ['id'=>(int)$r['id'], 'nombre'=>(string)$r['nombre']], $rows);
}
function create_tag(PDO $db, string $name): int {
    $name = validated_name($name, 60);
    $pk = idcol($db, 'etiquetas'); $nc = col($db, 'etiquetas', ['nombre']);
    $s = query_all($db, 'SELECT ' . ident($pk) . ' AS id FROM `etiquetas` WHERE ' . ident($nc) . ' = ? LIMIT 1', [$name]);
    if ($s) return (int)$s[0]['id'];
    $fields = [$nc => $name];
    if ($color = col($db, 'etiquetas', ['color'], false)) $fields[$color] = '#8572E6';
    $db->prepare('INSERT INTO `etiquetas` (' . implode(',', array_map('ident', array_keys($fields))) . ') VALUES (' . implode(',', array_fill(0, count($fields), '?')) . ')')->execute(array_values($fields));
    return (int)$db->lastInsertId();
}
function set_tags(PDO $db, int $habitId, array $names): void {
    $hfk = col($db, 'habitoetiquetas', ['id_habito','idHabito']);
    $tfk = col($db, 'habitoetiquetas', ['id_etiqueta','idEtiqueta']);
    $db->prepare('DELETE FROM `habitoetiquetas` WHERE ' . ident($hfk) . ' = ?')->execute([$habitId]);
    foreach (array_unique(array_filter(array_map('trim', $names))) as $name) {
        $tagId = create_tag($db, $name);
        $db->prepare('INSERT INTO `habitoetiquetas` (' . ident($hfk) . ', ' . ident($tfk) . ') VALUES (?,?)')->execute([$habitId, $tagId]);
    }
}
function habit_list(PDO $db, int $uid): array {
    $hid = idcol($db, 'habitos'); $name = col($db,'habitos',['nombre']); $owner = col($db,'habitos',['id_usuario','idUsuario']);
    $cat = col($db,'habitos',['id_categoria','idCategoria'],false);
    $color = col($db,'habitos',['color'],false);
    $created = col($db,'habitos',['fecha_creacion','fechaCreacion'],false);
    $cols = [ident($hid).' AS id',ident($name).' AS nombre'];
    $cols[] = $cat ? ident($cat).' AS categoria_id':'NULL AS categoria_id';
    $cols[] = $color ? ident($color).' AS color':'NULL AS color';
    $cols[] = $created ? ident($created).' AS fecha_creacion':'NULL AS fecha_creacion';
    $rows = query_all($db,'SELECT '.implode(', ',$cols).' FROM `habitos` WHERE '.ident($owner).' = ? ORDER BY '.ident($hid).' DESC',[$uid]);
    $categoryMap=[]; foreach(category_list($db) as $c) $categoryMap[$c['id']]=$c['nombre'];
    $result=[];
    foreach ($rows as $r) {
        $id=(int)$r['id']; $catid=$r['categoria_id'] === null ? null : (int)$r['categoria_id'];
        $result[$id]=['id'=>$id,'name'=>$r['nombre'],'categoryId'=>$catid,'category'=>$categoryMap[$catid]??'Sin categoría','color'=> $r['color']?:'#5d71f5','createdAt'=>$r['fecha_creacion'],'completed'=>[], 'tags'=>[]];
    }
    if (!$result) return [];
    // Dos consultas para los cumplimientos y las etiquetas de los hábitos del usuario.
    $fk=col($db,'cumplimientos',['id_habito','idHabito']); $date=col($db,'cumplimientos',['fecha']);
    $complete = col($db,'cumplimientos',['completado'],false);
    $sql='SELECT c.'.ident($fk).' AS id, c.'.ident($date).' AS fecha FROM `cumplimientos` c INNER JOIN `habitos` h ON c.'.ident($fk).' = h.'.ident($hid).' WHERE h.'.ident($owner).' = ?';
    if($complete) $sql.=' AND c.'.ident($complete).' = 1';
    foreach(query_all($db,$sql,[$uid]) as $r) { $id=(int)$r['id']; if(isset($result[$id])) $result[$id]['completed'][]=$r['fecha']; }
    $jf=col($db,'habitoetiquetas',['id_habito','idHabito']); $tf=col($db,'habitoetiquetas',['id_etiqueta','idEtiqueta']);
    $tpk=idcol($db,'etiquetas'); $tn=col($db,'etiquetas',['nombre']);
    $tags='SELECT j.'.ident($jf).' AS id,t.'.ident($tn).' AS nombre FROM `habitoetiquetas` j INNER JOIN `etiquetas` t ON j.'.ident($tf).' = t.'.ident($tpk).' INNER JOIN `habitos` h ON h.'.ident($hid).' = j.'.ident($jf).' WHERE h.'.ident($owner).' = ?';
    foreach(query_all($db,$tags,[$uid]) as $r) { $id=(int)$r['id']; if(isset($result[$id])) $result[$id]['tags'][]=$r['nombre']; }
    return array_values($result);
}
function streak(array $dates): int {
    $present=array_fill_keys($dates,true); $day=new DateTimeImmutable('today');
    if(!isset($present[$day->format('Y-m-d')])) $day=$day->modify('-1 day');
    $s=0;
    while($s < 36500 && isset($present[$day->format('Y-m-d')])) {++$s;$day=$day->modify('-1 day');}
    return $s;
}
