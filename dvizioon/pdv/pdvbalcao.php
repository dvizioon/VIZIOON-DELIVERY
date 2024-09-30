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
$_SESSION["pedido_id_pdv"] = $_GET['idpedido'];

$id_cliente     = $_SESSION["pedido_id_pdv"];
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

if (isset($_POST["pedidobalcao"])) {
	$nome 			= $_POST['nome'];
	$wps  			= $_POST['wps'];
	$fmpgto  		= "BALCAO";
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
	$data_nascimento = isset($_POST['data_nascimento']) ? $_POST['data_nascimento'] : "00-00-0000";
	$cep = isset($_POST['cep']) ? $_POST['cep'] : "";

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


	// Certifique-se de que o telefone e nome foram fornecidos
	if ($wps && $nome) {
		// Buscar os dados existentes no banco de dados
		$query = $connect->prepare("SELECT * FROM registroDados WHERE telefone = :telefone AND idu = :idu");
		$query->execute(['telefone' => $wps, 'idu' => $idu]);

		$registro = $query->fetch(PDO::FETCH_ASSOC);


		// Se o registro já existir, não atualize nada, apenas aproveite os dados
		if ($registro) {
			// O telefone já existe, não fazemos nenhuma atualização
			// Apenas seguimos com o uso do dado existente, se necessário
		} else {
			$wps = preg_replace('/[^0-9]/', '', $wps);
			// Se o telefone não existir, insira os dados
			$insertDadosRegistro = $connect->prepare("INSERT INTO registroDados (telefone, idu, nome, data_nascimento) 
                                     VALUES (:telefone, :idu, :nome, :data_nascimento)");
			$insertDadosRegistro->execute([
				'telefone' => $wps,
				'idu' => $idu,
				'nome' => $nome,
				'data_nascimento' => $data_nascimento
			]);
		}
	}



	// echo $nome_funcionario_criador;
	// echo $id_cliente;

	// if ($stmt_pedido->execute()) {
	// 	echo "Atualização do atendente criador bem-sucedida.";
	// } else {
	// 	echo "Erro na atualização do atendente criador.";
	// }


	$inst = $connect->query("INSERT INTO pedidos(idu, idpedido, fpagamento, cidade, numero, complemento, rua, bairro, troco, nome, data, hora, celular, taxa, mesa, pessoas, obs, vsubtotal, vadcionais, vtotal, entrada) VALUES ('$idu','$id_cliente','$fmpgto','cidade','$numero','$complemento','$rua','$bairro','$troco','$nome','$data','$hora','$wps','$taxa','0','0','0','$subtotal','$adcionais','$totalg','$totalg')");
	$update = $connect->query("UPDATE store SET status='1' WHERE idsecao='$id_cliente'");
	$update = $connect->query("UPDATE store_o SET status='1' WHERE ids='$id_cliente'");
	$stmt_pedido->execute();

	if ($update) {
		unset($_SESSION['pedido_id_pdv']);
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
							<div class="alert alert-dark client-found" style="border-left: 4px solid orange; padding: 15px; background-color: #f8f9fa;display:none;">

							</div>
							<form action="" method="post">
								<input type="hidden" name="pedidobalcao">

								<div class="row">

									<div class="col-lg-12">
										<div class="form-group">
											<label class="form-control-label">Nº do seu WhatsApp: <span class="tx-danger">*</span></label>
											<input type="text" placeholder="DDD+Número" id="cel" name="wps" class="form-control" required>
										</div>
									</div>

								</div>


								<div class="row">

									<div class="col-lg-12">
										<div class="form-group dtn">
											<label class="form-control-label">Data de Aniversario: <span class="tx-danger">*</span></label>
											<input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
										</div>
									</div>

								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="form-group">
											<label class="form-control-label">Primeiro Nome: <span class="tx-danger">*</span></label>
											<input type="text" name="nome" id="nome" class="form-control" maxlength="60" required>
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

		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

		<script>
			$(document).ready(function() {
				const clientFound = $(".client-found");


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


				clientFound.css("display", "none"); // Correção para esconder o elemento usando jQuery

				// Quando o campo de telefone perde o foco (blur), esta função é chamada
				$('#cel').on('blur', function() {
					// Obtém o valor do campo de telefone
					var telefone = $(this).val();

					// Remove caracteres não numéricos (parênteses, traços, espaços, etc.) do número de telefone
					telefone = telefone.replace(/[^\d]/g, '');

					// Seleciona o elemento de carregamento para exibir uma mensagem de "Carregando"


					// Define o conteúdo HTML para o indicador de carregamento (spinner e mensagem)
					clientFound.html(`
						<i class="fa fa-database" aria-hidden="true" style="color: orange;"></i>
						 <strong> Procurando Cliente...
					`);

					// Verifica se o telefone não está vazio
					if (telefone) {
						// Se o campo de telefone tiver um valor, faz uma requisição AJAX para o servidor
						$.ajax({
							url: '../include/verificarTelefone.php', // URL do script PHP que verificará o número de telefone
							type: 'POST', // Tipo de requisição (POST)
							data: {
								telefone: telefone, // Envia o número de telefone
								id_empresa: <?php echo $idu; ?> // Envia o ID da empresa
							}, // Dados enviados na requisição
							dataType: 'json', // Espera que o servidor responda em formato JSON
							success: function(response) {
								// console.log(response)
								clientFound.css("display", "block");
								// Se a requisição for bem-sucedida, esta função será chamada
								// Verifica se o telefone existe na resposta recebida do servidor
								if (response.existe) {
									// Se o telefone existir, exibe as informações e remove o loading após 2 segundos
									setTimeout(() => clientFound.html(`<i class="fa fa-database" aria-hidden="true" style="color: orange;"></i>
								<strong>Cliente Encontrado na Base de Dados.</strong> podemos prosseguir com o pedido.`), 1000);

									// Preenche o campo de nome com o nome recebido na resposta
									$('#nome').val(response.nome);

									// Outros campos estão comentados, mas poderiam ser preenchidos da mesma forma
									// $('#rua').val(response.bairro);
									// $('#endereco').val(response.endereco);
									// $('#complemento').val(response.complemento);
									// $('#cep').val(response.cep);
									// $('#casa').val(response.casa);
									// $('#primeiro_nome').val(response.primeiro_nome);
									// Verifica se o campo de data_nascimento está presente na resposta e atribui ao campo de input
									if (response.data_nascimento) {
										let dataFormatada = formatarData(response.data_nascimento);

										// $('.dtn').html(`
										// <div class="alert alert-info">
										// 	<i class="fa fa-info-circle" aria-hidden="true"></i> Data encontrada ${dataFormatada}. Entre em contato com administração da empresa para mudar se você acha que isso foi um erro.
										// </div>
										//  `);
										const dataOrginal = revertData(dataFormatada);
										// console.log(dataOrginal);

										$('.dtn').html(`
											<label class="form-control-label">Data de Nascimento <span class="text-success">(Encontrada)</span>: <span class="tx-danger">*</span></label>
											<input type="date" class="form-control" value="${dataOrginal}" disabled required>
										`);

									} else {
										$('.dtn').html(`
											<label class="form-control-label">Data de Nascimento: <span class="tx-danger">*</span></label>
											<input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
										`);
									}


								} else {

									clientFound.css("display", "none");
									$('.dtn').html(`
											<label class="form-control-label">Data de Nascimento: <span class="tx-danger">*</span></label>
											<input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
										`);
								}
							},
							error: function(xhr, status, error) {
								// Se o campo de telefone estiver vazio, remove o loading
								clientFound.css("display", "none");
								// Em caso de erro na requisição, exibe uma mensagem de erro
								console.error("Erro na requisição AJAX: ", status, error);
								// alert('Erro ao buscar os dados. Tente novamente.');
							}
						});
					} else {
						// Se o campo de telefone estiver vazio, remove o loading
						clientFound.css("display", "none");

						$('.dtn').html(`
											<label class="form-control-label">Data de Nascimento: <span class="tx-danger">*</span></label>
											<input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
										`);
						// (Opcional) Você pode adicionar uma mensagem para alertar o usuário que o campo está vazio
						// alert('Por favor, insira um número de telefone válido.');
					}
				});
			});
		</script>
</body>

</html>