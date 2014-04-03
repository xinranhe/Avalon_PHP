<?php
    session_start();
    //session_regenerate_id();
    if(!isset($_SESSION['user']) || !isset($_SESSION['timeout']))      // if there is no valid session
    {
        header("Location: login.php");
    }
    else {
        // session time in seconds;
        $sessionTime = 600;
        if($_SESSION['timeout'] + $sessionTime < time()) {
            // session time out
            include "logout.php";
            header("Location: login.php");
        }
    }
?>