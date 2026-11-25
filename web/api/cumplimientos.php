<?php
require __DIR__.'/_common.php';
run_api(function(PDO $db,string $method): void {
    $uid=user_id($db);
    $fk=col($db,'cumplimientos',['id_habito','idHabito']); $fechaCol=col($db,'cumplimientos',['fecha']);
    $done=col($db,'cumplimientos',['completado'],false);
    if($method==='GET') {
        $id=checked_id($_GET['habito_id']??null,'hábito');
        if(!owns_habit($db,$id,$uid)) fail('Hábito no encontrado.',404);
        $sql='SELECT '.ident($fechaCol).' AS fecha FROM `cumplimientos` WHERE '.ident($fk).'=?';
        if($done)$sql.=' AND '.ident($done).'=1';
        $rows=query_all($db,$sql.' ORDER BY '.ident($fechaCol).' DESC',[$id]);
        send_json(array_column($rows,'fecha'));
    }
    if(!in_array($method,['POST','DELETE'],true)) fail('Método no permitido.',405);
    $data=input_json(); $id=checked_id($data['habito_id']??null,'hábito');
    if(!owns_habit($db,$id,$uid)) fail('Hábito no encontrado.',404);
    $date=validated_date($data['fecha']??date('Y-m-d'));
    if($method==='DELETE') {
        $db->prepare('DELETE FROM `cumplimientos` WHERE '.ident($fk).'=? AND '.ident($fechaCol).'=?')->execute([$id,$date]);
        send_json(['habito_id'=>$id,'fecha'=>$date],200,'Cumplimiento desmarcado.');
    }
    $found=query_all($db,'SELECT '.ident($fk).' FROM `cumplimientos` WHERE '.ident($fk).'=? AND '.ident($fechaCol).'=? LIMIT 1',[$id,$date]);
    if($found) {
        if($done)$db->prepare('UPDATE `cumplimientos` SET '.ident($done).'=1 WHERE '.ident($fk).'=? AND '.ident($fechaCol).'=?')->execute([$id,$date]);
        send_json(['habito_id'=>$id,'fecha'=>$date],200,'El cumplimiento ya estaba registrado.');
    }
    $cols=[$fk=>$id,$fechaCol=>$date];
    if($done)$cols[$done]=1;
    $db->prepare('INSERT INTO `cumplimientos` ('.implode(',',array_map('ident',array_keys($cols))).') VALUES ('.implode(',',array_fill(0,count($cols),'?')).')')->execute(array_values($cols));
    send_json(['habito_id'=>$id,'fecha'=>$date],201,'Cumplimiento registrado.');
});
