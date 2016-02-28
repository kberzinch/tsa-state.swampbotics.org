<?php
namespace TSAState;

require_once "../php/db.class.php";

$output = file_get_contents("../html/index.html");
$db = new Db();
$result = $db->query('SELECT value FROM settings WHERE setting=0');
$output = str_replace("{numqualifying}", $result[0]["value"], $output);
$result = $db->query('SELECT value FROM settings WHERE setting=1');
if ($result[0]["value"] == 1) {
    $output = str_replace("{banner}", file_get_contents("../html/banner.html"), $output);
    $result = $db->query('SELECT value FROM settings WHERE setting=2');
    $output = str_replace("{banner-url}", $result[0]["value"], $output);
} else {
    $output = str_replace("{banner}", '', $output);
}
$output = str_replace("{state}", $db->query("SELECT value FROM settings WHERE setting=3")[0]['value'], $output);
$output = str_replace("{footer}", file_get_contents("../html/footer.html"), $output);
echo $output;
