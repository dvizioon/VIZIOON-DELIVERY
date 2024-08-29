<?php

ob_start();
session_start();

if (!isset($_SESSION['id_cliente'])) {
    $id_cliente = rand(100000, 999999) . "PW";
    $_SESSION["id_cliente"] = $id_cliente;
} else {
    $id_cliente = $_SESSION['id_cliente'];
    // Força a adição do sufixo "PW" se não estiver presente
    if (strpos($id_cliente, 'PW') === false) {
        $id_cliente .= "PW";
        $_SESSION["id_cliente"] = $id_cliente;
    }
}

// Verifica se o id_cliente foi corretamente gerado ou recuperado
if (!isset($id_cliente)) {
    header("location: ./");
    exit;
}

// Inclui os arquivos necessários
include('../funcoes/Conexao.php');
include('../funcoes/Key.php');
include('db/base.php');

$xurl = 'teste';
$site = HOME;
include('db/Funcoes.php');

$Url[1] = (empty($Url[1]) ? null : $Url[1]);
