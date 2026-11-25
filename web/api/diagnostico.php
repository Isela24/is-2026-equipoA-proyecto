<?php
require __DIR__.'/_common.php';
run_api(function(PDO $db,string $method): void {
    if($method!=='GET') fail('Método no permitido.',405);
    if(!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1','::1'], true)) fail('Diagnóstico disponible solo localmente.',403);
    $out=[];
    foreach(['usuarios','categorias','etiquetas','habitos','cumplimientos','habitoetiquetas'] as $t) $out[$t]=columns($db,$t);
    send_json(['motor'=>$db->getAttribute(PDO::ATTR_SERVER_VERSION),'base'=>'gestorhabitos','columnas'=>$out]);
});
