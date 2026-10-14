<?php
session_start();

// Ellam session ah azhichidu
$_SESSION = array();

// Cookie iruntha athuvum delete pannu
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Session destroy pannu
session_destroy();

// Login page ku thiruppi anuppu
header("Location: login.php?msg=logged_out");
exit();
?>