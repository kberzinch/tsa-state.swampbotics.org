<?php
namespace TSAState;

require "../db.class.php";

$output = file_get_contents("../index.html");
$db = new Db();
$result = $db->query('SELECT value FROM settings WHERE setting=1');
if ($result[0]["value"] == 1) {
    $output = str_replace("{banner}", file_get_contents("../banner.html"), $output);
    $result = $db->query('SELECT value FROM settings WHERE setting=2');
    $output = str_replace("{banner-url}", $result[0]["value"], $output);
} else {
    $output = str_replace("{banner}", '', $output);
}
echo $output;
