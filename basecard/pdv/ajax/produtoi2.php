<?php
ob_start();
session_start();
if (isset($_COOKIE['pdvx'])) {
	$idu = $_COOKIE['pdvx'];
} else {
	header("location: sair.php");
	exit;
}
$id_cliente = $_SESSION['id_cliente'];

include_once('../../../funcoes/Conexao.php');
include_once('../../../funcoes/Key.php');

if (isset($_POST["produto"])) {

	$iduser     = $_POST["iduser"];
	$idcat      = $_POST["idcat"];
	$produto    = $_POST["produto"];
	$valor      = isset($_POST["valor"]) ? $_POST["valor"] : "0.00";
	$idsecao    = $_POST["idsecao"];
	$idpedido   = $_POST["idpedido"];
	$quantidade = $_POST["quantidade"];
	$obser      = $_POST["observacoes"];
	$referencia = $_POST["referencia"];  // Capturando a referência

	if (isset($_POST['tamanho'])) {

		$taman = $_POST['tamanho'];
		$array = explode(',', $taman);

		$inserpro = $connect->query("INSERT INTO store (idu, idsecao, idpedido, produto_id, data, valor, quantidade, tamanho, status, obs, referencia) VALUES ('" . $iduser . "','" . $id_cliente . "','" . $idpedido . "','" . $produto . "','" . date("d-m-Y") . "','" . $array[1] . "','" . $quantidade . "','" . $array[0] . "','1','" . $obser . "', '" . $referencia . "')");
	} else {

		$inserpro = $connect->query("INSERT INTO store (idu, idsecao, idpedido, produto_id, data, valor, quantidade, obs, status, referencia) VALUES ('" . $iduser . "','" . $id_cliente . "','" . $idpedido . "','" . $produto . "','" . date("d-m-Y") . "','" . $valor . "','" . $quantidade . "','" . $obser . "', '1', '" . $referencia . "')");
	}

	if (isset($_POST['meioameios'])) {

		foreach ($_POST['meioameios'] as $valueo) {
			$text = $valueo;
			$array = explode(',', $text);
			$inserpro = $connect->query("INSERT INTO store_o (idu, ids, idp, nome, valor, quantidade, meioameio, status, id_referencia) VALUES ('" . $iduser . "','" . $id_cliente . "','" . $idpedido . "','" . $array[0] . "','" . $array[1] . "','" . $quantidade . "','1','1', '" . $referencia . "')");
		}

		// Alteração para considerar a referência ao alterar o valor
		$pegarmaiorvalor = $connect->query("SELECT MAX(valor) AS valor FROM store_o WHERE idp='" . $idpedido . "' AND meioameio='1' AND id_referencia='" . $referencia . "'");
		$pegarmaiorvalorx = $pegarmaiorvalor->fetch(PDO::FETCH_OBJ);
		$idlXd = $pegarmaiorvalorx->valor;

		$alteravalor = $connect->query("UPDATE store SET valor='$idlXd' WHERE idpedido='$idpedido' AND referencia='" . $referencia . "'");
	}


	if (isset($_POST['opcionais'])) {

		foreach ($_POST['opcionais'] as $Id => $valueo) {
			$text = $valueo;
			$array = explode(',', $text);
			$inserpro = $connect->query("INSERT INTO store_o (idu, ids, idp, nome, valor, quantidade, meioameio, status, id_referencia) VALUES ('" . $iduser . "','" . $id_cliente . "','" . $idpedido . "','" . $array[0] . "','" . $array[1] . "','" . $quantidade . "','0','1', '" . $referencia . "')");
		}
	}

	if ($inserpro) {
		header("location: ../pdvpedido.php?idpedido=" . $id_cliente . "");
		exit;
	}
}

if (isset($_GET["up"])) {
	echo unlink("" . $_GET["up"] . "");
}

ob_end_flush();
