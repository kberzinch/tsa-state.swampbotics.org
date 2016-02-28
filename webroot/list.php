<?php
namespace TSAState;

require "../php/authenticate.php";
require "../php/db.class.php";

header('Content-Type: text/plain');

$db = new Db();
$numqualifying = $db->query('SELECT value FROM settings WHERE setting=0');
$teams = $db->query('SELECT vin FROM scores ORDER BY (program_score+driver_score) DESC, program_score DESC LIMIT ?', 'i', $numqualifying[0]['value']);
for ($i = 0; $i < count($teams); $i++) {
    echo $teams[$i]['vin']."\n";
}
