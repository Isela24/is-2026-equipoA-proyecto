<?php
require __DIR__ . '/_common.php';
run_api(function(PDO $db, string $method): void {
    if ($method !== 'GET') fail('Método no permitido.',405);
    $uid=user_id($db);
    send_json([
        'usuario'=>['id'=>$uid,'nombre'=>(string)($_SESSION['ritmo_name'] ?? 'Usuario'),'correo'=>(string)($_SESSION['ritmo_email'] ?? '')],
        'csrf'=>$_SESSION['csrf'],
        'categorias'=>category_list($db),
        'etiquetas'=>tag_list($db),
        'habitos'=>habit_list($db,$uid)
    ]);
});
