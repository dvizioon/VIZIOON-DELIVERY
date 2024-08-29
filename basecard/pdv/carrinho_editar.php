<?php
if (isset($_COOKIE['pdvx'])) {
	$cod_id = $_COOKIE['pdvx'];
} else {
	header("Location: sair.php");
	exit();
}

include_once('../../funcoes/Conexao.php');
include_once('../../funcoes/Key.php');

// Certifique-se de definir o id_cliente ou substitua com o valor correto
$id_cliente = isset($id_cliente) ? $id_cliente : ''; // Substitua por uma definição real ou obtenha de outro lugar

// Verifique se a variável $id_cliente está definida
if ($id_cliente) {
	$pedido = $connect->query("SELECT * FROM pedidos WHERE idpedido='$id_cliente'");
	$pedido    = $pedido->fetch(PDO::FETCH_OBJ);
	// Define a consulta
	$query_registrospagamentos = "SELECT * FROM `registrospagamentos` WHERE `idpedido` = :idpedido";

	// Prepara a consulta
	$stmt = $connect->prepare($query_registrospagamentos);
	$stmt->bindParam(':idpedido', $id_cliente, PDO::PARAM_INT);

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
		// exit();
	}
} else {
	echo "ID do cliente não definido.";
	exit();
}

$dados_pagamentos = isset($dados_pagamentos) ? $dados_pagamentos : '{"tipo":"parcial","dados":[]}';

$dados_pagamentos_json = json_decode($dados_pagamentos, true) ?: ['tipo' => 'parcial', 'dados' => []];
$total_quantidade_global = 0;

if (!empty($dados_pagamentos_json['dados'])) {
	foreach ($dados_pagamentos_json['dados'] as $pagamento) {
		$quantidade = str_replace(',', '.', $pagamento['quantidade']);
		$quantidade_float = floatval($quantidade);
		if ($quantidade_float > 0) {
			$total_quantidade_global += $quantidade_float;
		}
	}
	// echo "Soma Total das Quantidades: R$ " . number_format($total_quantidade_global, 2, ',', '.');
} else {
	// echo "Dados inválidos ou vazios.";
}

$total_global_soucer = isset($total_quantidade_global) ? $total_quantidade_global : 0; // Valor padrão se não estiver definido
// echo $total_global_soucer;
$valor_troco_soucer = isset($valor_troco) ? $valor_troco : 0; // Definir valor padrão se não estiver definido
$valor_total_pago = floatval($total_global_soucer) - floatval($valor_troco_soucer);
$valor_total_faltando = floatval($pedido->vtotal) - floatval($valor_total_pago);
// Exibe a soma total global
// echo $valor_total_pago;
// echo "\nSoma Total Global: R$ " . number_format($valor_total_faltando, 2, ',', '.');
$status_parcial = isset($status) ? $status : '';
// echo "\nStatus Parcial: " . $status_parcial;
?>


