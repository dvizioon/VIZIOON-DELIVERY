<?php
session_start();

if (isset($_COOKIE['pdvx'])) {
    // Remove o cookie definindo um tempo de expiração no passado
    setcookie('pdvx', '', time() - 3600, '/');
    unset($_COOKIE['pdvx']); // Remove o valor de $_COOKIE, se necessário
}

session_destroy();
session_write_close();
header('Location: index.php');
exit(); // Adicione exit para garantir que nenhum código adicional seja executado

?>