<?php
namespace TSAState;

require_once "../php/authenticate.php";
require_once "../php/db.class.php";

// HANDLING FORMS
/*
FORM REFERENCE:
balls-low (1x)
balls-high (5x)
bonus-low (2x)
bonus-high (10x)
94 balls total
10 bonus balls total
 */
$db = new Db();
$message = '';
$prefill_team = '';
$prefill_robotchecked = false;
$teams = $db->query('SELECT vin, program_score, driver_score FROM scores WHERE vin=(?)', 's', $_POST['team']);
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    switch ($_POST['action']) {
        case 'report-score':
            $number = strtoupper($_POST['team']);
            if ($number === '') {
                $message = 'Please enter a team number.';
                break;
            }
            $score = $_POST['balls-low'] + (5 * $_POST['balls-high']) + (2 * $_POST['bonus-low']) + (10 * $_POST['bonus-high']);
            if ($_POST['balls-low'] + $_POST['balls-high'] > 94) {
                $message = 'Your score was not submitted because you reported a total of '.($_POST['balls-low'] + $_POST['balls-high']).' balls scored, but there\'s only 94 total.';
                $prefill_team = $number;
                if ($_POST['type'] === 'driver') {
                    $prefill_robotchecked = true;
                }
                break;
            }
            if ($_POST['bonus-low'] + $_POST['bonus-high'] > 10) {
                $message = 'Your score was not submitted because you reported a total of '.($_POST['bonus-low'] + $_POST['bonus-high']).' bonus balls scored, but there\'s only 10 total.';
                $prefill_team = $number;
                if ($_POST['type'] === 'driver') {
                    $prefill_robotchecked = true;
                }
                break;
            }
            switch ($_POST['type']) {
                case 'program':
                    $friendly = "Programming Skills";
                    if ($teams[0]['program_score'] > $score) {
                        $message = 'Your score was not submitted because a higher score is already in the database.\n\n'.$friendly.' for team '.$_POST['team'].'\nPrevious score: '.$teams[0]['program_score'].'\nYour input: '.$score;
                        break;
                    }
                    $db->query(
                        'INSERT INTO scores SET vin=?, program_balls_low=?,
                        program_balls_high=?, program_bonus_low=?, program_bonus_high=?,
                        program_score=? ON DUPLICATE KEY UPDATE program_balls_low=?,
                        program_balls_high=?, program_bonus_low=?, program_bonus_high=?,
                        program_score=?',
                        'siiiiiiiiii',
                        $number,
                        $_POST['balls-low'],
                        $_POST['balls-high'],
                        $_POST['bonus-low'],
                        $_POST['bonus-high'],
                        $score,
                        $_POST['balls-low'],
                        $_POST['balls-high'],
                        $_POST['bonus-low'],
                        $_POST['bonus-high'],
                        $score
                    );
                    $prefill_team = $number;
                    $prefill_robotchecked = true;
                    break;
                case 'driver':
                    $friendly = "Robot Skills";
                    if ($teams[0]['driver_score'] > $score) {
                        $message = 'Your score was not submitted because a higher score is already in the database.\n\n'.$friendly.' for team '.$_POST['team'].'\nPrevious score: '.$teams[0]['program_score'].'\nYour input: '.$score;
                        break;
                    }
                    $db->query(
                        'INSERT INTO scores SET vin=?, driver_balls_low=?,
                        driver_balls_high=?, driver_bonus_low=?, driver_bonus_high=?,
                        driver_score=? ON DUPLICATE KEY UPDATE driver_balls_low=?,
                        driver_balls_high=?, driver_bonus_low=?, driver_bonus_high=?,
                        driver_score=?',
                        'siiiiiiiiii',
                        $number,
                        $_POST['balls-low'],
                        $_POST['balls-high'],
                        $_POST['bonus-low'],
                        $_POST['bonus-high'],
                        $score,
                        $_POST['balls-low'],
                        $_POST['balls-high'],
                        $_POST['bonus-low'],
                        $_POST['bonus-high'],
                        $score
                    );
                    break;
                default:
                    $friendly = "unknown";
                    break;
            }
            if ($message === "") {
                $message = 'Your '.$friendly.' score of '.$score.' for team '.$number.' was successfully submitted.';
            }
            break;
        case 'update-numqualifying':
            $db->query('UPDATE settings SET value=(?) WHERE setting=0', 'i', $_POST['number']);
            $message = 'Number of qualifying teams successfully updated to '.$_POST['number'].'.';
            break;
        case 'update-banner':
            $db->query('UPDATE settings SET value=(?) WHERE setting=2', 's', $_POST['banner-url']);
            $db->query('UPDATE settings SET value=(?) WHERE setting=1', 'i', $_POST['banner-on']);
            $message = 'Banner URL successfully set to '.$_POST['banner-url'].'.';
            if ($_POST['banner-on'] == 1) {
                $message .= ' Banner is now displayed. Refresh your displays!';
            } else {
                $message .= ' Banner is now hidden.';
            }
            break;
        default:
            $message = 'Not a valid action: '.$_POST['action'];
            var_dump($_POST);
            break;
    }
}

// OUTPUT PAGE
$output = file_get_contents("../html/admin.html");
if ($message !== "") {
    $output = str_replace("{alert}", file_get_contents("../html/alert.html"), $output);
} else {
    $output = str_replace("{alert}", '', $output);
}
$output = str_replace("{message}", $message, $output);

$result = $db->query('SELECT value FROM settings WHERE setting=0');
$output = str_replace("{teams}", $result[0]["value"], $output);

$result = $db->query('SELECT value FROM settings WHERE setting=1');
if ($result[0]["value"] == 1) {
    $output = str_replace("{banner-checked}", 'checked', $output);
} else {
    $output = str_replace("{banner-checked}", '', $output);
}

$result = $db->query('SELECT value FROM settings WHERE setting=2');
$output = str_replace("{banner-url}", $result[0]["value"], $output);

$output = str_replace("{team-number}", $prefill_team, $output);
if ($prefill_robotchecked) {
    $output = str_replace("{robot-checked}", 'checked', $output);
} else {
    $output = str_replace("{program-checked}", 'checked', $output);
}

$output = str_replace("{footer}", file_get_contents("../html/footer.html"), $output);

$output = str_replace("{state}", $db->query("SELECT value FROM settings WHERE setting=3")[0]['value'], $output);

echo $output;
