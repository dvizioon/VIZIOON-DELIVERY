<?php

if (isset($_COOKIE['pdvx'])) {
	$cod_id = $_COOKIE['pdvx'];
} else {
	header("location: sair.php");
}
include_once('../../funcoes/Conexao.php');
include_once('../../funcoes/Key.php');
session_start();


$pegadadosgerais 	= $connect->query("SELECT * FROM config WHERE id='$cod_id'");
$dadosgerais		= $pegadadosgerais->fetch(PDO::FETCH_OBJ);
$nomeempresa 		= $dadosgerais->nomeempresa;

date_default_timezone_set('' . $dadosgerais->fuso . '');

$codigop  = $_POST['codigop'];

$pedido    = $connect->query("SELECT * FROM pedidos WHERE idpedido='$codigop'");
$pedido    = $pedido->fetch(PDO::FETCH_OBJ);
$celcli    = $pedido->celular;

// var_dump($pedido);

$produtoscay 	= $connect->query("SELECT * FROM store WHERE idsecao = '$codigop' ORDER BY id DESC");

$produtoscaxy 	= $connect->query("SELECT * FROM store WHERE idsecao = '$codigop' ORDER BY id DESC");

$produtosca 	= $connect->query("SELECT * FROM store WHERE idsecao = '$codigop' ORDER BY id DESC");
$produtoscx 	= $produtosca->rowCount();


$item      = $connect->query("SELECT * FROM store WHERE idsecao='$codigop'");
$opcionais = $connect->query("SELECT * FROM store_o WHERE ids='$codigop'");

// andamento

$msg1 =  "Olá! O Seu pedido foi aceito e já foi encaminhado para o preparo.\n";
$msg1 .= "\n";
$msg1 .= "*" . $nomeempresa . "*\n";
$msg1;

// if (isset($_POST["andamento"])) {
// 	$update = $connect->query("UPDATE pedidos SET status='2' WHERE idpedido='" . $_POST["andamento"] . "'");

// 	header("location: pdv.php?ok=");
// }
// Verifica se o formulário foi enviado
if (isset($_POST["andamento"])) {
	$id_pedido = isset($_POST['andamento']) ? $_POST['andamento'] : '';
	$nome_funcionario_criador = isset($_SESSION['nome_funcionario']) ? $_SESSION['nome_funcionario'] : "Sem Nome";

	// Atualiza o status do pedido
	$update_status = $connect->prepare("UPDATE pedidos SET status = '2' WHERE idpedido = :idpedido");
	$update_status->bindParam(':idpedido', $id_pedido);
	$update_status->execute();

	// Verifica se o campo atendente_criador já possui algum valor
	$sql_verifica = "SELECT `atendente_criador` FROM `pedidos` WHERE `idpedido` = :idpedido";
	$stmt_verifica = $connect->prepare($sql_verifica);
	$stmt_verifica->bindParam(':idpedido', $id_pedido);
	$stmt_verifica->execute();
	$pedido = $stmt_verifica->fetch(PDO::FETCH_ASSOC);

	if ($pedido && empty($pedido['atendente_criador'])) {
		// Se o campo atendente_criador estiver vazio, atualize-o
		$sql_pedido = "UPDATE `pedidos`
                       SET `atendente_criador` = :atendente_criador
                       WHERE `idpedido` = :idpedido";

		// Preparar a declaração SQL
		$stmt_pedido = $connect->prepare($sql_pedido);

		// Vincular parâmetros
		$stmt_pedido->bindParam(':idpedido', $id_pedido);
		$stmt_pedido->bindParam(':atendente_criador', $nome_funcionario_criador);

		// Executar a declaração
		$stmt_pedido->execute();
	}

	// Redireciona para a página com mensagem de sucesso
	header("Location: pdv.php?ok=");
}

// saiu para entrega

$msg2 = "Olá! O seu pedido está a caminho.\n";
$msg2 .= "\n";
$msg2 .= "*" . $nomeempresa . "*\n";
$msg2;

if (isset($_POST["entrega"])) {
	$update = $connect->query("UPDATE pedidos SET status='3' WHERE idpedido='" . $_POST["entrega"] . "'");
	header("location: pdv.php?ok=");
}

// disponivel para retirada

$msg3 = "Olá! Seu pedido já esta disponível para retirada em nosso estabelecimento.\n";
$msg3 .= "\n";
$msg3 .= "*" . $nomeempresa . "*\n";
$msg3;

if (isset($_POST["retirada"])) {
	$update = $connect->query("UPDATE pedidos SET status='4' WHERE idpedido='" . $_POST["retirada"] . "'");
	header("location: pdv.php?ok=");
}

// finalizado

$msg4 = "Pedido entregue! Obrigado pela preferência.\n";
$msg4 .= "\n";
$msg4 .= "*" . $nomeempresa . "*\n";
$msg4;

if (isset($_POST["finalizado"])) {
	$update = $connect->query("UPDATE pedidos SET status='5' WHERE idpedido='" . $_POST["finalizado"] . "'");
	$update = $connect->query("UPDATE pedidos SET mesa='0' WHERE idpedido='" . $_POST["finalizado"] . "'");
	header("location: pdv.php?ok=");
}

// cancelado

$msg5 = "Olá! Infelizmente o seu pedido foi cancelado.\n";
$msg5 .= "\n";
$msg5 .= "*" . $nomeempresa . "*\n";
$msg5;

if (isset($_POST["cancelado"])) {
	$update = $connect->query("UPDATE pedidos SET status='6' WHERE idpedido='" . $_POST["cancelado"] . "'");
	$update = $connect->query("UPDATE pedidos SET mesa='0' WHERE idpedido='" . $_POST["cancelado"] . "'");
	header("location: pdv.php?ok=");
}

function formatCurrency($num)
{
	if (preg_match('/' . "," . '/', $num)) {
		return formatValorMoedaDatabase($num);
	} else {
		$num = formatMoedaBr($num);
		return formatValorMoedaDatabase($num);
	}
}
function formatValorMoedaDatabase($num)
{
	return str_replace(',', '.', preg_replace('#[^\d\,]#is', '', $num));
}
function formatMoedaBr($num)
{
	return number_format($num, 2, ',', '.');
}

$pegadadospagamentos = $connect->query("SELECT * FROM metodospagamentos WHERE idu='$cod_id'");
$metodospagamentos = $pegadadospagamentos->fetchAll(PDO::FETCH_OBJ);


/**
 * Função para verificar se o JSON de dados de pagamento está vazio.
 *
 * @param mixed $dados Dados do pagamento em formato JSON (string) ou já decodificado (array).
 * @return array|string Retorna um array vazio se não houver dados ou o array decodificado do JSON.
 */
function verificarDadosPagamentos($dados)
{
	// Se $dados não for uma string, assume que já é um array
	if (is_array($dados)) {
		$dados_pagamentos = $dados;
	} else {
		// Decodifica o JSON para um array PHP
		$dados_pagamentos = json_decode($dados, true);

		// Verifica se a decodificação foi bem-sucedida
		if (json_last_error() !== JSON_ERROR_NONE) {
			return 'Erro na decodificação do JSON.';
		}
	}

	// Verifica se 'dados' existe e é um array
	if (isset($dados_pagamentos['dados']) && is_array($dados_pagamentos['dados']) && !empty($dados_pagamentos['dados'])) {
		return $dados_pagamentos;
	} else {
		return []; // Retorna um array vazio se não houver dados
	}
}


if (!function_exists('truncarTexto')) {
	/**
	 * Trunca o texto após o primeiro espaço e adiciona '...' se o texto for maior que o comprimento mínimo.
	 *
	 * @param string $texto O texto a ser truncado.
	 * @param int $tamanho Máximo comprimento do texto antes do truncamento.
	 * @param int $comprimento_minimo Comprimento mínimo para truncar o texto.
	 * @return string Texto truncado com '...' adicionado após o primeiro espaço.
	 */
	function truncarTexto($texto, $tamanho = 50, $comprimento_minimo = 5)
	{
		if (strlen($texto) > $comprimento_minimo) {
			if (strlen($texto) > $tamanho) {
				$pos_espaco = strpos($texto, ' ', $tamanho);
				if ($pos_espaco !== false) {
					$texto = substr($texto, 0, $pos_espaco) . '...';
				} else {
					$texto = substr($texto, 0, $tamanho) . '...';
				}
			}
		}
		return $texto;
	}
}

// Função para obter os dados do pedido
function getPedidoData($connect, $id_pedido)
{
	$sql = "SELECT `atendente`, `atendente_criador` 
            FROM `pedidos` 
            WHERE `idpedido` = :idpedido";

	$stmt = $connect->prepare($sql);
	$stmt->bindParam(':idpedido', $id_pedido);
	$stmt->execute();

	// Buscar os resultados
	$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

	// Retorna os valores ou vazios se não existir
	return [
		'atendente_criador' => isset($resultado['atendente_criador']) ? $resultado['atendente_criador'] : '',
		'atendente_fechador' => isset($resultado['atendente']) ? $resultado['atendente'] : ''
	];
}

// var_dump($metodospagamentos);

// Nova logica para Itemns Selecionados
$typeControl = "hidden";
// Define a consulta
$query_registrospagamentos = "SELECT * FROM `registrospagamentos` WHERE `idpedido` = :idpedido";

// Prepara a consulta
$stmt = $connect->prepare($query_registrospagamentos);
$stmt->bindParam(':idpedido', $codigop, PDO::PARAM_INT);

