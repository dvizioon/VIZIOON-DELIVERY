<?php
ob_start();
session_start();

$id_cliente = $_SESSION['id_cliente'];

include_once('../../funcoes/Conexao.php');
include_once('../../funcoes/Key.php');

if (isset($_POST["produto"])) {

	$iduser     = $_POST["iduser"];
	$idcat      = $_POST["idcat"];
	$produto    = $_POST["produto"];
	$valor      = isset($_POST["valor"]) ? $_POST["valor"] : "0.00";
	$idsecao    = $_POST["idsecao"];
	$idpedido   = $_POST["idpedido"];
	$quantidade = $_POST["quantidade"];
	$obser      = $_POST["observacoes"];
	$referencia = $_POST["referencia"]; // Capturando a referência

	// Preparar e executar a inserção do produto
	if (isset($_POST['tamanho'])) {
		$taman = $_POST['tamanho'];
		$array = explode(',', $taman);

		$stmt = $connect->prepare("INSERT INTO store (idu, idsecao, idpedido, produto_id, data, valor, quantidade, tamanho, obs, referencia) VALUES (:idu, :idsecao, :idpedido, :produto_id, :data, :valor, :quantidade, :tamanho, :obs, :referencia)");
		$stmt->execute([
			':idu' => $iduser,
			':idsecao' => $id_cliente,
			':idpedido' => $idpedido,
			':produto_id' => $produto,
			':data' => date("d-m-Y"),
			':valor' => $array[1],
			':quantidade' => $quantidade,
			':tamanho' => $array[0],
			':obs' => $obser,
			':referencia' => $referencia
		]);
	} else {
		$stmt = $connect->prepare("INSERT INTO store (idu, idsecao, idpedido, produto_id, data, valor, quantidade, obs, referencia) VALUES (:idu, :idsecao, :idpedido, :produto_id, :data, :valor, :quantidade, :obs, :referencia)");
		$stmt->execute([
			':idu' => $iduser,
			':idsecao' => $id_cliente,
			':idpedido' => $idpedido,
			':produto_id' => $produto,
			':data' => date("d-m-Y"),
			':valor' => $valor,
			':quantidade' => $quantidade,
			':obs' => $obser,
			':referencia' => $referencia
		]);
	}

	// Inserir meio a meio
	if (isset($_POST['meioameios'])) {
		foreach ($_POST['meioameios'] as $valueo) {
			$text = $valueo;
			$array = explode(',', $text);

			$stmt = $connect->prepare("INSERT INTO store_o (idu, ids, idp, nome, valor, quantidade, meioameio, id_referencia) VALUES (:idu, :ids, :idp, :nome, :valor, :quantidade, :meioameio, :id_referencia)");
			$stmt->execute([
				':idu' => $iduser,
				':ids' => $id_cliente,
				':idp' => $idpedido,
				':nome' => $array[0],
				':valor' => $array[1],
				':quantidade' => $quantidade,
				':meioameio' => 1,
				':id_referencia' => $referencia
			]);
		}

		// Pegar o maior valor dos itens meio a meio
		$stmt = $connect->prepare("SELECT MAX(valor) AS valor FROM store_o WHERE idp = :idp AND meioameio = 1 AND id_referencia = :id_referencia");
		$stmt->execute([
			':idp' => $idpedido,
			':id_referencia' => $referencia
		]);
		$pegarmaiorvalorx = $stmt->fetch(PDO::FETCH_OBJ);

		if ($pegarmaiorvalorx) {
			$idlXd = $pegarmaiorvalorx->valor;

			// Atualizar valor apenas para o item com a mesma referência
			$stmt = $connect->prepare("UPDATE store SET valor = :valor WHERE idpedido = :idpedido AND referencia = :referencia");
			$stmt->execute([
				':valor' => $idlXd,
				':idpedido' => $idpedido,
				':referencia' => $referencia
			]);
		}
	}

	// Inserir opcionais
	if (isset($_POST['opcionais'])) {
		foreach ($_POST['opcionais'] as $Id => $valueo) {
			$text = $valueo;
			$array = explode(',', $text);

			$stmt = $connect->prepare("INSERT INTO store_o (idu, ids, idp, nome, valor, quantidade, meioameio, id_referencia) VALUES (:idu, :ids, :idp, :nome, :valor, :quantidade, :meioameio, :id_referencia)");
			$stmt->execute([
				':idu' => $iduser,
				':ids' => $id_cliente,
				':idp' => $idpedido,
				':nome' => $array[0],
				':valor' => $array[1],
				':quantidade' => $quantidade,
				':meioameio' => 0,
				':id_referencia' => $referencia
			]);
		}
	}

	header("location: ../?ok=");
	exit;
}

if (isset($_GET["up"])) {
	$file = $_GET["up"];
	// Validar o arquivo antes de tentar excluí-lo
	if (file_exists($file)) {
		unlink($file);
	}
}

ob_end_flush();
