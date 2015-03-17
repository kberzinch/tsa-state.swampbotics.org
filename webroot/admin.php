<?php
namespace TSAState;

require "../authenticate.php";
require "../db.class.php";
require "../teamname.php";

// HANDLING FORMS
/*
FORM REFERENCE:
skyrise-sections (4x)
skyrise-cubes (4x)
posts-owned (1x)
posts-cubes (2x)
floor-cubes (1x)
 */
$db = new Db();
$message = '';
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    switch($_POST['action']) {
        case 'report-score':
            $number = strtoupper($_POST['team']);
            if ($number === '') {
                $message = 'Please enter a team number.';
                break;
            }
            $name = getTeamName($number);
            $score=($_POST['skyrise-sections'] * 4) + ($_POST['skyrise-cubes'] * 4) +
                ($_POST['posts-cubes'] * 2) + $_POST['posts-owned'] + $_POST['floor-cubes'];
            if ($_POST['skyrise-sections'] > 7) {
                $message = 'Your score was not submitted because you reported '.$_POST['skyrise-sections'].' skyrise sections scored. Max expected was 7.';
                break;
            }
            if ($_POST['skyrise-cubes'] > 8) {
                $message = 'Your score was not submitted because you reported '.$_POST['skyrise-cubes'].' cubes scored on skyrise. Max expected was 8.';
                break;
            }
            if ($_POST['posts-owned'] > 10) {
                $message = 'Your score was not submitted because you reported '.$_POST['posts-owned'].' posts owned. Max expected was 10.';
                break;
            }
            if (($_POST['posts-cubes'] + $_POST['floor-cubes'] + $_POST['skyrise-cubes']) > 44) {
                $message = 'Your score was not submitted because you reported '.($_POST['posts-cubes'] + $_POST['floor-cubes'] + $_POST['skyrise-cubes']).' total cubes scored. Max expected was 44.';
                break;
            }
            switch($_POST['type']) {
                case 'program':
                    $friendly = "Programming Skills";
                    $db->query(
                        'INSERT INTO scores SET vin=?, name=?, program_skyrise_sections=?,
                        program_skyrise_cubes=?, program_posts_owned=?, program_posts_cubes=?,
                        program_floor_cubes=?, program_score=? ON DUPLICATE KEY UPDATE program_skyrise_sections=?,
                        program_skyrise_cubes=?, program_posts_owned=?, program_posts_cubes=?,
                        program_floor_cubes=?, program_score=?',
                        'ssiiiiiiiiiiii',
                        $number,
                        $name,
                        $_POST['skyrise-sections'],
                        $_POST['skyrise-cubes'],
                        $_POST['posts-owned'],
                        $_POST['posts-cubes'],
                        $_POST['floor-cubes'],
                        $score,
                        $_POST['skyrise-sections'],
                        $_POST['skyrise-cubes'],
                        $_POST['posts-owned'],
                        $_POST['posts-cubes'],
                        $_POST['floor-cubes'],
                        $score
                    );
                    break;
                case 'driver':
                    $friendly = "Robot Skills";
                    $db->query(
                        'INSERT INTO scores SET vin=?, name=?, driver_skyrise_sections=?,
                        driver_skyrise_cubes=?, driver_posts_owned=?, driver_posts_cubes=?,
                        driver_floor_cubes=?, driver_score=? ON DUPLICATE KEY UPDATE driver_skyrise_sections=?,
                        driver_skyrise_cubes=?, driver_posts_owned=?, driver_posts_cubes=?,
                        driver_floor_cubes=?, driver_score=?',
                        'ssiiiiiiiiiiii',
                        $number,
                        $name,
                        $_POST['skyrise-sections'],
                        $_POST['skyrise-cubes'],
                        $_POST['posts-owned'],
                        $_POST['posts-cubes'],
                        $_POST['floor-cubes'],
                        $score,
                        $_POST['skyrise-sections'],
                        $_POST['skyrise-cubes'],
                        $_POST['posts-owned'],
                        $_POST['posts-cubes'],
                        $_POST['floor-cubes'],
                        $score
                    );
                    break;
                default:
                    $friendly = "unknown";
                    break;
            }
            $message = 'Your '.$friendly.' score of '.$score.' for team '.$number.' ('.$name.') was successfully submitted.';
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
$output = file_get_contents("../admin.html");
if ($message !== "") {
    $output = str_replace("{alert}", file_get_contents("../alert.html"), $output);
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

echo $output;