// Executa a consulta
$stmt->execute();

// Obtém o primeiro resultado
$result_RegistroPagamento = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se há resultados
if ($result_RegistroPagamento) {
	$id = $result_RegistroPagamento['id'];
	$idu = $result_RegistroPagamento['idu'];
	$nome = $result_RegistroPagamento['nome'];
	$idpedido = $result_RegistroPagamento['idpedido'];
	$status = $result_RegistroPagamento['status'];
	$tipo = $result_RegistroPagamento['tipo'];
	$dados_pagamentos = $result_RegistroPagamento['dados_pagamentos'];
	$mesa_registrada = $result_RegistroPagamento['mesa_registrada'];
	$data_registro = $result_RegistroPagamento['data_registro'];
	$vsubtotal = $result_RegistroPagamento['vsubtotal'];
	$vtotal = $result_RegistroPagamento['vtotal'];
	$valor_dinheiro = $result_RegistroPagamento['valor_dinheiro'];
	$valor_troco = $result_RegistroPagamento['valor_troco'];
	$formapaga = $result_RegistroPagamento['formapaga'];
} else {
	// echo "Nenhum resultado encontrado.";
}


// // Decodifica o JSON para um array PHP
// $dados_pagamentos_json = json_decode($dados_pagamentos, true);
// // Variável global para armazenar a soma total das quantidades
// $total_quantidade_global = 0;
// // Verifica se a decodificação foi bem-sucedida
// if (isset($dados_pagamentos_json['dados']) && is_array($dados_pagamentos_json['dados'])) {
// // Calcula a soma total das quantidades
// foreach ($dados_pagamentos_json['dados'] as $pagamento) {
// // Remove caracteres indesejados e converte para ponto decimal
// $quantidade = str_replace(',', '.', $pagamento['quantidade']);

// // Verifica se a conversão para float foi bem-sucedida
// $quantidade_float = floatval($quantidade);
// if ($quantidade_float !== 0 || $quantidade === '0') {
// $total_quantidade_global += $quantidade_float;
// }
// }
// // Exibe a soma total das quantidades
// // echo "Soma Total das Quantidades: R$ " . number_format($total_quantidade_global, 2, ',', '.');
// } else {
// echo "Dados inválidos.";
// }

// global $total_quantidade_global;
// $total_global_soucer = $total_quantidade_global;
// $valor_troco_soucer = $valor_troco;
// $valor_total_Faltando = floatval($total_global_soucer) - floatval($valor_troco_soucer);
// echo "\nSoma Total Global: R$ " . floatval($total_global_soucer) - floatval($valor_troco_soucer);
// // Verifica e processa os dados de pagamento

// $status_parcial = $status;
// echo $status_parcial;

// $resultado_verificao = verificarDadosPagamentos($dados_pagamentos_json);
// if (empty($resultado_$resultado_verificao )) {
// echo "Não há dados disponíveis.";
// } else {
// echo "Dados: ";
// print_r($resultado);
// }

// Exemplo de dados (substitua com os dados reais)
$dados_pagamentos = isset($dados_pagamentos) ? $dados_pagamentos : '{"tipo":"parcial","dados":[]}'; // Valor padrão se não estiver definido
// Decodifica o JSON para um array PHP
$dados_pagamentos_json = verificarDadosPagamentos($dados_pagamentos);

// Variável global para armazenar a soma total das quantidades
$total_quantidade_global = 0;

// Verifica se 'dados' existe e é um array
if (!empty($dados_pagamentos_json['dados'])) {
	// Calcula a soma total das quantidades
	foreach ($dados_pagamentos_json['dados'] as $pagamento) {
		// Remove caracteres indesejados e converte para ponto decimal
		$quantidade = str_replace(',', '.', $pagamento['quantidade']);
		$quantidade_float = floatval($quantidade);

		// Adiciona a quantidade ao total se não for zero
		if ($quantidade_float > 0) {
			$total_quantidade_global += $quantidade_float;
		}
	}

	// Exibe a soma total das quantidades
	// echo "Soma Total das Quantidades: R$ " . number_format($total_quantidade_global, 2, ',', '.');
} else {
	// echo "Dados inválidos ou vazios.";
}

// Variáveis para cálculo posterior
$total_global_soucer = isset($total_quantidade_global) ? $total_quantidade_global : 0; // Valor padrão se não estiver definido
$valor_troco_soucer = isset($valor_troco) ? $valor_troco : 0; // Definir valor padrão se não estiver definido
$valor_total_pago = floatval($total_global_soucer) - floatval($valor_troco_soucer);
$valor_total_faltando = floatval($pedido->vtotal) - floatval($valor_total_pago);
// Exibe a soma total global
// echo "\nSoma Total Global: R$ " . number_format($valor_total_Faltando, 2, ',', '.');

// Exibe o status parcial (se disponível)
$status_parcial = isset($status) ? $status : '';
// echo "\nStatus Parcial: " . $status_parcial;
$nome_funcionario = isset($_SESSION['nome_funcionario']) ? $_SESSION['nome_funcionario'] : "Sem Nome";
// echo $nome_funcionario;
// Consulta para obter a comissão ativa
$query_comissao = "
SELECT comissao
FROM comissao
WHERE idu = :idu AND statuso = 'habilitado'
";
$stmt = $connect->prepare($query_comissao);
$stmt->bindParam(':idu', $cod_id, PDO::PARAM_INT);
$stmt->execute();
$comissao_ativa = $stmt->fetch(PDO::FETCH_ASSOC);

// Consulta para obter o total de comissão
$query_total_comissao = "
SELECT SUM(comissao) as total_comissao
FROM comissao
WHERE idu = :idu AND statuso = 'habilitado'
";
$stmt_total = $connect->prepare($query_total_comissao);
$stmt_total->bindParam(':idu', $cod_id, PDO::PARAM_INT);
$stmt_total->execute();
$total_comissao = $stmt_total->fetch(PDO::FETCH_ASSOC);


$pedidoData = getPedidoData($connect, $pedido->idpedido);
// Acessa os valores
$atendente_criador_pedido = $pedidoData['atendente_criador'];
$atendente_fechador_pedido = $pedidoData['atendente_fechador'];
// // Agora você pode usar $atendente_criador_pedido e $atendente_fechador_pedido como necessário
// echo "Atendente Criador: " . htmlspecialchars($atendente_criador_pedido) . "<br>";
// echo "Atendente Fechador: " . htmlspecialchars($atendente_fechador_pedido) . "<br>";

?>

<!DOCTYPE html>
<html lang="en">

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
	<link rel="stylesheet" href="./style.css">


</head>

