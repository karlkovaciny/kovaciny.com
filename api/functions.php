<?php 
function http_error_response($code, $message) {
    header('X-PHP-Response-Code: $code', true, $code);
    echo "{\"code\":$code, \"message\":\"$message\"}";
    echo "\n";
    var_dump($_POST);
    die();
}
?>