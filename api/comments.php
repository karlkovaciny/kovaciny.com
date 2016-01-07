<?php
    require_once('access_token.php'); //in lieu of a real one
    require_once('dbconnect.php');
    echo "<html><body><pre>";
    var_dump($_POST['commentIds']);
    echo "<BR>-----------<BR>";
    $commentIds = array_map('intval', $_POST['commentIds']);
    var_dump($commentIds);
    
    //get list of comments to load
    //return what's asked for eg data 
?>