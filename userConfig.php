<?php
    $adminUsers = array('admin');
    function isAdmin() {
        global $adminUsers;
        if(!isset($_SESSION['user'])) {
            return false;
        }
        return in_array($_SESSION['user'], $adminUsers);
    }
?>