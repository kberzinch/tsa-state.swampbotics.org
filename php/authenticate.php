<?php
namespace TSAState;

$username = 'admin';
$password = 'swampbotics'; // obviously this will be changed to something more secure.
$fail = 'You are not authorized to access this page. <a href="?retry">Retry login</a>';

if (!isset($_SERVER['PHP_AUTH_USER']) or // user has not logged in
    (isset($_GET['retry']) and $_SERVER['PHP_AUTH_USER'] !== $username and $_SERVER['PHP_AUTH_PW'] !== $password)) {
    // user has logged in before, entered incorrect credentials, and is requesting retry
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