<body>

	<div class="slim-navbar">
		<div class="container">
			<ul class="nav">
				<li class="nav-item">
					<a class="nav-link" href="#">
						<span>
							<?php

							$status = "";
							if ($pedido->status == 1) {
								print $statusxx = "Status Atual - Pedido Novo";
								$status = "Pedido Novo";
							}
							if ($pedido->status == 2) {
								print $statusxx = "Status Atual - Em Andamento";
								$status = "Em Andamento";
							}
							if ($pedido->status == 3) {
								print $statusxx = "Status Atual - Saiu para entrega";
								$status = "Saiu para entrega";
							}
							if ($pedido->status == 4) {
								print $statusxx = "Status Atual - Disp. para retirada";
								$status = "Disp. para retirada";
							}
							if ($pedido->status == 5) {
								print $statusxx = "Status Atual - Finalizado";
								$status = "Finalizado";
							}
							if ($pedido->status == 6) {
								print $statusxx = "Status Atual - Cancelado";
								$status = "Cancelado";
							}
							if ($pedido->status == 7) {
								print $statusxx = "Status Atual - Confirmado pela Cozinha";
								$status = "Confirmado pela Cozinha";
							}
							?>
						</span>

						<?php



						// Decodifica o JSON para um array PHP e acessa o primeiro elemento
						$primeiro_elemento_delivery  = (is_array($delivery_array = json_decode($pedido->fpagamento, true)) && !empty($delivery_array))
							? $delivery_array[0]
							: null;

						$delivery_forma = $primeiro_elemento_delivery;

						if ($delivery_forma == "DELIVERY") {
							$delivery = "DELIVERY";
						}

						// var_dump($delivery);

						if ($delivery_forma == "DELIVERY") {
							$delivery = "DELIVERY";
						}
						if ($pedido->fpagamento == "MESA") {
							$delivery = "MESA";
						}
						if ($pedido->fpagamento == "BALCAO") {
							$delivery = "BALCÃO";
						}


						?>
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

	<?php if (isset($_GET["erro"])) { ?>
		<div class="alert alert-warning" role="alert">
			<i class="fa fa-asterisk" aria-hidden="true"></i> Erro.
		</div>
	<?php } ?>
	<?php if (isset($_GET["ok"])) { ?>
		<div class="alert alert-success" role="alert">
			<i class="fa fa-thumbs-o-up" aria-hidden="true"></i> Sucesso.
		</div>
	<?php } ?>

	<div class="slim-mainpanel">
		<div class="container">
			<div class="row mg-t-10">


				<?php if ($status == "Finalizado") { ?>
					<div class="col-md-6">
						<div class="card card-people-list pd-15 mg-b-10">
							<label class="section-title" style="margin-top:-1px"><i class="fa fa-check-square-o" aria-hidden="true"></i> ALTERAR STATUS DO PEDIDO</label>
							<hr>
							<h1 class="text-success">Pedido Finalizado...</h1>
						</div>

						<div class="card card-people-list pd-15 mg-b-10">
							<div class="card card-people-list pd-15 mg-b-10">
								<h1 class="text-info" style="font-size:1.5rem;">Acessos <i class="fa fa-users" aria-hidden="true"></i></h1>
							</div>
							<hr>
							<div style="display:flex; width:100%;margin-bottom:0.6rem;gap:0.5rem">
								<!-- Card para comissão ativa -->
								<div class="card card-people-list w-100 border border-success">
									<div class="card-header text-center">
										Funcionário que Abriu
									</div>
									<hr>
									<div class="card-content font-weight-bold">
										<?php
										echo !empty($atendente_criador_pedido) ? $atendente_criador_pedido : "Não Registrado"
										?>
									</div>
								</div>
								<!-- Card para total de comissão -->
								<div class="card card-people-list w-100 border border-danger">
									<div class="card-header text-center">
										Funcionário que Fechou
									</div>
									<hr>
									<div class="card-content font-weight-bold">
										<?php
										echo !empty($atendente_fechador_pedido) ? $atendente_fechador_pedido : "Não Registrado"
										?>
									</div>
								</div>
							</div>
						</div>


						<div class="card card-people-list pd-15 mg-b-10">
							<label class="section-title" style="margin-top:-1px"><i class="fa fa-chain" aria-hidden="true"></i> PAGAMENTOS ENCONTRADOS</label>
							<hr>

							<?php
							if (!function_exists('truncarTexto')) {
								/**
								 * Trunca o texto após o primeiro espaço e adiciona '...' se o texto for maior que o comprimento mínimo.
								 *
								 * @param string $texto O texto a ser truncado.
								 * @param int $tamanho Máximo comprimento do texto antes do truncamento.
								 * @param int $comprimento_minimo Comprimento mínimo para truncar o texto.
								 * @return string Texto truncado com '...' adicionado após o primeiro espaço.
								 */
								function truncarTexto($texto, $tamanho = 50, $comprimento_minimo = 5)
								{
									if (strlen($texto) > $comprimento_minimo) {
										if (strlen($texto) > $tamanho) {
											$pos_espaco = strpos($texto, ' ', $tamanho);
											if ($pos_espaco !== false) {
												$texto = substr($texto, 0, $pos_espaco) . '...';
											} else {
												$texto = substr($texto, 0, $tamanho) . '...';
											}
										}
									}
									return $texto;
								}
							}

							?>

							<?php

							// Define a consulta
							$query = "SELECT * FROM `registrospagamentos` WHERE `idpedido` = :idpedido";

							// Prepara a consulta
							$stmt = $connect->prepare($query);
							$stmt->bindParam(':idpedido', $codigop, PDO::PARAM_INT);

							// Executa a consulta
							$stmt->execute();

							// Obtém os resultados
							$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

							// Verifica se há resultados
							if (count($results) > 0) {
								echo '<div class="row mg-t-10">';
								echo '<table class="table gap-1">';
								echo '<thead>';
								echo '<tr>
									<th>ID</th>
									<th>Cliente</th>
									<th>Data</th>
									<th>Status</th>
									<th></th>
								</tr>';
								echo '</thead>';
								echo '<tbody>';


								// Loop pelos resultados
								foreach ($results as $row) {

									$nome_search = $row['nome'];
									$texto_truncado_nome = truncarTexto($nome_search, 9, 5);

									$data_search = $row['data_registro'];
									$texto_truncado_data = truncarTexto($data_search, 5, 5);


									echo '<tr>';
									echo '<td>' . htmlspecialchars($row['idpedido']) . '</td>';
									echo '<td title=' . htmlspecialchars($nome_search) . '>' . htmlspecialchars($texto_truncado_nome) . '</td>';
									echo '<td title=' . htmlspecialchars($data_search) . '>' . htmlspecialchars($texto_truncado_data) . '</td>';
									echo '<td class="bg-success text-light text-center">' . htmlspecialchars('pago') . '</td>';
									echo '<td class="text-info text-center bg-info"><form action="./historicopagamento.php" method="post">
								<input type="hidden" name="id_pedido" value="' . htmlspecialchars($row['idpedido']) . '" />
								<button style="cursor: pointer;" type="submit" class="btn btn-info w-100 btn-sm"><i class="fa fa-search"></i></button>
								</form></td>';

									echo '</tr>';
								}

								echo '</tbody>';
								echo '</table>';
								echo '</div>';
							} else {
								echo '<div class="row mg-t-10 p-4">';
								echo '<p>Nenhum histórico de pagamento encontrado para o pedido.</p>';
								echo '</div>';
							}

							?>

						</div>
					</div>
				<?php } else { ?>

					<!-- Verificar se status parcial do pedido existe ou seja é pedido parcial -->
					<?php if (!isset($status_parcial) || empty($status_parcial)) { ?>

						<!-- Verificar se o Status é igual a 1 pq é pedido novo -->
						<?php if ($pedido->status == 1) { ?>
							<div class="col-md-6">
								<div class="card card-people-list pd-15 mg-b-10">
									<label class="section-title" style="margin-top:-1px"><i class="fa fa-check-square-o" aria-hidden="true"></i> ALTERAR STATUS DO PEDIDO</label>
									<hr>
									<p style="border:1px solid #ccc;padding:1rem; margin-bottom:1rem;">Para Iniciar o Atendimento é necessário que você aceite o pedido. ✅</p>
									<div>
										<div class="d-flex w-100" style="justify-content:center;">
											<form action="" method="post" class="d-flex w-100" style="justify-content:center;">
												<input type="hidden" name="andamento" value="<?= $codigop; ?>">
												<input type="hidden" id="celular" value="<?= $celcli; ?>">
												<input type="hidden" id="mensagem" value="<?= $msg1; ?>">
												<button type="submit" class="btn btn-warning w-100" onClick="<?php echo $delivery == "DELIVERY" ? "enviarMensagem()" : "" ?>">Aceitar Pedido</button>
											</form>
										</div>

									</div>
								</div>
							</div>

						<?php } else if ($pedido->status > 1 && $pedido->status != 6) { ?>
							<!-- se o status de pedido geral for maior que 1 ou seja ele tá disponivel e não pode ser cancelado -->
							<div class="col-md-6">
								<div class="card card-people-list pd-15 mg-b-10">
									<label class="section-title" style="margin-top:-1px"><i class="fa fa-check-square-o" aria-hidden="true"></i> ALTERAR STATUS DO PEDIDO</label>
									<hr>

									<?php
									require_once("./motoboy.php");
									// var_dump(empty($motoboy_atual));
									?>
									<!-- Fazer atribuição do pedido ao motoboy -->
									<?php
									if ($delivery == "DELIVERY" && empty($motoboy_atual)) {
										echo $renderizacao;
									} else {
									?>

										<div class="row mg-t-10">
											<div class="col-md-4 mg-b-10">
												<!-- <form action="" method="post">
												<input type="hidden" name="andamento" value="<?= $codigop; ?>">
												<input type="hidden" id="celular" value="<?= $celcli; ?>">
												<input type="hidden" id="mensagem" value="<?= $msg1; ?>">
												<button type="submit" class="btn btn-warning btn-block" onClick="enviarMensagem()">Aceitar Pedido</button>
											</form> -->
												<form>

													<button class="btn btn-warning btn-block" disabled>Pedido já Aceito</button>
												</form>
											</div>
											<div class="col-md-4 mg-b-10">
												<form action="" method="post">
													<input type="hidden" name="entrega" value="<?= $codigop; ?>">
													<input type="hidden" id="celular2" value="<?= $celcli; ?>">
													<input type="hidden" id="mensagem2" value="<?= $msg2; ?>">
													<button <?php echo $delivery == "DELIVERY" ? "" : "disabled" ?> type="<?php echo $delivery == "DELIVERY" ? "submit" : "button" ?>" class="btn btn-success btn-block" onclick="<?php echo $delivery == "DELIVERY" ? "enviarMensagem2()" : "alert('Você não tem Autorização')" ?>">Saiu para entrega</button>
												</form>


											</div>
											<div class="col-md-4 mg-b-10">
												<form action="" method="post">
													<input type="hidden" name="retirada" value="<?= $codigop; ?>">
													<input type="hidden" id="celular3" value="<?= $celcli; ?>">
													<input type="hidden" id="mensagem3" value="<?php echo $msg3; ?>">
													<button <?php echo $delivery == "DELIVERY" ? "" : "disabled" ?> type="<?php echo $delivery == "DELIVERY" ? "submit" : "button" ?>" class="btn btn-success btn-block" onclick="<?php echo $delivery == "DELIVERY" ? "enviarMensagem3()" : "alert('Você não tem Autorização')" ?>">Disp. para retirada</button>
												</form>
											</div>
										</div>

										<div class="row">
											<div class="col-md-4">
												<!-- <form action="" method="post">
												<input type="hidden" name="finalizado" value="<?= $codigop; ?>">
												<input type="hidden" id="celular4" value="<?= $celcli; ?>">
												<input type="hidden" id="mensagem4" value="<?php echo $msg4; ?>">
												<button type="submit" class="btn btn-purple btn-block" onClick="enviarMensagem4()">Finalizar</button>
											</form> -->

												<?php
												$nome = str_replace('%20', ' ', $pedido->nome);
												?>


												<button class="btn btn-purple btn-block" onclick="openModal()">Finalizar Pedido</button>



												<div id="modal_finalizacao" class="modal_finalizacao">
													<!-- Vai mostrar caso não tenha motoboy no pedido -->

													<?php

													if ($delivery == "DELIVERY" && empty($motoboy_atual)) {
														echo $renderizacao;
													} else {
													?>
														<div class="modal_finalizacao-content">
															<h4>
																<?php echo $nome_funcionario ?> Está Finalizado o Pedido 📦.
															</h4>
															<p style="border:1px solid #ccc;padding:1rem; margin-bottom:1rem;">Tem certeza que deseja finalizar este pedido? Isso liberará a mesa para outros pedidos. Esta ação é irreversível. Se deseja sinalizar o pedido como finalizado, significa que a mesa será liberada, mesmo que ainda esteja sendo utilizada por outras pessoas. ⚠️</p>
															<div class="modal_finalizacao-header">
																<span class="modal_finalizacao-close" onclick="closeModal()">&times;</span>
																<!--  se for igual a delivery ele não abre nem para mesa nem balcao -->
																<?php
																if ($delivery == "DELIVERY"):
																	echo $renderizacao;
																endif;
																?>
																<?php
																if ($delivery == "DELIVERY") {
																	echo "<h4>Finalizar Pedido</h4>";
																} else {
																	echo "<h2>Finalizar Pedido</h2>";
																}
																?>




															</div>
															<div class="modal_finalizacao-body">
																<div class="info-container">
																	<?php if ($pedido->taxa > 0) { ?>
																		<p class="info-text"><span class="info-label">Taxa de Entrega: </span> R$: <?= formatMoedaBr(formatCurrency($pedido->taxa)) ?></p>
																		<p class="info-text"><span class="info-label">Total Geral: </span> R$: <?= formatMoedaBr(formatCurrency($pedido->vtotal)) ?></p>
																	<?php } else { ?>
																		<h4 class="total-text">Total: <?= formatMoedaBr(formatCurrency($pedido->vtotal)) ?></h4>
																	<?php } ?>
																</div>
																<div class="modal_finalizacao-tabs">
																	<div class="tab-buttons">
																		<button class="modal_finalizacao-tablink active" onclick="openTab(event, 'modal_finalizacao-simples')">Pagamento à vista</button>
																		<button class="modal_finalizacao-tablink" onclick="openTab(event, 'modal_finalizacao-rachadinha')">Pagamento rateado</button>
																		<button class="modal_finalizacao-tablink" onclick="openTab(event, 'modal_finalizacao-parcial')">Pagamento Parcial</button>
																	</div>
																</div>

																<!-- Metodo Simples -->
																<div id="modal_finalizacao-simples" class="modal_finalizacao-tabcontent" style="display: block;">
																	<?php require_once("./modal/modal_simples.php"); ?>
																</div>

																<!-- Método Rachadinha -->
																<div id="modal_finalizacao-rachadinha" class="modal_finalizacao-tabcontent">
																	<?php require_once("./modal/modal_rateada.php"); ?>
																</div>

																<!-- Método Parcial -->
																<div id="modal_finalizacao-parcial" class="modal_finalizacao-tabcontent">
																	<?php require_once("./modal/modal_parcial.php"); ?>
																</div>

															</div>
														</div>
													<?php
													};
													?>
												</div>


												<script>
													function openModal() {
														document.getElementById("modal_finalizacao").style.display = "flex";
													}

													function closeModal() {
														document.getElementById("modal_finalizacao").style.display = "none";
													}

													function openTab(evt, tabName) {
														var i, tabcontent, tablinks;
														tabcontent = document.getElementsByClassName("modal_finalizacao-tabcontent");
														for (i = 0; i < tabcontent.length; i++) {
															tabcontent[i].style.display = "none";
														}
														tablinks = document.getElementsByClassName("modal_finalizacao-tablink");
														for (i = 0; i < tablinks.length; i++) {
															tablinks[i].className = tablinks[i].className.replace(" active", "");
														}
														document.getElementById(tabName).style.display = "block";
														evt.currentTarget.className += " active";
													}
												</script>

											</div>
											<div class="col-md-4" align="center" style="font-size:20px; margin-top:5px;"><i class="fa fa-arrow-left mg-r-10" aria-hidden="true"></i><i class="fa fa-cutlery" aria-hidden="true"><i class="fa fa-arrow-right mg-l-10" aria-hidden="true"></i></i>
											</div>
											<div class="col-md-4">
												<form action="" method="post">
													<input type="hidden" name="cancelado" value="<?= $codigop; ?>">
													<input type="hidden" id="celular5" value="<?= $celcli; ?>">
													<input type="hidden" id="mensagem5" value="<?php echo $msg5; ?>">
													<button type="submit" class="btn btn-danger btn-block" onClick="<?php echo $delivery == "DELIVERY" ? "enviarMensagem()" : "" ?>">Cancelar</button>
												</form>
											</div>
										</div>

									<?php
									};
									?>
								</div>


								<div class="card card-people-list pd-15 mg-b-10">
									<div class="card card-people-list pd-15 mg-b-10">
										<h1 class="text-info" style="font-size:1.5rem;">Produtos Entregues</h1>
									</div>
									<hr>
									<div style="display:flex; width:100%;margin-bottom:0.6rem;gap:0.5rem">
										<?php
										require_once("./todoist.php");
										
										if (isset($cod_id)) {
										    $todoList = new TodoListComponent($connect, $cod_id, $pedido->idpedido);
										    $todoList->render();
										} else {
										    echo '<div class="alert alert-danger">Erro: Você não está autorizado a acessar esta página.</div>';
										}

										?>
									</div>
								</div>

								<div class="card card-people-list pd-15 mg-b-10">
									<div class="card card-people-list pd-15 mg-b-10">
										<h1 class="text-info" style="font-size:1.5rem;">Acessos <i class="fa fa-users" aria-hidden="true"></i></h1>
									</div>
									<hr>
									<div style="display:flex; width:100%;margin-bottom:0.6rem;gap:0.5rem">
										<!-- Card para comissão ativa -->
										<div class="card card-people-list w-100 border border-success">
											<div class="card-header text-center">
												Funcionário que Abriu
											</div>
											<hr>
											<div class="card-content font-weight-bold">
												<?php
												echo !empty($atendente_criador_pedido) ? $atendente_criador_pedido : "Não Registrado"
												?>
											</div>
										</div>
										<!-- Card para total de comissão -->
										<div class="card card-people-list w-100 border border-danger">
											<div class="card-header text-center">
												Funcionário que Fechou
											</div>
											<hr>
											<div class="card-content font-weight-bold">
												<?php
												echo !empty($atendente_fechador_pedido) ? $atendente_fechador_pedido : "Não Registrado"
												?>
											</div>
										</div>
									</div>
								</div>


								<div class="card card-people-list pd-15 mg-b-10">
									<div class="card card-people-list pd-15 mg-b-10">
										<h1 class="text-info" style="font-size:1.5rem;">Comissão</h1>
									</div>
									<div style="display:flex; width:100%;margin-bottom:0.6rem;gap:0.5rem">
										<!-- Calculo da Comissão -->
										<?php
										if ($comissao_ativa) {
										?>
											<!-- Card para comissão ativa -->
											<div class="card card-people-list w-100">
												<div class="card-header">
													Comissão Ativada
												</div>
												<hr>
												<div class="card-content">
													<?php if ($comissao_ativa): ?>
														<p><strong>Comissão Ativa para Este Pedido:</strong></p>
														<p><strong>Valor da Comissão:</strong> <?php echo number_format($comissao_ativa['comissao'], 2, ',', '.'); ?>%</p>

													<?php else: ?>
														<p>Nenhuma comissão ativa para este pedido.</p>
													<?php endif; ?>
												</div>
											</div>
											<!-- Card para total de comissão -->
											<div class="card card-people-list w-100">
												<div class="card-header">
													Total de Comissão
												</div>
												<hr>
												<div class="card-content">
													<?php if ($total_comissao): ?>
														<p class=""><strong>Calculo:</strong> R$ <?php echo number_format($total_comissao['total_comissao'] / 100, 2, ',', '.'); ?></p>
														<p class="bg-success p-3 text-white border-dark rounded-5"><strong>Total Acumulativo:</strong> R$ <?php echo number_format(($total_comissao['total_comissao'] / 100) * $pedido->vtotal, 2, ',', '.'); ?></p>
													<?php else: ?>
														<p>Sem total de comissão disponível.</p>
													<?php endif; ?>
												</div>
											</div>
										<?php
										};
										?>
									</div>
								</div>


								<div class="card card-people-list pd-15 mg-b-10">
									<div class="card card-people-list pd-15 mg-b-10">
										<h1 class="text-warning" style="font-size:1.5rem;">Ferramentas - Liberadas</h1>
									</div>
									<hr>
									<p>Próxima Atualização...</p>

								</div>

								<div class="card card-people-list pd-15 mg-b-10">
									<label class="section-title" style="margin-top:-1px"><i class="fa fa-chain" aria-hidden="true"></i> PAGAMENTOS ENCONTRADOS</label>
									<hr>


									<?php
									// Define a consulta
									$query = "SELECT * FROM `registrospagamentos` WHERE `idpedido` = :idpedido";

									// Prepara a consulta
									$stmt = $connect->prepare($query);
									$stmt->bindParam(':idpedido', $codigop, PDO::PARAM_INT);

									// Executa a consulta
									$stmt->execute();

									// Obtém os resultados
									$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

									// Verifica se há resultados
									if (count($results) > 0) {
										echo '<div class="row mg-t-10">';
										echo '<table class="table gap-1">';
										echo '<thead>';
										echo '<tr>
									<th>ID</th>
									<th>Cliente</th>
									<th>Data</th>
									<th>Status</th>
									<th></th>
								</tr>';
										echo '</thead>';
										echo '<tbody>';


										// Loop pelos resultados
										foreach ($results as $row) {

											$nome_search = $row['nome'];
											$texto_truncado_nome = truncarTexto($nome_search, 9, 5);

											$data_search = $row['data_registro'];
											$texto_truncado_data = truncarTexto($data_search, 5, 5);


											echo '<tr>';
											echo '<td>' . htmlspecialchars($row['idpedido']) . '</td>';
											echo '<td title=' . htmlspecialchars($nome_search) . '>' . htmlspecialchars($texto_truncado_nome) . '</td>';
											echo '<td title=' . htmlspecialchars($data_search) . '>' . htmlspecialchars($texto_truncado_data) . '</td>';
											echo '<td class="bg-success text-light text-center">' . htmlspecialchars('pago') . '</td>';
											echo '<td class="text-info text-center bg-info"><form action="./historicopagamento.php" method="post">
								<input type="hidden" name="id_pedido" value="' . htmlspecialchars($row['idpedido']) . '" />
								<button style="cursor: pointer;" type="submit" class="btn btn-info w-100 btn-sm"><i class="fa fa-search"></i></button>
								</form></td>';

											echo '</tr>';
										}

										echo '</tbody>';
										echo '</table>';
										echo '</div>';
									} else {
										echo '<div class="row mg-t-10 p-4">';
										echo '<p>Nenhum histórico de pagamento encontrado para o pedido.</p>';
										echo '</div>';
									}

									?>

								</div>

							</div>
						<?php } else if ($pedido->status == 6) {  ?>
							<!-- se o status de pedido geral for igual a 6 ele tá indisponivel -->
							<div class="col-md-6">

								<div class="card card-people-list pd-15 mg-b-10">
									<div class="card card-people-list pd-15 mg-b-10">
										<label class="section-title d-flex  w-100" style="align-items:center;gap:1rem;"><i class="fa fa-check-square-o" aria-hidden="true"></i> Acho que você quis <a href="pdvpedido.php?idpedido=<?= $id_pedido = rand(100000, 999999); ?>" class="btn btn-success btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> Criar novo Pedido</a> </label>
										<hr>
										<h1 class="text-danger">Pedido Cancelado...</h1>
									</div>
									<hr>
									<p class="d-flex  w-100" style="border:1px solid #ccc;padding:0.5rem; margin-bottom:1rem;gap:1rem;">Pedido Cancelado Registrar Motivo !!!❌</p>
								</div>
								<div class="card card-people-list pd-15 mg-b-10">
									<label class="section-title" style="margin-top:-1px"><i class="fa fa-chain" aria-hidden="true"></i> Registrar Motivo *</label>
									<hr>

									<?php
									require_once("./relatorios/relatorioPedido.php");


									if (isset($pedido->idpedido)) {
										$idpedido = intval($pedido->idpedido);
										echo relatorioPedido($idpedido, $connect);
									} else {
										echo "ID do pedido não especificado.";
									}
									?>

								</div>
							</div>
						<?php }; ?>

					<?php  } else if ($status_parcial === "1") { ?>
					<?php  } else if ($status_parcial === "2") { ?>
						<!-- Se o Status parcial existe ou seja o registro de pagamento  -->
						<div class="col-md-6">
							<div class="card card-people-list pd-15 mg-b-10">
								<label class="section-title" style="margin-top:-1px"><i class="fa fa-check-square-o" aria-hidden="true"></i> ALTERAR STATUS DO PEDIDO</label>
								<hr>
								<h1 class="text-warning">Complete o Pedido Parcial...</h1>
							</div>


							<div class="card card-people-list pd-15 mg-b-10">
								<label class="section-title" style="margin-top:-1px"><i class="fa fa-check-square-o" aria-hidden="true"></i> ALTERAR STATUS DO PEDIDO</label>
								<hr>
								<div class="row">
									<div class="col-md-4">

										<?php
										$nome = str_replace('%20', ' ', $pedido->nome);
										?>

										<button class="btn btn-purple btn-block" onclick="openModal()">Finalizar Pedido</button>

										<div id="modal_finalizacao" class="modal_finalizacao">

											<div class="modal_finalizacao-content">
												<p style="border:1px solid #ccc;padding:1rem; margin-bottom:1rem;">Tem certeza que deseja finalizar este pedido? Isso liberará a mesa para outros pedidos. Esta ação é irreversível. Se deseja sinalizar o pedido como finalizado, significa que a mesa será liberada, mesmo que ainda esteja sendo utilizada por outras pessoas. ⚠️</p>
												<div class="modal_finalizacao-header">
													<span class="modal_finalizacao-close" onclick="closeModal()">&times;</span>
													<h2>Finalizar Pedido Parcial</h2>
												</div>
												<div class="modal_finalizacao-body">
													<div class="info-container">
														<?php if ($pedido->taxa > 0) { ?>
															<p class="info-text"><span class="info-label">Taxa de Entrega: </span> R$: <?= formatMoedaBr(formatCurrency($pedido->taxa)) ?></p>
															<p class="info-text"><span class="info-label">Total Geral: </span> R$: <?= formatMoedaBr(formatCurrency($pedido->vtotal)) ?></p>
														<?php } else { ?>
															<div class="grapichsModalFat">


																<div class="grapichsModal">
																	<p>Total:<?= formatMoedaBr(formatCurrency($pedido->vtotal)) ?></p>
																</div>
																<div class="grapichsModal">
																	<p>Pago:<?= formatMoedaBr(formatCurrency($valor_total_pago == "0" ? $valor_total_pago : $valor_total_pago)) ?></p>
																</div>
																<div class="grapichsModal">
																	<p>Pendente:<?= formatMoedaBr(formatCurrency($valor_total_pago == "0" ? 0 : $pedido->vtotal - $valor_total_pago)) ?></p>
																</div>
															</div>
														<?php } ?>
													</div>
													<div class="modal_finalizacao-tabs">
														<div class="tab-buttons">

															<button class="modal_finalizacao-tablink" onclick="openTab(event, 'modal_finalizacao-parcial')">Pagamento Parcial</button>
														</div>
													</div>

													<!-- Método Parcial -->
													<div id="modal_finalizacao-parcial" class="modal_finalizacao-tabcontent" style="display: block;">
														<?php require_once("./modal/modal_parcial.php"); ?>
													</div>

												</div>
											</div>
										</div>


										<script>
											function openModal() {
												document.getElementById("modal_finalizacao").style.display = "flex";
											}

											function closeModal() {
												document.getElementById("modal_finalizacao").style.display = "none";
											}

											function openTab(evt, tabName) {
												var i, tabcontent, tablinks;
												tabcontent = document.getElementsByClassName("modal_finalizacao-tabcontent");
												for (i = 0; i < tabcontent.length; i++) {
													tabcontent[i].style.display = "none";
												}
												tablinks = document.getElementsByClassName("modal_finalizacao-tablink");
												for (i = 0; i < tablinks.length; i++) {
													tablinks[i].className = tablinks[i].className.replace(" active", "");
												}
												document.getElementById(tabName).style.display = "block";
												evt.currentTarget.className += " active";
											}
										</script>

									</div>
									<div class="col-md-4" align="center" style="font-size:20px; margin-top:5px;"><i class="fa fa-arrow-left mg-r-10" aria-hidden="true"></i><i class="fa fa-cutlery" aria-hidden="true"><i class="fa fa-arrow-right mg-l-10" aria-hidden="true"></i></i>
									</div>
									<div class="col-md-4">
										<form action="" method="post">

											<button type="button" class="btn btn-danger btn-block">Alterar Pedido</button>
										</form>
									</div>
								</div>
							</div>

							<div class="modal_finalizacao-header">
								<h4>Parcelas adicionadas...</h4>
							</div>

							<?php


							// Variáveis globais
							global $connect, $codigop, $pedido, $dados_pagamentos_json;

							// Função para processar a remoção
							function processarRemocao($dados_pagamentos_json, $index_remocao)
							{
								if (isset($dados_pagamentos_json['dados'][$index_remocao])) {
									unset($dados_pagamentos_json['dados'][$index_remocao]);
									$dados_pagamentos_json['dados'] = array_values($dados_pagamentos_json['dados']); // Reindexa o array
								}

								return $dados_pagamentos_json;
							}

							// Função para salvar os dados no banco de dados
							function salvarDadosNoBanco($idpedido, $dados_pagamentos_json)
							{
								global $connect;
								$dados_pagamentos_atualizados = json_encode($dados_pagamentos_json, JSON_UNESCAPED_UNICODE);

								$sql = "UPDATE registrospagamentos SET dados_pagamentos = :dados_pagamentos WHERE idpedido = :idpedido";
								$stmt = $connect->prepare($sql);
								$stmt->bindParam(':dados_pagamentos', $dados_pagamentos_atualizados);
								$stmt->bindParam(':idpedido', $idpedido);
								$stmt->execute();
							}

							// Verifica se o botão de remoção foi pressionado
							if (isset($_POST['remover'])) {
								$index_remocao = intval($_POST['remover']);
								$dados_pagamentos_json = processarRemocao($dados_pagamentos_json, $index_remocao);

								// Exemplo de idpedido (substitua conforme necessário)
								$idpedido = $pedido->idpedido; // substitua pelo idpedido correto
								salvarDadosNoBanco($idpedido, $dados_pagamentos_json);

								// Redireciona para a mesma página para atualizar a lista
								echo '<form id="reloadForm" method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '">';
								echo '<input type="hidden" name="codigop" value="' . htmlspecialchars($codigop) . '">';
								echo '</form>';
								echo '<script type="text/javascript">document.getElementById("reloadForm").submit();</script>';
								exit();
							}

							// Função para gerar HTML
							function gerarListaComRemocao($dados_pagamentos_json)
							{
								global $codigop;
								if (isset($dados_pagamentos_json['dados']) && is_array($dados_pagamentos_json['dados'])) {
									$html = '<form method="post" action="" style="max-width: 600px; margin: auto; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">';
									$html .= '<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">';
									$html .= '<thead><tr style="background-color: #f8f8f8;"><th style="padding: 10px; border: 1px solid #ddd;">Método</th><th style="padding: 10px; border: 1px solid #ddd;">Quantidade</th><th style="padding: 10px; border: 1px solid #ddd;">Ação</th></tr></thead>';
									$html .= '<tbody>';
									$html .= '<input type="hidden" name="codigop" value="' . htmlspecialchars($codigop) . '">';

									foreach ($dados_pagamentos_json['dados'] as $index => $pagamento) {
										$metodo = htmlspecialchars($pagamento['metodo']);
										$quantidade = htmlspecialchars($pagamento['quantidade']);
										$html .= '<tr>';
										$html .= '<td style="padding: 10px; border: 1px solid #ddd;">' . $metodo . '</td>';
										$html .= '<td style="padding: 10px; border: 1px solid #ddd;">R$ ' . $quantidade . '</td>';
										$html .= '<td style="padding: 10px; border: 1px solid #ddd;"><button type="submit" name="remover" value="' . $index . '" style="padding: 5px 10px; background-color: #ff4d4d; color: white; border: none; border-radius: 3px; cursor: pointer;">Remover</button></td>';
										$html .= '</tr>';
									}

									$html .= '</tbody>';
									$html .= '</table>';
									$html .= '</form>';

									return $html;
								} else {
									return 'Dados inválidos.';
								}
							}

							// Gera o HTML
							echo gerarListaComRemocao($dados_pagamentos_json);
							?>


						</div>
						<!-- Se o Status parcial 5 ou seja o registro de pagamento foi finalizado  -->
					<?php  } else if ($status_parcial === "5") { ?>
					<?php  } ?>

				<?php } ?>



				<div class="col-md-3">
					<center>
						<a href="#" class="btn btn-primary btn-block invoice-print" name="btnprint" onClick="PrintMe('print')"><i class="fa fa-print" aria-hidden="true"></i> Balcão</a>
					</center>
					<div class="card card-people-list pd-15 mg-b-10" style="background-color:#fdfbe3">

						<div id="print" style="font-family: Arial;">
							<center>
								<p class="tx-15"><strong>RESUMO DO PEDIDO</strong></p>
							</center>
							<center>
								<p class="tx-12">Comanda Balcão</span></p>
								<center>
									<p class="tx-12"><?= $pedido->data; ?> às <?= $pedido->hora; ?></span></p>
									<center>
										<p class="tx-12">Nº <?= $codigop; ?></span></p>
										<hr />
										<?php
										while ($carpro = $produtosca->fetch(PDO::FETCH_OBJ)) {
											$nomepro  = $connect->query("SELECT nome FROM produtos WHERE id = '" . $carpro->produto_id . "'");
											$nomeprox = $nomepro->fetch(PDO::FETCH_OBJ);
											// var_dump($carpro->pedido_entregue);
											
										?>
								
											<p style="margin-left:10px;" align="left"><span class="tx-12"><b>** Item: </b><?php print $nomeprox->nome; ?> <?php echo isset($carpro->pedido_entregue) ? ($carpro->pedido_entregue == "sim" ? " ✔" : "") : null; ?></span></p>

											<?php if ($carpro->tamanho != "N") { ?>
												<p style="margin-left:10px;" align="left"><span class="tx-12"><b>- Tamanho: </b><?php print $carpro->tamanho; ?></span></p>
											<?php } ?>

											<p style="margin-left:10px;" align="left"><span class="tx-12"><b>- Qnt:</b> <?php print $carpro->quantidade; ?></span></p>

											<p style="margin-left:10px;" align="left"><span class="tx-12"><b>- V. Unitário:</b> <?php echo "R$: " . $carpro->valor; ?></span></p>

											<?php if ($carpro->obs) { ?>
												<p style="margin-left:10px;" align="left"><span class="tx-12"><b>- Obs:</b> <?php echo $carpro->obs; ?></span></p>
											<?php } else { ?>
												<p style="margin-left:10px;" align="left"><span class="tx-12"><b>- Obs:</b> Não</span></p>
											<?php } ?>

											<?php
											$meiom  = $connect->query("SELECT * FROM store_o WHERE idp = '" . $carpro->idpedido . "' AND status = '1' AND idu='$cod_id' AND meioameio='1' AND id_referencia='" . $carpro->referencia . "' ");
											$meiomc = $meiom->rowCount();
											?>

											<?php if ($meiomc > 0) { ?>
												<p style="margin-left:10px;" align="left"><span class="tx-12"><b>* <?= $meiomc; ?> Sabores:</b></span></p>
												<p style="margin-left:10px;" align="left"><span class="tx-12">
														<?php while ($meiomv = $meiom->fetch(PDO::FETCH_OBJ)) { ?>
															<?= $meiomv->nome . "<br>"; ?>
														<?php } ?>
													</span></p>
											<?php } ?>

											<?php
											$adcionais  = $connect->query("SELECT * FROM store_o WHERE idp = '" . $carpro->idpedido . "' AND status = '1' AND idu='$cod_id' AND meioameio='0' AND id_referencia='$carpro->referencia'");
											$adcionaisc = $adcionais->rowCount();
											?>

											<?php if ($adcionaisc > 0) { ?>
												<p style="margin-left:10px;" align="left"><span class="tx-12"><b>* Adicionais/Ingredientes:</b></p>
												<p style="margin-left:10px;" align="left"><span class="tx-12">
														<?php while ($adcionaisv = $adcionais->fetch(PDO::FETCH_OBJ)) { ?>
															<?= "-  R$: " . $adcionaisv->valor . " | " . $adcionaisv->nome . "<br>"; ?>
														<?php } ?>
													</span></p>
											<?php } ?>
											<center>=========================</center>
											</p>
										<?php } ?>

										<?php
										$nome = str_replace('%20', ' ', $pedido->nome);



										// Decodifica o JSON para um array PHP e acessa o primeiro elemento
										$primeiro_elemento_delivery  = (is_array($delivery_array = json_decode($pedido->fpagamento, true)) && !empty($delivery_array))
											? $delivery_array[0]
											: null;

										$delivery = $primeiro_elemento_delivery;


										?>

										<br>
										<center><strong>DADOS DO CLIENTE</strong></center>
										<hr />
										<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Nome: </b><?= $nome; ?></span></p>
										<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Celular: </b><?= $pedido->celular; ?></span></p>
										<?php if ($pedido->mesa > 0) { ?>
											<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Mesa: </b><?= $pedido->mesa; ?></span></p>
											<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Pessoa na Mesa: </b><?= $pedido->pessoas; ?></span></p>
											<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Obs: </b><?= $pedido->obs; ?></span></p>
										<?php } ?>
										<?php if ($delivery == "DELIVERY" && isset($delivery)) { ?>
											<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Rua: </b><?= $pedido->rua; ?></span></p>
											<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Nº: </b><?= $pedido->numero; ?></span></p>
											<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Bairro: </b><?= $pedido->bairro; ?></span></p>
											<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Complemento: </b><?= $pedido->complemento; ?></span></p>
										<?php } ?>
										<br>
										<center><strong>PAGAMENTO</strong></center>
										<hr />
										<?php

										$segundo_elemento_delivery  = (is_array($delivery_array = json_decode($pedido->fpagamento, true)) && !empty($delivery_array))
											? $delivery_array[1]
											: null;



										if (isset($segundo_elemento_delivery) && !empty($segundo_elemento_delivery)) {
											print $delivery = "<p style=\"margin-left:10px;\" align=\"left\"><span class=\"tx-12\"><b>Pagou na Forma: " . $segundo_elemento_delivery . "</b></span></p>";
										}
										if ($pedido->fpagamento == "MESA") {
											print $delivery = "<p style=\"margin-left:10px;\" align=\"left\"><span class=\"tx-12\"><b>Na Mesa</b></span></p>";
										}
										if ($pedido->fpagamento == "BALCAO") {
											print $delivery = "<p style=\"margin-left:10px;\" align=\"left\"><span class=\"tx-12\"><b>No Balcão</b></span></p>";
										}
										?>
										<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Subtotal: R$: </b><?= formatMoedaBr(formatCurrency($pedido->vsubtotal)) ?></span></p>
										<?php if ($pedido->vadcionais > 0.00) { ?>
											<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Adicionais: R$: </b><?= formatMoedaBr(formatCurrency($pedido->vadcionais)) ?></span></p>
										<?php } ?>
										<?php if ($pedido->taxa > 0) { ?>
											<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Taxa de Entrega: R$: </b><?= formatMoedaBr(formatCurrency($pedido->taxa)) ?></span></p>
											<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Total Geral: </b> R$: <?= formatMoedaBr(formatCurrency(($pedido->vtotal))) ?></span></p>
										<?php } else { ?>
											<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Total Geral: </b> R$: <?= formatMoedaBr(formatCurrency($pedido->vtotal)) ?></span></p>
										<?php } ?>
										<?php if ($pedido->troco > 0) { ?>
											<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Troco para: R$: </b><?= formatMoedaBr(formatCurrency($pedido->troco)) ?></span></p>
											<?php $ValorDoTroco = $pedido->troco - $pedido->vtotal ?>
											<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Valor do Troco: R$: </b><?= formatMoedaBr(formatCurrency($ValorDoTroco)) ?></span></p>
										<?php } ?>
										<br>



										<?php
										// Função para truncar texto
										function truncarTexto($texto, $limite, $quebra = true)
										{
											$tamanho = strlen($texto);
											if ($tamanho <= $limite) {
												$novoTexto = $texto;
											} else {
												if ($quebra) {
													$novoTexto = trim(substr($texto, 0, $limite)) . '...';
												} else {
													$ultimoEspaco = strrpos(substr($texto, 0, $limite), ' ');
													$novoTexto = trim(substr($texto, 0, $ultimoEspaco)) . '...';
												}
											}
											return $novoTexto;
										}

										// Define a consulta
										$query = "SELECT * FROM `registrospagamentos` WHERE `idpedido` = :idpedido";
										// Prepara a consulta
										$stmt = $connect->prepare($query);
										$stmt->bindParam(':idpedido', $codigop, PDO::PARAM_INT);
										// Executa a consulta
										$stmt->execute();
										// Obtém os resultados
										$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

										// Verifica se há resultados
										if (count($results) > 0) {
											// Loop pelos resultados
											foreach ($results as $row) {
												$nome_search = $row['nome'];
												$texto_truncado_nome = truncarTexto($nome_search, 9, 5);

												$data_search = $row['data_registro'];
												$texto_truncado_data = truncarTexto($data_search, 5, 5);

												$jsonPagamento = $row['dados_pagamentos'];

												if (!empty($jsonPagamento)) {
													$pagamentoArray = json_decode($jsonPagamento, true);

													// if (json_last_error() === JSON_ERROR_NONE && isset($pagamentoArray['dados']) && !empty($pagamentoArray['dados'])) {

													// 	echo '
													// 		<hr>
													// 		<br>
													// 		<center><strong>PEDIDO PAGO</strong></center>
													// 		';

													// 	echo '<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Formas de Pagamento:</b></span></p>';

													// 	foreach ($pagamentoArray['dados'] as $pagamento) {
													// 		echo '<p style="margin-left:10px;" align="left">' . '<strong class="tx-12">' . $pagamento['metodo'] . ': R$: </strong>' . ' <span>' . formatMoedaBr(formatCurrency($pagamento['quantidade'])) . '</span>' . '</p>';
													// 	}

													// 	echo '<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Tipo de Pagamento: </b>';
													// 	echo $pagamentoArray['tipo'];
													// 	echo '</span></p>';
													// } else {
													// 	echo '<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Erro ao decodificar os dados de pagamento.</b></span></p>';
													// }

													if (json_last_error() === JSON_ERROR_NONE && isset($pagamentoArray['dados']) && !empty($pagamentoArray['dados'])) {

														echo '
                                                        <hr>
                                                        <br>
                                                        <center><strong>PEDIDO PAGO</strong></center>';

														echo '<div style="margin-left:10px;">';
														// echo '<p class="tx-12" style="margin-left:0px;" align="left"><b>Formas de Pagamento:</b></p>';
														echo '<p style="margin-left:0px;" align="left"><span class="tx-12"><b>Tipo de Pagamento: </b>';
														echo htmlspecialchars($pagamentoArray['tipo']);
														echo '</span></p>';

														if ($pagamentoArray['tipo'] === "rateio") {
															echo '<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">';
															echo '<thead>';
															echo '<tr>';
															echo '<th>Método</th>';
															echo '<th>Quantidade</th>';
															echo '</tr>';
															echo '</thead>';
															echo '<tbody>';

															foreach ($pagamentoArray['dados'] as $pagamento) {
																$quantidadeLimpa = preg_replace('/[^0-9,\.]/', '', $pagamento['quantidade']);
																$quantidadeFloat = floatval(str_replace(',', '.', $quantidadeLimpa));

																// Formatar a quantidade como moeda
																$quantidadeFormatada = number_format($quantidadeFloat, 2, ',', '.');

																echo '<tr>';
																echo '<td>' . htmlspecialchars($pagamento['metodo']) . '</td>';
																echo '<td> R$:' . htmlspecialchars($quantidadeFormatada) . '</td>';
																echo '</tr>';
															}

															echo '</tbody>';
															echo '</table>';
															echo '</div>';
														} else if ($pagamentoArray['tipo'] == "a vista") {

															echo '<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">';
															echo '<thead>';
															echo '<tr>';
															echo '<th>Método</th>';
															echo '<th>Quantidade</th>';
															echo '</tr>';
															echo '</thead>';
															echo '<tbody>';


															foreach ($pagamentoArray['dados'] as $pagamento) {
																$quantidadeLimpa = preg_replace('/[^0-9,\.]/', '', $pagamento['quantidade']);
																$quantidadeFloat = floatval(str_replace(',', '.', $quantidadeLimpa));

																// Formatar a quantidade como moeda
																$quantidadeFormatada = number_format($quantidadeFloat, 2, ',', '.');

																echo '<tr>';
																echo '<td>' . htmlspecialchars($pagamento['metodo']) . '</td>';
																echo '<td> R$:' . htmlspecialchars($quantidadeFormatada) . '</td>';
																echo '</tr>';
															}

															echo '</tbody>';
															echo '</table>';
															echo '</div>';
														} else if ($pagamentoArray['tipo'] == "parcial") {

															echo '<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">';
															echo '<thead>';
															echo '<tr>';
															echo '<th>Método</th>';
															echo '<th>Quantidade</th>';
															echo '</tr>';
															echo '</thead>';
															echo '<tbody>';


															foreach ($pagamentoArray['dados'] as $pagamento) {
																// Remover caracteres não numéricos da quantidade e converter para float
																$quantidadeLimpa = preg_replace('/[^0-9,\.]/', '', $pagamento['quantidade']);
																$quantidadeFloat = floatval(str_replace(',', '.', $quantidadeLimpa));

																// Formatar a quantidade como moeda
																$quantidadeFormatada = number_format($quantidadeFloat, 2, ',', '.');

																echo '<tr>';
																echo '<td>' . htmlspecialchars($pagamento['metodo']) . '</td>';
																echo '<td> R$ ' . htmlspecialchars($quantidadeFormatada) . '</td>';
																echo '</tr>';
															}


															echo '</tbody>';
															echo '</table>';
															echo '</div>';
														}
													} else {
														echo '<p style="margin-left:0px;" align="left"><span class="tx-12"><b>Erro ao decodificar os dados de pagamento.</b></span></p>';
													}
												} else {
													echo '<p>Pagamento não Reconhecido...</p>';
												}
											}

											// if ($row['valor_dinheiro'] && $row['valor_troco']) {

											// 	echo '<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Valor em Dinheiro: R$: </b>';
											// 	echo formatMoedaBr(formatCurrency($row['valor_dinheiro']));

											// 	echo '</span></p>';

											// 	echo '<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Valor Troco: R$: </b>';
											// 	echo formatMoedaBr(formatCurrency($row['valor_troco']));
											// 	echo '</span></p>';
											// }

											if ($row['valor_dinheiro'] && $row['valor_troco']) {

												echo '<div style="margin-left:10px;margin-top:1rem;">';
												echo '<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">';
												echo '<thead>';
												echo '<tr>';
												echo '<th>Caixa Recebido</th>';
												echo '<th>Troco</th>';
												echo '</tr>';
												echo '</thead>';
												echo '<tbody>';



												$quantidadeLimpa = preg_replace('/[^0-9,\.]/', '', $pagamento['quantidade']);
												$quantidadeFloat = floatval(str_replace(',', '.', $quantidadeLimpa));

												// Formatar a quantidade como moeda
												$quantidadeFormatadaDinheiro = number_format($quantidadeFloat, 2, ',', '.');

												echo '<tr>';
												echo '<td> R$:' . htmlspecialchars($quantidadeFormatadaDinheiro) . '</td>';
												echo '<td> R$:' . htmlspecialchars(number_format($row['valor_troco'], 2, ',', '.')) . '</td>';
												echo '</tr>';

												echo '</tbody>';
												echo '</table>';
												echo '</div>';
											}
										} else {
											echo '<p><strong>Nenhum histórico de pagamento encontrado para o pedido.</strong></p> <br>';
										}
										?>

										<p style="margin-left:10px;" align="right"><span class="tx-11"><b><?= date("d-m-Y H:i:s"); ?></b></span></p>
									</center>
								</center>
						</div>
					</div>
				</div>

				<div class="col-md-3">

					<center>
						<a href="#" class="btn btn-info btn-block invoice-print" name="btnprint" onClick="PrintMe2('print2')"><i class="fa fa-print" aria-hidden="true"></i> Cozinha</a>
					</center>

					<div class="card card-people-list pd-15 mg-b-10" style="background-color:#fdfbe3">

						<div id="print2" style="font-family: Arial;">

							<center>
								<p class="tx-15"><strong>RESUMO DO PEDIDO</strong></p>
							</center>
							<center>
								<p class="tx-12">Comanda Cozinha</p>
							</center>
							<center>
								<p class="tx-12"><?= $pedido->data; ?> às <?= $pedido->hora; ?></span></p>
								<center>
									<p class="tx-12">Nº <?= $codigop; ?></p>
								</center>
								<hr />

								<?php
								while ($carpro2 = $produtoscaxy->fetch(PDO::FETCH_OBJ)) {
									$nomepro2  = $connect->query("SELECT nome FROM produtos WHERE id = '" . $carpro2->produto_id . "'");
									$nomeprox2 = $nomepro2->fetch(PDO::FETCH_OBJ);
								?>

									<p style="margin-left:10px;" align="left"><span class="tx-12"><b>** Item:</b> <?php print $nomeprox2->nome; ?></span></p>

									<?php if ($carpro2->tamanho != "N") { ?>

										<p style="margin-left:10px;" align="left"><span class="tx-12"><b>- Tamanho:</b> <?php print $carpro2->tamanho; ?></span></p>

									<?php } ?>

									<p style="margin-left:10px;" align="left"><span class="tx-12"><b>- Qnt:</b> <?php print $carpro2->quantidade; ?></span></p>

									<?php if ($carpro2->obs) { ?>
										<p style="margin-left:10px;" align="left"><span class="tx-12"><b>- Obs:</b> <?php echo $carpro2->obs; ?></span></p>
									<?php } else { ?>
										<p style="margin-left:10px;" align="left"><span class="tx-12"><b>- Obs:</b> Não</span></p>
									<?php } ?>

									<?php
									$meiom2  = $connect->query("SELECT * FROM store_o WHERE idp = '" . $carpro2->idpedido . "' AND status = '1' AND idu='$cod_id' AND meioameio='1'AND id_referencia='" . $carpro2->referencia . "'");
									$meiomc2 = $meiom2->rowCount();
									?>

									<?php if ($meiomc2 > 0) { ?>
										<p style="margin-left:10px;" align="left"><span class="tx-12"><b>* <?= $meiomc2; ?> Sabores:</b></span></p>
										<p style="margin-left:10px;" align="left"><span class="tx-12">
												<?php while ($meiomv2 = $meiom2->fetch(PDO::FETCH_OBJ)) { ?>
													<?= $meiomv2->nome . "<br>"; ?>
												<?php } ?>
											</span></p>
									<?php } ?>

									<?php
									$adcionais2  = $connect->query("SELECT * FROM store_o WHERE idp = '" . $carpro2->idpedido . "' AND status = '1' AND idu='$cod_id' AND meioameio='0' AND id_referencia='$carpro2->referencia' ");
									$adcionaisc2 = $adcionais2->rowCount();
									// print $adcionaisc2;
									?>

									<?php if ($adcionaisc2 > 0) { ?>
										<p style="margin-left:10px;" align="left"><span class="tx-12"><b>* Adicionais/Ingredientes:</b></p>
										<p style="margin-left:10px;" align="left"><span class="tx-12">
												<?php while ($adcionaisv2 = $adcionais2->fetch(PDO::FETCH_OBJ)) { ?>
													<?= "- " . $adcionaisv2->nome . "<br>"; ?>
												<?php } ?>
											</span></p>
									<?php } ?>
									<center>=========================</center>
									</p>
								<?php } ?>

								<?php
								$nome = str_replace('%20', ' ', $pedido->nome);
								?>
								<br>
								<center><strong>DADOS DO CLIENTE</strong></center>
								<hr />
								<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Nome: </b><?= $nome; ?></span></p>
								<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Celular: </b><?= $pedido->celular; ?></span></p>
								<?php if ($pedido->mesa > 0) { ?>
									<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Mesa: </b><?= $pedido->mesa; ?></span></p>
									<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Pessoa na Mesa: </b><?= $pedido->pessoas; ?></span></p>
									<p style="margin-left:10px;" align="left"><span class="tx-12"><b>Obs: </b><?= $pedido->obs; ?></span></p>
								<?php } ?>
								<br>
								<p style="margin-left:10px;" align="right"><span class="tx-11"><b><?= date("d-m-Y H:i:s"); ?></b></span></p>
						</div>
					</div>
				</div>


			</div>
		</div>
		<!-- <script src="../lib/jquery/js/jquery.js"></script>
		<script src="../lib/bootstrap/js/bootstrap.js"></script>
		<script src="../js/moeda.js"></script> -->

		<script src="../lib/jquery/js/jquery.js"></script>
		<script src="../lib/datatables/js/jquery.dataTables.js"></script>
		<script src="../lib/datatables-responsive/js/dataTables.responsive.js"></script>
		<script src="../lib/select2/js/select2.min.js"></script>

		<!-- <script>
			$('.dinheiro').mask('#.##0,00', {
				reverse: true
			});
		</script> -->


		<script language="javascript">
			function PrintMe(DivID) {
				var disp_setting = "toolbar=yes,location=no,";
				disp_setting += "directories=yes,menubar=yes,";
				disp_setting += "scrollbars=yes,width=450, height=600, left=100, top=25";
				var content_vlue = document.getElementById(DivID).innerHTML;
				var docprint = window.open("", "", disp_setting);
				docprint.document.open();
				docprint.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"');
				docprint.document.write('"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">');
				docprint.document.write('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">');
				docprint.document.write('<head><title>COMANDA BALCAO</title>');
				docprint.document.write('<style type="text/css">body{ margin:0px;');
				docprint.document.write('font-family:verdana,Arial;color:#000;');
				docprint.document.write('font-family:Verdana, Geneva, sans-serif; font-size:12px;}');
				docprint.document.write('a{color:#000;text-decoration:none;} </style>');
				docprint.document.write('</head><body onLoad="self.print()">');
				docprint.document.write(content_vlue);
				docprint.document.write('</body></html>');
				docprint.document.close();
				docprint.focus();
			}
		</script>

		<script language="javascript">
			function PrintMe2(DivID) {
				var disp_setting = "toolbar=yes,location=no,";
				disp_setting += "directories=yes,menubar=yes,";
				disp_setting += "scrollbars=yes,width=450, height=600, left=100, top=25";
				var content_vlue = document.getElementById(DivID).innerHTML;
				var docprint = window.open("", "", disp_setting);
				docprint.document.open();
				docprint.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"');
				docprint.document.write('"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">');
				docprint.document.write('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">');
				docprint.document.write('<head><title>COMANDA MESA</title>');
				docprint.document.write('<style type="text/css">body{ margin:0px;');
				docprint.document.write('font-family:verdana,Arial;color:#000;');
				docprint.document.write('font-family:Verdana, Geneva, sans-serif; font-size:12px;}');
				docprint.document.write('a{color:#000;text-decoration:none;} </style>');
				docprint.document.write('</head><body onLoad="self.print()"><center>');
				docprint.document.write(content_vlue);
				docprint.document.write('</center></body></html>');
				docprint.document.close();
				docprint.focus();
			}
		</script>
		<script type="text/javascript">
			function enviarMensagem() {
				var celular = document.querySelector("#celular").value;
				celular = celular.replace(/\D/g, '');
				if (celular.length < 13) {
					celular = "55" + celular;
				}
				var texto = document.querySelector("#mensagem").value;
				texto = window.encodeURIComponent(texto);
				window.open("https://api.whatsapp.com/send?phone=" + celular + "&text=" + texto, "_blank");
			}
		</script>
		<script type="text/javascript">
			function enviarMensagem2() {
				var celular = document.querySelector("#celular2").value;
				celular = celular.replace(/\D/g, '');
				if (celular.length < 13) {
					celular = "55" + celular;
				}
				var texto = document.querySelector("#mensagem2").value;
				texto = window.encodeURIComponent(texto);
				window.open("https://api.whatsapp.com/send?phone=" + celular + "&text=" + texto, "_blank");
			}
		</script>
		<script type="text/javascript">
			function enviarMensagem3() {
				var celular = document.querySelector("#celular3").value;
				celular = celular.replace(/\D/g, '');
				if (celular.length < 13) {
					celular = "55" + celular;
				}
				var texto = document.querySelector("#mensagem3").value;
				texto = window.encodeURIComponent(texto);
				window.open("https://api.whatsapp.com/send?phone=" + celular + "&text=" + texto, "_blank");
			}
		</script>
		<script type="text/javascript">
			function enviarMensagem4() {
				var celular = document.querySelector("#celular4").value;
				celular = celular.replace(/\D/g, '');
				if (celular.length < 13) {
					celular = "55" + celular;
				}
				var texto = document.querySelector("#mensagem4").value;
				texto = window.encodeURIComponent(texto);
				window.open("https://api.whatsapp.com/send?phone=" + celular + "&text=" + texto, "_blank");
			}
		</script>
		<script type="text/javascript">
			function enviarMensagem5() {
				var celular = document.querySelector("#celular5").value;
				celular = celular.replace(/\D/g, '');
				if (celular.length < 13) {
					celular = "55" + celular;
				}
				var texto = document.querySelector("#mensagem5").value;
				texto = window.encodeURIComponent(texto);
				window.open("https://api.whatsapp.com/send?phone=" + celular + "&text=" + texto, "_blank");
			}
		</script>
</body>

</html>