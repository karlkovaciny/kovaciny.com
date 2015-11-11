<?php
//this should give you a proper usertoken, but it doesn't

//authentication of sorts
if (!empty($_COOKIE['user'])) {
    $usertoken = $_COOKIE['user'];
} else {
    session_start();
    $usertoken = !empty($_SESSION['user']) ? $_SESSION['user'] : "";
}

if (empty($usertoken)) {
    http_error_response(401, "no usertoken supplied");
}   
?>