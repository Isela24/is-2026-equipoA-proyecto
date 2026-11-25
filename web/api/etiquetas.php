<?php
require __DIR__.'/_common.php';
run_api(function(PDO $db,string $method): void {
    if($method==='GET') send_json(tag_list($db));
    if($method!=='POST') fail('Método no permitido.',405);
    $data=input_json(); $nombre=validated_name($data['nombre']??'',60);
    $id=create_tag($db,$nombre);
    send_json(['id'=>$id,'nombre'=>$nombre],201,'Etiqueta disponible.');
});
