<?php
namespace TSAState;

require_once "../php/authenticate.php";
require_once "../php/db.class.php";

header('Content-type: text/csv');
header('Content-disposition: attachment;filename=scores.csv');

$db = new Db();
$teams = $db->query('SELECT *, (program_score+driver_score) FROM scores ORDER BY (program_score+driver_score) DESC, program_score DESC');
$output = "vin,pgl,pgh,pol,poh,pscore,dgl,dgh,dol,doh,dscore,total\n";
for ($i = 0; $i < count($teams); $i++) {
    $output .= $teams[$i]['vin'].','.$teams[$i]['program_balls_low'].','.$teams[$i]['program_balls_high'].','.$teams[$i]['program_bonus_low'].','.$teams[$i]['program_bonus_high'].','.$teams[$i]['program_score'].','.$teams[$i]['driver_balls_low'].','.$teams[$i]['driver_balls_high'].','.$teams[$i]['driver_bonus_low'].','.$teams[$i]['driver_bonus_high'].','.$teams[$i]['driver_score'].','.$teams[$i]['(program_score+driver_score)']."\n";
}

echo $output;
