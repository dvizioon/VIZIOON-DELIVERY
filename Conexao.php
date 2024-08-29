<?php
$servidor = 'localhost';
$usuario  = 'root';
$senha       = '';
$banco    = 'cardapio';



//FILTROS DE ENTRADA DO USUÁRIO
require_once(__DIR__ . "/filter-input.php");


// FUNCOES DO SISTEMA DE CADASTRO ###########

$nomesite  = 'CALANGO FOODS';
$urlmaster = 'http://cardapio.local'; // APENAS A URL PRINCIPAL SEM A BARRA NO FINAL ---- ----

date_default_timezone_set('America/Sao_Paulo');
