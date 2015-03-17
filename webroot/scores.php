<?php
namespace TSAState;

require "../authenticate.php";
require "../db.class.php";

$db = new Db();
$teams = $db->query('SELECT *, (program_score+driver_score) FROM scores ORDER BY (program_score+driver_score), program_score DESC');
$data = '{data}';
for ($i = 0; $i < count($teams); $i++) {
    $data = str_replace('{data}', '<tr><td>'.($i + 1).'</td><td>'.$teams[$i]['vin'].'</td><td>'.$teams[$i]['program_skyrise_sections'].'</td><td>'.$teams[$i]['program_skyrise_cubes'].'</td><td>'.$teams[$i]['program_posts_owned'].'</td><td>'.$teams[$i]['program_posts_cubes'].'</td><td>'.$teams[$i]['program_floor_cubes'].'</td><td>'.$teams[$i]['program_score'].'</td><td>'.$teams[$i]['driver_skyrise_sections'].'</td><td>'.$teams[$i]['driver_skyrise_cubes'].'</td><td>'.$teams[$i]['driver_posts_owned'].'</td><td>'.$teams[$i]['driver_posts_cubes'].'</td><td>'.$teams[$i]['driver_floor_cubes'].'</td><td>'.$teams[$i]['driver_score'].'</td><td>'.$teams[$i]['(program_score+driver_score)'].'</td></tr>{data}', $data);
}

$data = str_replace('{data}', '', $data);
$output = file_get_contents("../scores.html");
$output = str_replace('{data}', $data, $output);
echo $output;
