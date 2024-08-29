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

$_GET['idpedido'] = preg_replace("/[^0-9]/", "", $_GET['idpedido']);
$_SESSION["id_cliente"] = $_GET['idpedido'];



$stmt = $connect->prepare("SELECT * FROM efeitosSonoros WHERE idu = ? AND padrao = 'h'");
$stmt->execute([$idu]);
$efeito_padrao = $stmt->fetch(PDO::FETCH_OBJ);


// var_dump($efeito_padrao);

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

$categorias 	= $connect->query("SELECT * FROM categorias WHERE idu='$idu' ORDER BY posicao ASC");

$produtosca 	= $connect->query("SELECT * FROM store WHERE idsecao = '$id_cliente' AND status='1' AND idu='$idu' ORDER BY id DESC");
$produtoscx 	= $produtosca->rowCount();

if ($produtoscx > 0) {
	$somando 	= $connect->query("SELECT valor, SUM(valor * quantidade) AS soma FROM store WHERE idsecao='" . $id_cliente . "' and status='1' and idu='$idu'");
	$somando 	= $somando->fetch(PDO::FETCH_OBJ);
	$somandop 	= $connect->query("SELECT quantidade, SUM(quantidade) AS somap FROM store WHERE idsecao='" . $id_cliente . "' and status='1' and idu='$idu'");
	$somandop 	= $somandop->fetch(PDO::FETCH_OBJ);
}

// var_dump($produtoscx);

//

if (isset($_POST["pedidomesa"])) {
	$nome 			= $_POST['nome'];
	$wps  			= $_POST['wps'];
	$mesa 			= $_POST['mesa'];
	$pessoas 		= $_POST['pessoas'];
	$observacoes	= $_POST['observacoes'];
	$subtotal 		= $_POST['subtotal'];
	$adcionais  	= $_POST['adcionais'];
	$totalg  		= $_POST['totalg'];

	$editarcor 		= $connect->query("UPDATE pedidos SET nome='$nome', celular='$wps', pessoas='$pessoas', fpagamento='MESA', mesa='$mesa', obs='$observacoes', vsubtotal='$subtotal', vadcionais='$adcionais', vtotal='$totalg' WHERE idpedido='$id_cliente'");

	if (isset($_SESSION['nomeprt']) || !empty($_SESSION['nomeprt'])) {
		//criar um script para página /home
		// $_SESSION['ativar_script_audio'] = " <script>
		// var audio = new Audio('./sounds/campainha.mp3');
		// audio.addEventListener('canplaythrough', function() {
		// 	audio.play();
		// 	});
		// 	</script>";

		if (empty($efeito_padrao)) {
			$_SESSION['ativar_script_audio'] = "
            <script>
                var audio = new Audio('./sounds/campainha.mp3');
                audio.addEventListener('canplaythrough', function() {
                    audio.play();
                });
            </script>
            ";
		} else {
			$_SESSION['ativar_script_audio'] = "
            <script>
                var audio = new Audio('./sounds/" . htmlspecialchars(basename($efeito_padrao->caminho)) . "');
                audio.addEventListener('canplaythrough', function() {
                    audio.play();
                });
            </script>
            ";
		}
		// Reseta a Integridade
	}

	$id_produto_status = isset($_SESSION['nomeprt']) ? "1" : "2";

	$editarcor = $connect->query("UPDATE pedidos SET status = '$id_produto_status' WHERE idpedido='$id_cliente'");

	if ($editarcor) {
		unset($_SESSION['nomeprt']);
		header("location: pdv.php");
		exit;
	}
}


// echo isset($_SESSION['nomeprt']) ? "Exite Produto no Carrinho" : "Não Existe Produto no carrinho";

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

		.bg-purple {
			background-color: #6f42c1;
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
									<div class="slim-card-title"><i class="fa fa-caret-right"></i> DADOS DO CLIENTE</div>
								</div>
							</div>
							<br>
							<form action="" method="post">
								<input type="hidden" name="pedidomesa">
								<?php
								$dadospedido 	= $connect->query("SELECT * FROM pedidos WHERE idpedido='$id_cliente'");
								$dadospedido 	= $dadospedido->fetch(PDO::FETCH_OBJ);
								?>
								<div class="row">

									<div class="col-lg-3">
										<div class="form-group">
											<label class="form-control-label">Nº do seu WhatsApp: <span class="tx-danger">*</span></label>
											<input type="text" placeholder="DDD+Número" maxlength="11" name="wps" class="form-control" value="<?= $dadospedido->celular; ?>" required>
										</div>
									</div>

									<div class="col-lg-3">
										<div class="form-group">
											<label class="form-control-label">Primeiro Nome: <span class="tx-danger">*</span></label>
											<input type="text" name="nome" class="form-control" maxlength="60" value="<?= $dadospedido->nome; ?>" required>
										</div>
									</div>

									<div class="col-lg-3">
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
													$class = '';
													$disabled = '';
													if ($dadospedido->mesa == $i) {
														$class = 'bg-purple'; // Cor roxa para a mesa atual do usuário
													} elseif ($mesa_ocupada) {
														$class = 'bg-red';
														$disabled = 'disabled';
													} else {
														$class = 'bg-green';
													}

													// Verifica se é a mesa padrão e marca como selecionada
													$selected = ($dadospedido->mesa == $i) ? 'selected' : '';

													// Cria a opção da mesa com as classes de estilo e estado dinâmicas
													echo '<option value="' . $i . '" class="' . $class . '" ' . $disabled . ' ' . $selected . '>' . $i . '</option>';
												}
												?>
											</select>
										</div>
									</div>

									<script>
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
											<input type="text" name="pessoas" class="form-control" maxlength="3" value="<?= $dadospedido->pessoas; ?>" required>
										</div>
									</div>

								</div>

								<div class="row">

									<div class="col-lg-12">
										<div class="form-group">
											<label class="form-control-label">Observações: <span class="tx-danger">*</span></label>
											<textarea rows="3" name="observacoes" class="form-control"><?= $dadospedido->obs; ?></textarea>
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