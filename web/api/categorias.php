<?php
require __DIR__.'/_common.php';
run_api(function(PDO $db,string $method): void {
    if($method==='GET') send_json(category_list($db));
    if($method!=='POST') fail('Método no permitido.',405);
    $payload=input_json(); $nombre=validated_name($payload['nombre']??'',100);
    $nc=col($db,'categorias',['nombre']);
    $found=query_all($db,'SELECT '.ident(idcol($db,'categorias')).' AS id FROM `categorias` WHERE '.ident($nc).'=? LIMIT 1',[$nombre]);
    if($found) send_json(['id'=>(int)$found[0]['id'],'nombre'=>$nombre],200,'La categoría ya existía.');
    $db->prepare('INSERT INTO `categorias` ('.ident($nc).') VALUES (?)')->execute([$nombre]);
    send_json(['id'=>(int)$db->lastInsertId(),'nombre'=>$nombre],201,'Categoría registrada.');
});
