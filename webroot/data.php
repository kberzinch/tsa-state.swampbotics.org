<?php
//namespace TSAState;

//require "../db.class.php";

header('Content-Type: application/json');

echo '[

{
"vin": "12345",
"name": "",
"program_score": 35,
"driver_score": null
},
{
"vin": "",
"name": "",
"program_score": 0,
"driver_score": null
},
{
"vin": "1234",
"name": "",
"program_score": null,
"driver_score": 34
},
{
"vin": "2105A",
"name": "Swampbotics A",
"program_score": 5,
"driver_score": 6
},
{
"vin": "2105B",
"name": "Swampbotics B",
"program_score": 3,
"driver_score": 20
},
{
"vin": "123",
"name": "",
"program_score": 0,
"driver_score": 100
}

]';

// OUTPUT PAGE
//$db = new Db();
//$numqualifying = $db->query('SELECT value FROM settings');
//$teams = $db->query('SELECT * FROM scores ORDER BY (program_score+driver_score), program_score DESC LIMIT ?', 'i', $numqualifying[0]['value']);
//echo '{"data" : '.json_encode($teams).'}';
