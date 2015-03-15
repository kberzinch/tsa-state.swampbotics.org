<?php
namespace TSAState;

require "../db.class.php";

header('Content-Type: application/json');

// OUTPUT PAGE
$db = new Db();
$numqualifying = $db->query('SELECT value FROM settings');
$teams = $db->query('SELECT * FROM scores ORDER BY (program_score+driver_score), program_score DESC LIMIT ?', 'i', $numqualifying[0]['value']);
echo '{"data" : '.json_encode($teams).'}';
