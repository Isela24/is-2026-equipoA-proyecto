<?php
require __DIR__.'/_common.php';
run_api(function(PDO $db,string $method): void {
    if($method!=='GET') fail('Método no permitido.',405);
    $uid=user_id($db);$id=checked_id($_GET['habito_id']??null,'hábito');
    foreach(habit_list($db,$uid) as $habit) if($habit['id']===$id) send_json(['habito_id'=>$id,'racha'=>streak($habit['completed'])]);
    fail('Hábito no encontrado.',404);
});