<div class="card card-people-list pd-15 mg-b-10">
	<div class="slim-card-title"><i class="fa fa-caret-right"></i> PEDIDOS</div>
	<?php
	if ($produtoscx > 0) { ?>
		<div align="center"><span class="tx-11">Comanda nº <?php print $id_cliente; ?></span></div>
	<?php } ?>
	<div class="media-list">
		<?php
		while ($carpro = $produtosca->fetch(PDO::FETCH_OBJ)) {
			$nomepro  = $connect->query("SELECT nome FROM produtos WHERE id = '" . $carpro->produto_id . "' AND idu='$idu'");
			$nomeprox = $nomepro->fetch(PDO::FETCH_OBJ);

			// var_dump($carpro->id);
		?>
			<div style="padding:0.3rem 0.1rem;border:1px solid #ccc; border-radius:0.5rem;"><span class="tx-12"><i class="fa fa-square tx-8 mg-r-5"></i> Ref: <strong><?php print $carpro->referencia; ?></strong></span></div>
			<div><span class="tx-12"><i class="fa fa-square tx-8 mg-r-5"></i> Ítem: <strong><?php print $nomeprox->nome; ?></strong></span></div>
			<?php if ($carpro->tamanho != "N") { ?>
				<div><span class="tx-12"><i class="fa fa-square tx-8 mg-r-5"></i> Tamanho: <strong><?php print $carpro->tamanho; ?></strong></span></div>
			<?php } ?>
			<div><span class="tx-12"><i class="fa fa-square tx-8 mg-r-5"></i> Qnt: <strong><?php print $carpro->quantidade; ?></strong></span></div>
			<div>
				<span class="tx-12">
					<?php echo "<i class=\"fa fa-square tx-8 mg-r-5\"></i> V. Unitário: <strong >R$: " . $carpro->valor . "</strong >"; ?>
				</span>
			</div>
			<div>
				<span class="tx-12">

					<?php
					$meiom  = $connect->query("SELECT * FROM store_o WHERE idp = '" . $carpro->idpedido . "' AND status = '1' AND idu='$idu' AND meioameio='1' AND id_referencia='$carpro->referencia'");
					$meiomc = $meiom->rowCount();
					?>
					<?php if ($meiomc > 0) { ?>
						<i class="fa fa-square tx-8 mg-r-5"></i> <?= $meiomc; ?> Sabores:<br><strong>
							<?php while ($meiomv = $meiom->fetch(PDO::FETCH_OBJ)) {
								echo "- " . $meiomv->nome . "<br>";
							} ?>
						</strong>
					<?php } ?>

					<?php
					$adcionais  = $connect->query("SELECT * FROM store_o WHERE idp = '" . $carpro->idpedido . "' AND status = '1' AND idu='$idu' AND meioameio='0' AND id_referencia='$carpro->referencia'");
					$adcionaisc = $adcionais->rowCount();

					?>
					<?php if ($adcionaisc > 0) { ?>
						<i class="fa fa-square tx-8 mg-r-5"></i> Adicionais:<br><strong>
							<?php while ($adcionaisv = $adcionais->fetch(PDO::FETCH_OBJ)) {
								echo "-  R$: " . $adcionaisv->valor . " | <strong>" . $adcionaisv->nome . "</strong><br>";
							} ?>
						</strong>
					<?php } ?>
				</span>
			</div>
			<div align="right">
				<a href="pdvpedidoeditar.php?apagaritem=<?= $carpro->idpedido; ?>&iditem=<?= $carpro->id; ?>" style="color:#CC0000">
					<i class="fa fa-minus-square mg-r-5 tx-danger tx-9"></i><span class="tx-12">Excluir</span> <!-- (<?= htmlspecialchars($carpro->id); ?>) Opcional ID Item -->
				</a>
			</div>

			<hr>
		<?php } ?>

		<?php if ($total_global_soucer !== 0) { ?>

			<div>
				<div class="row">
					<div class="col-6">Total Pago</div>
					<div class="col-6">R$: <?php if (isset($total_global_soucer)) {


												if ($total_global_soucer !== 0) {
													// Valor Subtraido
													echo $gx = number_format($valor_total_pago, 2, ',', '.');
												}
											} else {
												print "0,00";
											} ?></div>
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
				// Função para gerar HTML com estilo retro
				function gerarListaComRemocao($dados_pagamentos_json)
				{
					global $codigop;
					if (isset($dados_pagamentos_json['dados']) && is_array($dados_pagamentos_json['dados'])) {
						$html = '<form method="post" action="" style="max-width: 600px; margin: auto; padding: 20px; border: 2px solid #000; border-radius: 10px; background-color: #f4f4f4; font-family: \'Courier New\', Courier, monospace; color: #333;">';
						$html .= '<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; background-color: #e0e0e0;">';
						$html .= '<thead>Valores Descontados: <tr style="background-color: #c0c0c0;"><th style="padding: 10px; border: 1px solid #000; text-align: left;">Método</th><th style="padding: 10px; border: 1px solid #000; text-align: left;">Quantidade</th></tr></thead>';
						$html .= '<tbody>';
						$html .= '<input type="hidden" name="codigop" value="' . htmlspecialchars($codigop) . '">';

						foreach ($dados_pagamentos_json['dados'] as $index => $pagamento) {
							$metodo = htmlspecialchars($pagamento['metodo']);
							$quantidade = htmlspecialchars($pagamento['quantidade']);
							$html .= '<tr>';
							$html .= '<td style="padding: 10px; border: 1px solid #000;">' . $metodo . '</td>';
							$html .= '<td style="padding: 10px; border: 1px solid #000;">R$ ' . $quantidade . '</td>';
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

		<?php } ?>



		<?php if ($produtoscx >= 1) { ?>
	</div>
	<div class="row">
		<div class="col-6">SubTotal</div>
		<div class="col-6">R$: <?php if (isset($somando->soma)) {


									if ($total_global_soucer !== 0) {
										// Valor Subtraido
										$valor_total = floatval($somando->soma) - floatval($valor_total_pago);
										echo $gx = number_format($valor_total, 2, ',', '.');
									} else {
										echo number_format($somando->soma, 2, ',', ' ');
									}
								} else {
									print "0,00";
								} ?></div>
	</div>

	<div class="row mg-t-10">
		<div class="col-6">Adicionais</div>
		<div class="col-6">R$:
			<?php
			$opcionais  = $connect->query("SELECT valor, quantidade FROM store_o WHERE ids = '" . $id_cliente . "' AND status = '1' AND idu='$idu' AND meioameio='0'");
			$sumx = 0;
			while ($valork = $opcionais->fetch(PDO::FETCH_OBJ)) {
				$quantop = $valork->quantidade;
				$valorop = $valork->valor;
				$totais = $valorop * $quantop;
				$sumx += $totais;
			}
			echo $opctg = number_format($sumx, 2, ',', ' ');
			?>
			<input type="hidden" name="adcionais" class="form-control" value="<?php echo $sumx; ?>">
		</div>
	</div>

	<div class="row mg-t-10">
		<div class="col-6 tx-16"><strong>Total Geral</strong></div>
		<div class="col-6 tx-16"><strong>R$:
				<?php
				if (isset($somando->soma)) {
					$geral = $somando->soma + $sumx;

					if ($total_global_soucer !== 0) {
						// Valor Subtraído
						$valor_total = floatval($geral) - floatval($valor_total_pago);
						echo $gx = number_format($valor_total, 2, ',', '.');
					} else {
						echo $gx = number_format($geral, 2, ',', '.');
					}
				} else {
					print "0,00";
				}
				?>
			</strong></div>
	</div>

	<?php if ($geral > $dadosempresa->dfree) {
				$_SESSION["ent_gratis"] = "okg"; ?>
		<hr>
		<div class="alert alert-success" role="alert" style="margin-bottom:-5px">
			<strong class="tx-success"><i class="fa fa-thumbs-o-up mg-r-5"></i> Entrega Grátis.</strong>
		</div>
	<?php } else {
				unset($_SESSION['ent_gratis']);
			} ?>

	<div class="media-list">

		<?php
			// Query para Consulta de Dados do Pedido Atual
			$pedido_cliente_informacoes = $connect->query("SELECT * FROM pedidos WHERE idpedido='" . $id_cliente . "' ORDER BY id DESC");
			$buscar_pedido_cliente_informacoes = $pedido_cliente_informacoes->fetch(PDO::FETCH_OBJ);

			// Verificar se há resultados na consulta
			if (!$buscar_pedido_cliente_informacoes) {
				return; // Ou qualquer ação desejada quando não houver resultados
			}

			// Definir $pedidossx com os dados do pedido obtidos
			$pedidossx = $buscar_pedido_cliente_informacoes;

			$primeiro_elemento_delivery  = (is_array($delivery_array = json_decode($pedidossx->fpagamento, true)) && !empty($delivery_array))
				? $delivery_array[0]
				: null;

			// Lógica para definir $delivery baseado em $pedidossx->fpagamento


			if ($primeiro_elemento_delivery == "DELIVERY") {
				$delivery = "<span style=\"color:#FF0000\">DELIVERY</span>";
			}

			if ($pedidossx->fpagamento == "MESA") {
				$delivery = "MESA";
			}
			if ($pedidossx->fpagamento == "BALCAO") {
				$delivery = "BALCÃO";
			}

		?>

		<?php if ($delivery == "MESA" && $dadosempresa->mesa == 1) { ?>
			<label class="rdiobox ckbox-success mg-t-15">
				<a href="pdvmesaeditar.php?idpedido=<?= $id_cliente; ?>&tipo=mesa"><button class="btn btn-danger btn-block" name="cart">Pedido na Mesa <i class="fa fa-arrow-right mg-l-10"></i></button></a>
			</label>
		<?php } elseif ($delivery == "BALCÃO" && $dadosempresa->balcao == 1) { ?>
			<label class="rdiobox ckbox-success mg-t-15">
				<a href="pdvbalcaoeditar.php?idpedido=<?= $id_cliente; ?>&tipo=balcao"><button class="btn btn-purple btn-block" name="cart">Pedido no Balcão <i class="fa fa-arrow-right mg-l-10"></i></button></a>
			</label>
		<?php } elseif ($delivery == "<span style=\"color:#FF0000\">DELIVERY</span>" && $dadosempresa->delivery == 1) { ?>
			<label class="rdiobox ckbox-success">
				<a href="pdvdeliveryeditar.php?idpedido=<?= $id_cliente; ?>&tipo=delivery"><button class="btn btn-success btn-block" name="cart">Pedido Delivery <i class="fa fa-arrow-right mg-l-10"></i></button></a>
			</label>
		<?php } ?>

		<!-- Botão para mudar estilo de pedido -->
		<button class="btn btn-primary btn btn-block" id="btnChangeStyle">
			Mudar Pedido ?
		</button>

		<!-- Div para mostrar opções de estilo de pedido -->
		<div id="styleOptions" style="display: none;">
			<?php if ($delivery != "MESA" && $dadosempresa->mesa == 1) { ?>
				<label class="rdiobox ckbox-success mg-t-15">
					<a href="pdvmesaeditar.php?idpedido=<?= $id_cliente; ?>&tipo=mesa"><button class="btn btn-danger btn-block" name="cart">Pedido na Mesa <i class="fa fa-arrow-right mg-l-10"></i></button></a>
				</label>
			<?php } ?>
			<?php if ($delivery != "BALCÃO" && $dadosempresa->balcao == 1) { ?>
				<label class="rdiobox ckbox-success mg-t-15">
					<a href="pdvbalcaoeditar.php?idpedido=<?= $id_cliente; ?>&tipo=balcao"><button class="btn btn-purple btn-block" name="cart">Pedido no Balcão <i class="fa fa-arrow-right mg-l-10"></i></button></a>
				</label>
			<?php } ?>
			<?php if ($delivery != "<span style=\"color:#FF0000\">DELIVERY</span>" && $dadosempresa->delivery == 1) { ?>
				<label class="rdiobox ckbox-success">
					<a href="pdvdeliveryeditar.php?idpedido=<?= $id_cliente; ?>&tipo=delivery"><button class="btn btn-success btn-block" name="cart">Pedido Delivery <i class="fa fa-arrow-right mg-l-10"></i></button></a>
				</label>
			<?php } ?>
		</div>

		<script>
			// Script para mostrar/ocultar opções de estilo de pedido ao clicar no botão
			document.getElementById('btnChangeStyle').addEventListener('click', function() {
				var styleOptionsDiv = document.getElementById('styleOptions');
				if (styleOptionsDiv.style.display === 'none') {
					styleOptionsDiv.style.display = 'block';
				} else {
					styleOptionsDiv.style.display = 'none';
				}
			});
		</script>
		<hr>
	</div>


</div>
<?php } else { ?>
	<div align="center" style="margin-top:-10px; margin-bottom:10px;"><img src="../img/cart_off.png" style="width:55px; height:50px;" /><br>Carrinho Vazio</div>
<?php } ?>
</div>
</div>