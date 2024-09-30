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

// apagar item do pedido

if (isset($_GET["apagaritem"])) {
	$idel = $_GET['apagaritem'];
	$idce = $_GET['idce'];
	$delitem = $connect->query("DELETE FROM store WHERE idpedido='$idel'");
	$delopci = $connect->query("DELETE FROM store_o WHERE idp='$idel'");
	if ($delitem) {
		header("location: pdvpedido.php?idpedido=" . $idce . "B");
		exit;
	}
}

$pegadadospagamentos = $connect->query("SELECT * FROM metodospagamentos WHERE idu='$idu'");
$metodospagamentos = $pegadadospagamentos->fetchAll(PDO::FETCH_OBJ);

$_GET['idpedido'] = preg_replace("/[^0-9]/", "", $_GET['idpedido']);
$_SESSION["pedido_id_pdv"] = $_GET['idpedido'];

$id_cliente     = $_SESSION["pedido_id_pdv"];
$idPedido = $_GET['idpedido'];
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

if (isset($_POST["pedidodelivery"])) {

	if (empty($_POST['bairro'])) {
		// Exibe uma mensagem de alerta com CSS inline
		echo '
    <div style="
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
        position: relative;
    ">
        <strong>Atenção!</strong> O campo <b>Bairro</b> está vazio. Por favor, preencha-o.
        <button style="
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: transparent;
            border: none;
            font-size: 20px;
            line-height: 1;
            color: #721c24;
            cursor: pointer;
        ">&times;</button>
    </div>';
		return;
	}

	$nome 			= $_POST['nome'];
	$wps  			= $_POST['wps'];

	$data_nascimento = isset($_POST['data_nascimento']) ? $_POST['data_nascimento'] : "0000-00-00";
	$cep = isset($_POST['cep']) ? $_POST['cep'] : "000000";


	if (empty($_POST['fmpgto'])) {
		header("javascript:history.back()");
	}

	// $fmpgto  		= $_POST['fmpgto'];
	// Suponha que você tenha um valor em $_POST['fmpgto']
	$fmpgto_post = $_POST['fmpgto'];

	// Crie um array e adicione o valor
	$fmpgto_array = ['DELIVERY', $fmpgto_post];

	// Converta o array para JSON
	$fmpgto_json = json_encode($fmpgto_array);



	if (empty($_POST['troco'])) {
		$troco  		= '0.00';
	} else {
		$troco  		= $_POST['troco'];
	}

	if (empty($_POST['complemento'])) {
		$complemento	= "Não";
	} else {
		$complemento	= $_POST['complemento'];
	}

	$troco    		= str_replace(",", ".", $troco);
	$cidade  		= $_POST['cidade'];
	$uf  			= $_POST['uf'];
	$numero  		= $_POST['numero'];
	$bairro  		= $_POST['bairro'];
	$rua  			= $_POST['rua'];

	$taxa  			= $_POST['taxa'];

	$numero  		= $_POST['numero'];
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

	$inst = $connect->query("INSERT INTO pedidos(idu, idpedido, fpagamento, cidade, numero, complemento, rua, bairro, troco, nome, data, hora, celular, taxa, mesa, pessoas, obs, vsubtotal, vadcionais, vtotal) VALUES ('$idu','$id_cliente','$fmpgto_json','cidade','$numero','$complemento','$rua','$bairro','$troco','$nome','$data','$hora','$wps','$taxa','0','0','0','$subtotal','$adcionais','$totalg')");
	$update = $connect->query("UPDATE store SET status='1' WHERE idsecao='$id_cliente'");
	$update = $connect->query("UPDATE store_o SET status='1' WHERE ids='$id_cliente'");

	$stmt_pedido->execute();

	if (!empty($wps) && !empty($idu)) {
		$query = $connect->prepare("SELECT * FROM registroDados WHERE telefone = :telefone AND idu = :idu");
		$query->execute([':telefone' => $wps, ':idu' => $idu]);
		$registro = $query->fetch(PDO::FETCH_ASSOC);

		// Inserindo dados se o telefone não existir no banco
		if (!$registro) {
			$wps = preg_replace('/[^0-9]/', '', $wps);
			$insertDadosRegistro = $connect->prepare("
                INSERT INTO registroDados 
                (telefone, idu, nome, bairro, endereco, complemento, cep, casa, primeiro_nome, data_nascimento) 
                VALUES 
                (:telefone, :idu, :nome, :bairro, :endereco, :complemento, :cep, :casa, :primeiro_nome, :data_nascimento)
            ");

			$insertDadosRegistro->execute([
				':telefone' => $wps,
				':idu' => $idu,
				':nome' => $nome,
				':bairro' => $bairro,
				':endereco' => $rua,
				':complemento' => $complemento,
				':cep' => $cep,
				':casa' => $numero,
				':primeiro_nome' => $nome,
				':data_nascimento' => $data_nascimento
			]);
		}
	}

	if ($update) {
		unset($_SESSION['pedido_id_pdv']);
		header("location: pdv.php");
		exit;
	}
}


function removerAcentosEPontos($texto)
{
	// Remove acentos
	$texto = preg_replace(
		'/[áàãâä]/u',
		'a',
		$texto
	);
	$texto = preg_replace(
		'/[éèêë]/u',
		'e',
		$texto
	);
	$texto = preg_replace(
		'/[íìîï]/u',
		'i',
		$texto
	);
	$texto = preg_replace(
		'/[óòõôö]/u',
		'o',
		$texto
	);
	$texto = preg_replace(
		'/[úùûü]/u',
		'u',
		$texto
	);
	$texto = preg_replace(
		'/[ç]/u',
		'c',
		$texto
	);
	// Remove pontos
	$texto = str_replace('.', '', $texto);
	// Converte para maiúsculas
	return strtoupper($texto);
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
								<input type="hidden" name="pedidodelivery">
								<input type="hidden" name="cidade" value="<?php echo $dadosempresa->cidade; ?>">
								<input type="hidden" name="uf" value="<?php echo $dadosempresa->uf; ?>">

								<?php if ($somando->soma > $dadosempresa->dfree) { ?>
									<div class="row mg-b-10">
										<div align="center" class="col-lg-12">
											<div class="alert alert-success" role="alert">
												<strong class="tx-success"><i class="fa fa-thumbs-o-up mg-r-5"></i> Entrega Grátis.</strong>
											</div>
										</div>
									</div>
								<?php } ?>


								<div class="row">

									<div class="col-lg-6">
										<div class="form-group">
											<label class="form-control-label">Bairros e Regiões atendidas : <span class="tx-danger">*</span></label>
											<select id="select-taxa" class="form-control selec2">
												<?php if ($somando->soma > $dadosempresa->dfree) { ?>
													<option value="">Selecione</option>
													<?php
													$lerbanco  = $connect->query("SELECT * FROM bairros WHERE idu='" . $idu . "'");
													while ($taxabairro = $lerbanco->fetch(PDO::FETCH_OBJ)) {
													?>
														<option value="pdvdelivery.php?idpedido=<?= $id_cliente; ?>&tipo=delivery&bairro=<?= $taxabairro->id; ?>"><?php echo $taxabairro->bairro; ?></option>
													<?php } ?>
												<?php } else { ?>
													<option value="">Selecione</option>
													<?php
													$lerbanco  = $connect->query("SELECT * FROM bairros WHERE idu='" . $idu . "'");
													while ($taxabairro = $lerbanco->fetch(PDO::FETCH_OBJ)) {
													?>
														<option value="pdvdelivery.php?idpedido=<?= $id_cliente; ?>&tipo=delivery&bairro=<?= $taxabairro->id; ?>">Taxa de R$: <?php echo $taxabairro->taxa; ?></span> - <?php echo $taxabairro->bairro; ?></option>
													<?php } ?>
												<?php } ?>
											</select>
										</div>
									</div>



									<div class="col-lg-4">
										<div class="form-group">
											<label class="form-control-label">Bairro ou Região: </label>
											<div class="input-group">
												<?php if (isset($_GET["bairro"])) {
													$idbairro = $_GET["bairro"];
													$pegabairro = $connect->query("SELECT * FROM bairros WHERE id='" . $idbairro . "'");
													$pegabairro	= $pegabairro->fetch(PDO::FETCH_OBJ); ?>
													<input type="text" class="form-control" value="<?= $pegabairro->bairro; ?>" disabled="disabled">
													<input type="hidden" name="bairro" value="<?= $pegabairro->bairro; ?>">
												<?php } else { ?>
													<input type="text" class="form-control" value="Aguardando..." disabled="disabled">
												<?php } ?>
											</div>
										</div>
									</div>

									<div class="col-lg-2">
										<div class="form-group">
											<label class="form-control-label">Taxa: </label>
											<div class="input-group">
												<?php if (isset($_GET["bairro"])) {
													$idbairro2 = $_GET["bairro"];
													$pegataxa = $connect->query("SELECT * FROM bairros WHERE id='" . $idbairro . "'");
													$pegataxa	= $pegataxa->fetch(PDO::FETCH_OBJ); ?>
													<?php if ($somando->soma > $dadosempresa->dfree) { ?>
														<input type="text" class="form-control" value="0.00" disabled="disabled">
														<input type="hidden" name="taxa" value="0.00">
														<?php $taxa = "0.00"; ?>
													<?php } else { ?>
														<input type="text" class="form-control" value="<?= $pegataxa->taxa; ?>" disabled="disabled">
														<input type="hidden" name="taxa" value="<?= $pegataxa->taxa; ?>">
														<?php $taxa = $pegataxa->taxa; ?>
													<?php } ?>
												<?php } else { ?>
													<?php $taxa = "0.00"; ?>
													<input type="text" class="form-control" value="Aguardando..." disabled="disabled">
													<input type="hidden" name="taxa" value="<?= $taxa; ?>">
												<?php } ?>

											</div>
										</div>
									</div>


									<div class="col-lg-12">
										<div class="form-group">
											<label class="form-control-label">Nº do seu WhatsApp: <span class="tx-danger">*</span></label>
											<input type="text" id="cel" placeholder="DDD+Número" name="wps" class="form-control" required>
										</div>
									</div>

									<div class="col-lg-12">
										<div class="form-group dtn">
											<label class="form-control-label">Data de Aniversario: <span class="tx-danger">*</span></label>
											<input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
										</div>
									</div>
								</div>
								<div class="row">

									<div class="col-lg-9">
										<div class="form-group">
											<label class="form-control-label">Endereço: <span class="tx-danger">*</span></label>
											<div class="input-group">
												<input class="form-control" id="rua" type="text" name="rua" maxlength="100" required>
											</div>
										</div>
									</div>
									<div class="col-lg-3">
										<div class="form-group">
											<label class="form-control-label">Número: <span class="tx-danger">*</span></label>
											<div class="input-group">
												<input type="text" id="casa" class="form-control" name="numero" maxlength="5" required>
											</div>
										</div>
									</div>

								</div>

								<div class="row">

									<div class="col-lg-6">
										<div class="form-group">
											<label class="form-control-label">Ponto de Referência/Complemento: </label>
											<input class="form-control" type="text" id="complemento" name="complemento" maxlength="160">
										</div>
									</div>

									<div class="col-lg-6">
										<div class="form-group">
											<label class="form-control-label">Primeiro Nome: <span class="tx-danger">*</span></label>
											<input type="text" id="nome" name="nome" class="form-control" maxlength="60" required>
										</div>
									</div>

								</div>

								<hr>

								<div class="row">

									<div class="col-lg-6">
										<div class="form-group">
											<label class="form-control-label">Forma de Pagamento: <span class="tx-danger">*</span></label>
											<!-- <select id="options" class="form-control" onChange="verifica(this.value)" name="fmpgto" required>
												<option value="" disabled selected><b>Selecione...</b></option>
												<option value="DINHEIRO">Dinheiro</option>
												<option value="CARTAO">Cartão</option>
											</select> -->

											<select style="width:100%;" id="options" class="form-control" onChange="verifica(this.value)" name="fmpgto" required>
												<option value="" disabled selected><b>SELECIONE...</b></option>
												<?php foreach ($metodospagamentos as $metodo) { ?>
													<option value="<?php echo removerAcentosEPontos($metodo->metodopagamento); ?>">
														<?php echo removerAcentosEPontos($metodo->metodopagamento); ?>
													</option>
												<?php } ?>
											</select>


										</div>
									</div>

									<div class="col-lg-6">
										<div class="form-group">
											<label class="form-control-label">Precisa de Troco?: </label>
											<input type="text" name="troco" id="troco" value="0,00" class="dinheiro form-control">
										</div>
									</div>

								</div>
								<hr>
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
		<script src="../lib/jquery.maskedinput/js/jquery.maskedinput.js"></script>
		<script src="../lib/select2/js/select2.full.min.js"></script>
		<script src="../js/moeda.js"></script>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script src="../js/jquery.mask.min.js"></script>




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
		<script>
			$(function() {
				'use strict';
				$('#cel').mask('(99)99999-9999');
				$('#numb').mask('9999');

			});
		</script>

		<script>
			$(document).ready(function() {

				function formatarData(data) {
					// Verifica se a data está no formato "YYYY-MM-DD"
					if (data && data.includes('-')) {
						let partes = data.split('-'); // Divide a data em partes [AAAA, MM, DD]
						let dataFormatada = `${partes[2]}/${partes[1]}/${partes[0]}`; // Reorganiza para DD/MM/AAAA
						return dataFormatada;
					}
					return data; // Retorna a data sem alteração se não estiver no formato esperado
				}


				function revertData(data) {
					if (data && data.includes("/")) {
						const partes = data.split("/");
						const formatData = `${partes[2]}-${partes[1]}-${partes[0]}`

						return formatData
					}

					return data
				}

				// Quando o campo de telefone perde o foco (blur), esta função é chamada
				$('#cel').on('blur', function() {
					// Obtém o valor do campo de telefone
					var telefone = $(this).val();

					// Remove os parênteses, espaços e traços do telefone, mantendo apenas números
					telefone = telefone.replace(/[^\d]/g, '');

					// Seleciona o elemento de carregamento para exibir uma mensagem de "Carregando"
					const loadingUser = $(".loading-informaceos");

					// Verifica se o telefone não está vazio
					if (telefone) {
						// Se o telefone não está vazio, exibe o loading
						loadingUser.html(`
							<span class="loader"></span>
							<p>Carregando Dados...</p>
                		`);

						// Faz a requisição AJAX usando jQuery
						$.ajax({
							url: '../include/verificarTelefone.php', // URL do arquivo PHP que processará a requisição
							type: 'POST', // Método de envio
							data: {
								telefone: telefone,
								id_empresa: <?php echo $idu; ?> // Envia o ID da empresa
							}, // Dados que estão sendo enviados
							dataType: 'json', // Espera que a resposta seja um JSON
							success: function(response) {
								// Manipula a resposta recebida do servidor
								// console.log("Resposta do servidor: ", response);

								// Verifica se a resposta indica que o telefone existe
								if (response.existe) {
									// Exibe a modal informando que o telefone foi encontrado
									setTimeout(() => loadingUser.html(``), 2000);


									// Preenche os campos com os dados retornados
									$('#nome').val(response.nome);
									$('#rua').val(response.endereco)
									$('#endereco').val(response.endereco);
									$('#complemento').val(response.complemento);
									$('#cep').val(response.cep);
									$('#casa').val(response.casa);
									$('#primeiro_nome').val(response.primeiro_nome);

									// Verifica se o campo de data_nascimento está presente na resposta e atribui ao campo de input
									if (response.data_nascimento) {
										let dataFormatada = formatarData(response.data_nascimento);
										// console.log(dataFormatada)

										const dataOrginal = revertData(dataFormatada);
										// console.log(dataOrginal);




										$('.dtn').html(`
											<label class="form-control-label">Data de Nascimento <span class="text-success">(Encontrada)</span>: <span class="tx-danger">*</span></label>
											<input type="date" class="form-control" value="${dataOrginal}" disabled required>
										`);
										// $('.dtn').html(`
										//     <div class="alert alert-info">
										//         <i class="fa fa-info-circle" aria-hidden="true"></i> Data encontrada ${dataFormatada}. Entre em contato com a empresa para mudar se você acha que isso foi um erro.
										//     </div>
										// `);

									} else {
										$('.dtn').html(`
                                    		<label class="form-control-label">Data de Nascimento: <span class="tx-danger">*</span></label>
                                    		<input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
                                		`);
									}

								} else {
									// // Se o telefone não foi encontrado, limpa o loading
									loadingUser.html(``);
									$('.dtn').html(`
                                    		<label class="form-control-label">Data de Nascimento: <span class="tx-danger">*</span></label>
                                    		<input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
                                		`);
								}
							},
							error: function(xhr, status, error) {
								console.error("Erro na requisição AJAX: ", status, error);
							}
						});
					} else {
						// Se o telefone estiver vazio, limpa o loading
						// loadingUser.html('');
					}
				});
			});
		</script>



</body>

</html>