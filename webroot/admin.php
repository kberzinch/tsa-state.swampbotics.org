<?php
namespace TSAState;

require "../db.class.php";

// AUTHENTICATION
$username = 'admin';
$password = 'cats'; // obviously this will be changed to something more secure.
$fail = 'You are not authorized to access this page. <a href="?retry">Retry login</a>';

if (!isset($_SERVER['PHP_AUTH_USER']) or (isset($_GET['retry']) and $_SERVER['PHP_AUTH_USER'] !== $username and $_SERVER['PHP_AUTH_PW'] !== $password)) {
    header('WWW-Authenticate: Basic realm="Tournament staff only"');
    header('HTTP/1.0 401 Unauthorized');
    echo $fail;
    exit;
}
if ($_SERVER['PHP_AUTH_USER'] !== $username) {
    echo $fail;
    exit;
}
if ($_SERVER['PHP_AUTH_PW'] !== $password) {
    echo $fail;
    exit;
}

// HANDLING FORMS
$db = new Db();
$message = '';
if($_SERVER['REQUEST_METHOD'] == "POST") {
	switch($_POST['action']) {
		case 'report-score':
			switch($_POST['type']) {
				case 'program':
				$friendly = "Programming Skills";
				$db->query('INSERT INTO scores SET vin=(?), program_score=(?) ON DUPLICATE KEY UPDATE program_score=(?)', 'sii', $_POST['team'], $_POST['score'], $_POST['score']);
				break;
				case 'driver':
				$friendly = "Robot Skills";
				$db->query('INSERT INTO scores SET vin=(?), driver_score=(?) ON DUPLICATE KEY UPDATE driver_score=(?)', 'sii', $_POST['team'], $_POST['score'], $_POST['score']);
				break;
				default:
				$friendly = "unknown";
				break;
			}
			$message = 'Your '.$friendly.' score of '.$_POST['score'].' for team '.$_POST['team'].' was successfully submitted.';
			break;
		case 'update-numqualifying':
			$db->query('UPDATE settings SET value=(?)', 'i', $_POST['number']);
			$message = 'Number of qualifying teams successfully updated to '.$_POST['number'].'.';
			break;
		default:
			$message = 'Not a valid action: '.$_POST['action'];
				break;
	}
}

// OUTPUT PAGE
$output = file_get_contents("../admin.html");
if($message !== "") {
	$output = str_replace("{alert}", file_get_contents("../alert.html"), $output);
} else {
	$output = str_replace("{alert}", '', $output);
}
$teams = $db->query('SELECT value FROM settings');
$output = str_replace("{teams}", $teams[0]["value"], $output);
$output = str_replace("{message}", $message, $output);
echo $output;