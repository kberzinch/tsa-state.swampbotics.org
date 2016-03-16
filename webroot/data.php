<?php
namespace TSAState;

require_once "../php/db.class.php";

header('Content-Type: application/json');

$db = new Db();
$numqualifying = $db->query('SELECT value FROM settings WHERE setting=0');
$teams = $db->query('SELECT vin, program_score, driver_score FROM scores ORDER BY (program_score+driver_score) DESC, program_score DESC, (program_bonus_low+program_bonus_high) DESC, (driver_bonus_low+driver_bonus_high) DESC LIMIT ?', 'i', $numqualifying[0]['value']);
for ($i = 0; $i < count($teams); $i++) {
    if ($teams[$i]['program_score'] === null) {
        $teams[$i]['program_score'] = '';
    }
    if ($teams[$i]['driver_score'] === null) {
        $teams[$i]['driver_score'] = '';
    }
}
echo json_encode($teams);
