<?php
require __DIR__.'/_common.php';
run_api(function(PDO $db,string $method): void {
    if($method!=='GET') fail('Método no permitido.',405);
    $habits=habit_list($db,user_id($db));
    $days=[]; $total=0; $best=0;
    for($i=6;$i>=0;$i--) $days[(new DateTimeImmutable('today'))->modify("-$i day")->format('Y-m-d')]=0;
    foreach($habits as $h) {
        $total+=count($h['completed']);$best=max($best,streak($h['completed']));
        foreach($h['completed'] as $day) if(isset($days[$day]))$days[$day]++;
    }
    $possible=count($habits)*7;
    send_json(['habitos'=>count($habits),'total_cumplimientos'=>$total,'mejor_racha'=>$best,'consistencia_semanal'=>$possible?round(array_sum($days)/$possible*100):0,'por_dia'=>$days]);
});
