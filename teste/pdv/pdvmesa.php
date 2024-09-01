<?php
//ob_start();
session_start();
if (isset($_COOKIE['pdvx'])) {
	$idu = $_COOKIE['pdvx'];
} else {
	header("location: sair.php");
}
include_once('../../funcoes/Conexao.php');
include_once('../../funcoes/Key.php');

//$_GET['idpedido'] = preg_replace("/[^0-9]/", "", $_GET['idpedido']);
$_SESSION["id_cliente"] = $_GET['idpedido'];

// Mesas
$mesas_informacoes_disponiveis_mesas = $connect->query("SELECT * FROM mesas WHERE idu='" . $idu . "' ORDER BY id DESC");
$mesas_disponiveis = $mesas_informacoes_disponiveis_mesas->fetch(PDO::FETCH_OBJ);

// Clientes
$pedidos_informacoes_disponiveis = $connect->query("SELECT * FROM pedidos WHERE idu='" . $idu . "' ORDER BY id DESC");
$pedidos_disponiveis = $pedidos_informacoes_disponiveis->fetchAll(PDO::FETCH_OBJ);



$id_cliente     = $_SESSION['id_cliente'];
$tipo_pedido     = $_GET['tipo'];

$empresa 		= $connect->query("SELECT * FROM config WHERE id='$idu'");
$dadosempresa 	= $empresa->fetch(PDO::FETCH_OBJ);

date_default_timezone_set('' . $dadosempresa->fuso . '');

$categorias 	= $connect->query("SELECT * FROM categorias WHERE idu='$idu' ORDER BY posicao ASC");

$produtosca 	= $connect->query("SELECT * FROM store WHERE idsecao = '$id_cliente' AND status='1' AND idu='$idu' ORDER BY id DESC");
$produtoscx 	= $produtosca->rowCount();

if ($produtoscx > 0) {
	$somando 	= $connect->query("SELECT valor, SUM(valor * quantidade) AS soma FROM store WHERE idsecao='" . $id_cliente . "' and status='1' and idu='$idu'");
	$somando 	= $somando->fetch(PDO::FETCH_OBJ);
	$somandop 	= $connect->query("SELECT quantidade, SUM(quantidade) AS somap FROM store WHERE idsecao='" . $id_cliente . "' and status='1' and idu='$idu'");
	$somandop 	= $somandop->fetch(PDO::FETCH_OBJ);
}

//

if (isset($_POST["pedidomesa"])) {
	$nome 			= $_POST['nome'];
	$wps  			= $_POST['wps'];
	$fmpgto  		= "MESA";
	$mesa 			= $_POST['mesa'];
	$pessoas 		= $_POST['pessoas'];
	$observacoes	= $_POST['observacoes'];
	$troco  		= '0.00';
	$complemento	= '0';
	$cidade  		= '0';
	$uf  			= '0';
	$numero  		= '0';
	$bairro  		= '0';
	$rua  			= '0';
	$taxa  			= '0.00';
	$numero  		= '0';
	$subtotal 		= $_POST['subtotal'];
	$adcionais  	= $_POST['adcionais'];
	$totalg  		= $_POST['totalg'];
	$data			= date("d-m-Y");
	$hora			= date("H:i:s");

	// $id_pedido = isset($_POST['andamento']) ? $_POST['andamento'] : '';
	$nome_funcionario_criador = isset($_SESSION['nome_funcionario']) ? $_SESSION['nome_funcionario'] : "Sem Nome";
	// Atualiza o nome do atendente e o nome do atendente criador
	$sql_pedido = "UPDATE `pedidos`
                   SET `atendente_criador` = :atendente_criador
                   WHERE `idpedido` = :idpedido";
	// Preparar a declaração SQL
	$stmt_pedido = $connect->prepare($sql_pedido);
	$stmt_pedido->bindParam(':idpedido', $id_cliente);
	$stmt_pedido->bindParam(':atendente_criador', $nome_funcionario_criador);

	$inst = $connect->query("INSERT INTO pedidos(idu, idpedido, fpagamento, cidade, numero, complemento, rua, bairro, troco, nome, data, hora, celular, taxa, mesa, pessoas, obs, vsubtotal, vadcionais, vtotal) VALUES ('$idu','$id_cliente','$fmpgto','cidade','$numero','$complemento','$rua','$bairro','$troco','$nome','$data','$hora','$wps','$taxa','$mesa','$pessoas','$observacoes','$subtotal','$adcionais','$totalg')");
	$update = $connect->query("UPDATE store SET status='1' WHERE idsecao='$id_cliente'");
	$update = $connect->query("UPDATE store_o SET status='1' WHERE ids='$id_cliente'");
	$stmt_pedido->execute();

	if ($update) {
		header("location: pdv.php");
		exit;
	}
}


?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Premium Quality and Responsive UI for Dashboard.">
	<meta name="author" content="ThemePixels">
	<title>RECEBIMENTO DE PEDIDOS</title>
	<link href="../lib/font-awesome/css/font-awesome.css" rel="stylesheet">
	<link href="../lib/Ionicons/css/ionicons.css" rel="stylesheet">
	<link href="../lib/datatables/css/jquery.dataTables.css" rel="stylesheet">
	<link href="../lib/select2/css/select2.min.css" rel="stylesheet">
	<link href="../lib/SpinKit/css/spinkit.css" rel="stylesheet">
	<link rel="stylesheet" href="../css/slim.css">
	<style>
		.bg-green {
			background-color: #4CAF50;
			color: #fff;
		}

		.bg-red {
			background-color: #f44336;
			color: #fff;
		}
	</style>
