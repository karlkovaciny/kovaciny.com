<?php
    require_once('access_token.php'); //in lieu of a real one
    require_once('dbconnect.php');
    echo "<html><body><pre>";
    //echo html_entity_decode(file_get_contents('php://input'));
    echo "<BR><BR>";
    
    $jso = json_decode($_POST['json']);
    var_dump($jso);
    echo "<BR><BR>----- " . $jso->text . " ------<BR><BR>";
    //$commentIds = array_map('intval', $_POST['commentIds']);
    //var_dump($commentIds);
    echo "<BR><BR>";
    
    //get list of comments to load
    //return what's asked for eg data 
?>