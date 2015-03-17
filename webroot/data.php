<?php
namespace TSAState;

require "../db.class.php";

header('Content-Type: application/json');

$db = new Db();
$numqualifying = $db->query('SELECT value FROM settings WHERE setting=0');
$teams = $db->query('SELECT vin, program_score, driver_score FROM scores ORDER BY (program_score+driver_score) DESC, program_score DESC LIMIT ?', 'i', $numqualifying[0]['value']);
for ($i = 0; $i < count($teams); $i++) {
    if ($teams[$i]['program_score'] === null) {
        $teams[$i]['program_score'] = '';
    }
    if ($teams[$i]['driver_score'] === null) {
        $teams[$i]['driver_score'] = '';
    }
}
echo json_encode($teams);