</head>

<body>

	<div class="slim-navbar">
		<div class="container">
			<ul class="nav">
				<li class="nav-item">
					<a class="nav-link" href="#">
						<span>
							Novo Pedido n° <?= $id_cliente; ?>
						</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="pdv.php">
						<i class="icon ion-ios-analytics-outline"></i>
						<span> VOLTAR </span>
					</a>
				</li>
			</ul>
		</div>
	</div>

	<div class="slim-mainpanel">
		<div class="container">

			<div class="row mg-t-10">

				<div class="col-md-9">

					<div class="card card-people-list pd-15 mg-b-10">

						<div class="media-list">

							<div class="row" style="margin-top:-30px">
								<div class="col-lg-12">
									<div class="slim-card-title d-flex align-items-center"><i class="fa fa-caret-right"></i> DADOS DO CLIENTE -<p> OPCIONAIS</p>
									</div>
								</div>
							</div>
							<br>
							<form action="" method="post">
								<input type="hidden" name="pedidomesa">

								<div class="row">

									<div class="col-lg-3">
										<div class="form-group">
											<label class="form-control-label">Nº do seu WhatsApp: <span class="tx-danger">*</span></label>
											<input type="text" placeholder="DDD+Número" maxlength="11" name="wps" class="form-control" required value="<?= $dadosempresa->celular; ?>">
										</div>
									</div>

									<div class="col-lg-3">
										<div class="form-group">
											<label class="form-control-label">Primeiro Nome: <span class="tx-danger">*</span></label>
											<input type="text" name="nome" class="form-control" maxlength="60" required value="CLIENTE CONSUMIDOR">
										</div>
									</div>

									<div class="col-lg-3">
										<?php
										// Exibição dos dados do usuário e mesas ocupadas
										// foreach ($pedidos_disponiveis as $mesa_usuario) {
										// 	echo "ID do Usuário: " . $mesa_usuario->id . "<br>";
										// 	echo "Mesas do Usuário: " . $mesa_usuario->mesa . "<br>";
										// }
										?>

										<div class="form-group">
											<label class="form-control-label">Escolha a Mesa: <span class="tx-danger">*</span></label>
											<select name="mesa" id="selectMesa" class="form-control" required>
												<?php
												// Loop para criar as opções de 1 a 10
												for ($i = 1; $i <= $mesas_disponiveis->numero; $i++) {
													// Verifica se a mesa está ocupada pelo usuário
													$mesa_ocupada = false;
													foreach ($pedidos_disponiveis as $mesa_usuario) {
														if ($mesa_usuario->mesa == $i) {
															$mesa_ocupada = true;
															break;
														}
													}

													// Define as classes de estilo e estado da opção
													$class = $mesa_ocupada ? 'bg-red' : 'bg-green';
													$disabled = $mesa_ocupada ? 'disabled' : '';

													// Cria a opção da mesa com as classes de estilo e estado dinâmicas
													echo '<option value="' . $i . '" class="' . $class . '" ' . $disabled . '>' . $i . '</option>';
												}
												?>
											</select>
										</div>
									</div>

									<script>
										// Script para desabilitar opções indisponíveis no carregamento da página
										document.addEventListener('DOMContentLoaded', function() {
											var selectMesa = document.getElementById('selectMesa');
											var options = selectMesa.options;

											for (var i = 0; i < options.length; i++) {
												if (options[i].classList.contains('bg-red')) {
													options[i].disabled = true;
												}
											}
										});
									</script>

									<div class="col-lg-3">
										<div class="form-group">
											<label class="form-control-label">Qnt de Pessoas: <span class="tx-danger">*</span></label>
											<input type="text" name="pessoas" class="form-control" maxlength="3" required>
										</div>
									</div>

								</div>

								<div class="row">

									<div class="col-lg-12">
										<div class="form-group">
											<label class="form-control-label">Observações: <span class="tx-danger">*</span></label>
											<textarea rows="3" name="observacoes" class="form-control" placeholder="Observações são opcionais..."></textarea>
										</div>
									</div>
								</div>
						</div>
					</div>
				</div>

				<div class="col-md-3 d-sm-block d-md-block d-lg-block">
					<?php include('carrinho_fim.php'); ?>
				</div>



			</div>
		</div>

		<script src="../lib/jquery/js/jquery.js"></script>
		<script src="../lib/bootstrap/js/bootstrap.js"></script>
		<script src="../js/moeda.js"></script>
		<script>
			function verifica(value) {
				var input = document.getElementById("troco");

				if (value == 'DINHEIRO') {
					input.disabled = false;
				} else if (value == 'CARTAO') {
					input.disabled = true;
				}
			};
		</script>

		<script>
			$('#select-taxa').change(function() {
				window.location = $(this).val();
			});
		</script>

		<script>
			$('.dinheiro').mask('#.##0,00', {
				reverse: true
			});
		</script>
</body>

</html>