<?php
namespace TSAState;

require "../db.class.php";

// OUTPUT PAGE
$output = file_get_contents("../index.html");
$db = new Db();
$teams = $db->query('SELECT *, (program_score+driver_score) FROM scores ORDER BY (program_score+driver_score) DESC LIMIT 32');
for($i = 0; $i < count($teams); $i++) {
	$output = str_replace("{row}", file_get_contents("../row.html"), $output);
	$output = str_replace("{rank}", $i + 1, $output);
	$output = str_replace("{vin}", $teams[$i]['vin'], $output);
	$output = str_replace("{program_score}", $teams[$i]['program_score'], $output);
	$output = str_replace("{robot_score}", $teams[$i]['driver_score'], $output);
	$output = str_replace("{sum}", $teams[$i]['(program_score+driver_score)'], $output);
}
$output = str_replace("{row}", '', $output);
echo $output;