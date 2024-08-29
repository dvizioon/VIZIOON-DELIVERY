<?php

session_start();

if (isset($_COOKIE['pdvx'])) {
    setcookie('pdvx', '', 0, '', '', isset($_SERVER['HTTPS']), true);
}


session_unset(); 
session_destroy();

session_write_close();


header('Location: index.php');
exit(); 
