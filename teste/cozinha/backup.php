<?php

if (isset($_COOKIE['pdvx'])) {

	$cod_id = $_COOKIE['pdvx'];
} else {

	header("location: sair.php");
}

include_once('../../funcoes/Conexao.php');

include_once('../../funcoes/Key.php');

// $update = $connect->query("UPDATE pedidos SET status='7' WHERE id='" . $_GET["confirmar"] . "'");
// header("location: tela.php");



// // Supondo que $connect seja sua conexão com o banco de dados
// $idpedido = isset($_GET["confirmar"]) ? $_GET["confirmar"] : null;

// if ($idpedido) {
//     // Query para selecionar todos os registros da tabela store e dados opcionais relacionados da tabela store_o
//     $query = "
//         SELECT 
//             s.id AS store_id, s.idpedido, s.valor, s.quantidade, s.tamanho, s.obs, s.pedido_entregue, p.nome AS produto_nome, 
//             o.id AS store_o_id, o.nome AS opcional_nome, o.valor AS opcional_valor, o.quantidade AS opcional_quantidade 
//         FROM 
//             store s
//         LEFT JOIN 
//             store_o o ON s.referencia = o.id_referencia
//         INNER JOIN 
//             produtos p ON s.produto_id = p.id
//         WHERE 
//             s.idpedido = :idpedido
//     ";

//     $stmt = $connect->prepare($query);
//     $stmt->bindParam(':idpedido', $idpedido, PDO::PARAM_STR);
//     $stmt->execute();

//     $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

//     // if ($resultados) {
//     //     foreach ($resultados as $resultado) {
//     //         echo "ID do Pedido: " . htmlspecialchars($resultado['idpedido']) . "<br>";
//     //         echo "Produto: " . htmlspecialchars($resultado['produto_nome']) . "<br>";
//     //         echo "Valor: R$ " . htmlspecialchars($resultado['valor']) . "<br>";
//     //         echo "Quantidade: " . htmlspecialchars($resultado['quantidade']) . "<br>";
//     //         echo "Tamanho: " . htmlspecialchars($resultado['tamanho']) . "<br>";
//     //         echo "Observação: " . htmlspecialchars($resultado['obs']) . "<br>";
//     //         echo "Pedido Entregue: " . htmlspecialchars($resultado['pedido_entregue']) . "<br>";

//     //         if (!empty($resultado['store_o_id'])) {
//     //             echo "-- Opcionais --<br>";
//     //             echo "Nome do Opcional: " . htmlspecialchars($resultado['opcional_nome']) . "<br>";
//     //             echo "Valor do Opcional: R$ " . htmlspecialchars($resultado['opcional_valor']) . "<br>";
//     //             echo "Quantidade do Opcional: " . htmlspecialchars($resultado['opcional_quantidade']) . "<br>";
//     //         }

//     //         echo "<br>";
//     //     }
//     // } else {
//     //     echo "Nenhum resultado encontrado para o ID do pedido: " . htmlspecialchars($idpedido);
//     // }



// 	// var_dump($cod_id);
// 	// var_dump($resultados);
// } 



$idpedido = isset($_GET["confirmar"]) ? $_GET["confirmar"] : null;
$idEntregar = isset($_GET["entregar"]) ? $_GET["entregar"] : null;

if ($idpedido) {
	// Verifica se o pedido já está na tabela `cozinha`
	$check_query = "
        SELECT COUNT(*) as count FROM cozinha WHERE idpedido = :idpedido
    ";
	$stmt_check = $connect->prepare($check_query);
	$stmt_check->bindParam(':idpedido', $idpedido, PDO::PARAM_STR);
	$stmt_check->execute();
	$count = $stmt_check->fetch(PDO::FETCH_ASSOC)['count'];

	if ($count == 0) {
		// Query para selecionar apenas um registro da tabela store (para fins de inserção única)
		$query = "
            SELECT 
                s.id AS store_id, s.idpedido
            FROM 
                store s
            WHERE 
                s.idpedido = :idpedido
            LIMIT 1
        ";

		$stmt = $connect->prepare($query);
		$stmt->bindParam(':idpedido', $idpedido, PDO::PARAM_STR);
		$stmt->execute();

		$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($resultado) {
			// Inserir apenas um registro na tabela `cozinha` com status "fazendo"
			$insert_query = "
                INSERT INTO cozinha (idu, idpedido, data, status_cozinha)
                VALUES (:idu, :idpedido, NOW(), 'fazendo')
            ";

			$stmt_insert = $connect->prepare($insert_query);
			$stmt_insert->bindParam(':idu', $resultado['store_id'], PDO::PARAM_INT);
			$stmt_insert->bindParam(':idpedido', $resultado['idpedido'], PDO::PARAM_STR);
			$stmt_insert->execute();

			// Remover o parâmetro 'confirmar' da URL após a inserção
			echo "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var url = new URL(window.location);
                        url.searchParams.delete('confirmar');
                        window.history.replaceState({}, document.title, url.toString());
                    });
                </script>";

			// echo "Pedido aceito e inserido na cozinha com sucesso!";
		} else {
			echo "Nenhum resultado encontrado para o ID do pedido: " . htmlspecialchars($idpedido);
		}
	} else {
		// Pedido já foi inserido na cozinha
		echo "O pedido já foi inserido na cozinha.";
	}
}

// Verificar se a ação é entregar itens específicos
if (isset($_POST['entregar_itens'])) {
	$idpedido = $_POST['idpedido'];
	$itens = isset($_POST['itens']) ? $_POST['itens'] : [];

	// Atualize o status dos itens selecionados como entregues
	if (!empty($itens)) {
		foreach ($itens as $item_id) {
			$stmt_update_store = $connect->prepare("UPDATE store SET pedido_entregue='sim' WHERE id=:id AND idsecao=:idpedido");
			$stmt_update_store->bindParam(':id', $item_id, PDO::PARAM_INT);
			$stmt_update_store->bindParam(':idpedido', $idpedido, PDO::PARAM_STR);
			$stmt_update_store->execute();
		}

		// Atualizar a tabela store_o para os itens selecionados
		$stmt_update_store_o = $connect->prepare("UPDATE store_o SET pedido_entregue='sim' WHERE id IN (" . implode(',', array_map('intval', $itens)) . ") AND idp=:idpedido");
		$stmt_update_store_o->bindParam(':idpedido', $idpedido, PDO::PARAM_STR);
		$stmt_update_store_o->execute();
	}

	// Redirecionar após a atualização
	header("Location: tela.php");
	exit();
}
// Verificar se a ação é entregar todos os itens
if (isset($_POST['entregar_tudo'])) {
	$idpedido = $_POST['idpedido'];

	// Atualizar todos os itens como entregues
	$stmt_update_store = $connect->prepare("UPDATE store SET pedido_entregue='sim' WHERE idpedido=:idpedido");
	$stmt_update_store->bindParam(':idpedido', $idpedido, PDO::PARAM_STR);
	$stmt_update_store->execute();

	$stmt_update_store_o = $connect->prepare("UPDATE store_o SET pedido_entregue='sim' WHERE idp=:idpedido");
	$stmt_update_store_o->bindParam(':idpedido', $idpedido, PDO::PARAM_STR);
	$stmt_update_store_o->execute();

	// Atualizar a tabela cozinha para refletir que o pedido foi entregue
	$stmt_update_cozinha = $connect->prepare("UPDATE cozinha SET status_cozinha='entregue' WHERE idpedido=:idpedido");
	$stmt_update_cozinha->bindParam(':idpedido', $idpedido, PDO::PARAM_STR);
	$stmt_update_cozinha->execute();

	// Atualizar o status do pedido para "entregue"
	$stmt_update_pedido = $connect->prepare("UPDATE pedidos SET status='7' WHERE idpedido=:idpedido");
	$stmt_update_pedido->bindParam(':idpedido', $idpedido, PDO::PARAM_INT);
	$stmt_update_pedido->execute();

	// Redirecionar após a atualização
	header("Location: tela.php");
	exit();
}
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
	<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"> -->

	<style>

	</style>


</head>

<body>

	<div class="slim-navbar">
		<div class="container">
			<ul class="nav">
				<li class="nav-item">
					<a class="nav-link" href="#">
						<i class="icon ion-ios-home-outline"></i>
						<span>RECEBIMENTO DE PEDIDOS</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#">
						<span>
							<div class="sk-wave">
								<div class="sk-rect sk-rect1 bg-gray-800"></div>
								<div class="sk-rect sk-rect2 bg-gray-800"></div>
								<div class="sk-rect sk-rect3 bg-gray-800"></div>
								<div class="sk-rect sk-rect4 bg-gray-800"></div>
								<div class="sk-rect sk-rect5 bg-gray-800"></div>
							</div>
						</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="sair.php">
						<i class="icon ion-ios-analytics-outline"></i>
						<span>SAIR</span>
					</a>
				</li>
			</ul>
		</div>
	</div>

	<div class="slim-mainpanel mt-3">
		<div class="m-3">
			<!-- Nav tabs -->
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="novos-tab" data-toggle="tab" href="#novos" role="tab" aria-controls="novos" aria-selected="true">Pedidos Novos</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="encerrados-tab" data-toggle="tab" href="#encerrados" role="tab" aria-controls="encerrados" aria-selected="false">Finalizados do Dia</a>
				</li>
			</ul>

			<!-- Tab content -->
			<div class="tab-content" id="myTabContent">



				<div class="tab-pane fade show active" id="novos" role="tabpanel" aria-labelledby="novos-tab">
					<div class="mg-t-20 container-content">
						<?php
						$dia = date("d-m-Y");

						// Pedidos novos
						$pedidos_novos = $connect->query("SELECT * FROM pedidos WHERE idu='" . $cod_id . "' AND data = '" . $dia . "' AND status='2' ORDER BY id ASC");
						while ($pedidossx = $pedidos_novos->fetch(PDO::FETCH_OBJ)) {
							$idpedido = isset($pedidossx->idpedido) ? $pedidossx->idpedido : null;
							if ($idpedido) {
								// Verificar se todos os itens do pedido foram entregues
								$total_itens = $connect->query("SELECT COUNT(*) as total FROM store WHERE idsecao = '$idpedido' AND status = '1'")->fetch(PDO::FETCH_OBJ)->total;
								$itens_entregues = $connect->query("SELECT COUNT(*) as entregues FROM store WHERE idsecao = '$idpedido' AND status = '1' AND pedido_entregue = 'sim'")->fetch(PDO::FETCH_OBJ)->entregues;

								if ($total_itens == $itens_entregues) {
									// Todos os itens foram entregues

									// Atualizar a tabela cozinha para refletir que o pedido foi entregue
									$stmt_update_cozinha = $connect->prepare("UPDATE cozinha SET status_cozinha='entregue' WHERE idpedido=:idpedido");
									$stmt_update_cozinha->bindParam(':idpedido', $idpedido, PDO::PARAM_STR);
									$stmt_update_cozinha->execute();

									// Atualizar o status do pedido para "entregue"
									$stmt_update_pedido = $connect->prepare("UPDATE pedidos SET status='7' WHERE idpedido=:idpedido");
									$stmt_update_pedido->bindParam(':idpedido', $idpedido, PDO::PARAM_INT);
									$stmt_update_pedido->execute();


									header("Location: tela.php");
									exit();

									echo '<div class="col-lg-12 mg-b-20">
											<div class="card card-info form-container">
												<div class="card-body">
													<div class="row">
														<div class="col-md-12 text-center">
															<span class="tx-18" style="color:#00CC00"><b>PEDIDO ' . htmlspecialchars($idpedido) . '</b></span><br>
															<span class="tx-13">Todos os itens foram entregues.</span>
														</div>
													</div>
												</div>
											</div>
										</div>';
								} else {
									// Mostrar itens que ainda não foram entregues
						?>
									<div class="card">
										<form action="tela.php" method="POST">
											<input type="hidden" name="idpedido" value="<?= htmlspecialchars($idpedido); ?>">
											<div class="col-lg-12 mg-b-20 w-100 h-100">
												<div class="w-100 h-100">
													<div class="card-body w-100 h-100">
														<div class="row">
															<div class="col-md-2 text-center ">
																<span class="tx-18" style="color:#00CC00"><b>PEDIDO</b></span><br>
																<span class="tx-13"><?= htmlspecialchars($pedidossx->idpedido); ?></span>
															</div>
															<div class="col-md-3">
																<span class="tx-12"><b>Cliente: </b><?= htmlspecialchars($pedidossx->nome); ?></span><br>
																<span class="tx-12"><b>Celular: </b><?= htmlspecialchars($pedidossx->celular); ?></span><br>
															</div>
															<div class="col-md-3">
																<span class="tx-12"><?= htmlspecialchars($pedidossx->data); ?></span><br>
																<span class="tx-12"><?= htmlspecialchars($pedidossx->hora); ?></span><br>
														</div>
															<div class="col-md-4">
																	<?php
																$check_query = "SELECT status_cozinha FROM cozinha WHERE idpedido = :idpedido LIMIT 1";
																$stmt_check = $connect->prepare($check_query);
																$stmt_check->bindParam(':idpedido', $idpedido, PDO::PARAM_STR);
																$stmt_check->execute();
																$cozinha_result = $stmt_check->fetch(PDO::FETCH_ASSOC);
																if ($cozinha_result) {
																	echo '<button type="submit" name="entregar_tudo" class="btn btn-danger w-100">ENTREGAR TUDO</button>';
																	echo '<hr>';
																	echo '<button type="submit" name="entregar_itens" class="btn btn-success w-100">ENTREGAR ITENS</button>';
																} else {
																	echo '<a href="tela.php?confirmar=' . htmlspecialchars($idpedido) . '">
                                                    <button type="button" class="btn btn-info">ACEITAR PEDIDO</button>
                                                </a>';
																}
																?>
															</div>
														</div>
														<hr />
														<div class="container-card-info">
															<?php
															// Consultar produtos que ainda não foram entregues
															$produtos = $connect->query("SELECT * FROM store WHERE idsecao = '$idpedido' AND status = '1' AND pedido_entregue = 'nao' ORDER BY id ASC");
															while ($carpro2 = $produtos->fetch(PDO::FETCH_OBJ)) {
																$nomepro2 = $connect->query("SELECT nome FROM produtos WHERE id = '" . htmlspecialchars($carpro2->produto_id) . "'");
																$nomeprox2 = $nomepro2->fetch(PDO::FETCH_OBJ);
															?>
																<div class="card-container container-card-info-content ">
																	<span class="tx-14" style="color:#FF0000"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i> <b>Ítem:</b> <?= htmlspecialchars($nomeprox2->nome); ?></span><br>
																	<div style="padding:0.3rem 0.1rem; border:1px solid #ccc; border-radius:0.5rem;">
																		<span class="tx-12"><i class="fa fa-square tx-8 mg-r-5"></i> Ref: <strong><?= htmlspecialchars($carpro2->referencia); ?></strong></span>
																	</div>
																	<?php if ($carpro2->tamanho != "N") { ?>
																		<span class="tx-12"><b>- Tamanho:</b> <?= htmlspecialchars($carpro2->tamanho); ?></span><br>
																	<?php } ?>
																	<span class="tx-12"><b>- Qnt:</b> <?= htmlspecialchars($carpro2->quantidade); ?></span><br>
																	<?php if ($carpro2->obs) { ?>
																		<span class="tx-12"><b>- Obs:</b> <?= htmlspecialchars($carpro2->obs); ?></span><br>
																	<?php } else { ?>
																		<span class="tx-12"><b>- Obs:</b> Não</span><br>
																	<?php } ?>

																	<?php

																	$meiom2  = $connect->query("SELECT * FROM store_o WHERE idp = '" . $carpro2->idpedido . "' AND status = '1' AND idu='$cod_id' AND meioameio='1' AND id_referencia='$carpro2->referencia'");

																	$meiomc2 = $meiom2->rowCount();

																	?>

																	<?php if ($meiomc2 > 0) { ?>

																		<span class="tx-12"><b>* <?= $meiomc2; ?> Sabores:</b></span><br />

																		<span class="tx-12">

																			<?php while ($meiomv2 = $meiom2->fetch(PDO::FETCH_OBJ)) { ?>

																				<?= $meiomv2->nome . "<br>"; ?>

																			<?php } ?>

																		</span>

																	<?php } ?>

																	<?php

																	$adcionais2  = $connect->query("SELECT * FROM store_o WHERE idp = '" . $carpro2->idpedido . "' AND status = '1' AND idu='$cod_id' AND meioameio='0' AND id_referencia='$carpro2->referencia'");

																	$adcionaisc2 = $adcionais2->rowCount();

																	?>

																	<?php if ($adcionaisc2 > 0) { ?>

																		<span class="tx-12"><b>* Adicionais/Ingredientes:</b></span><br />

																		<span class="tx-12">

																			<?php while ($adcionaisv2 = $adcionais2->fetch(PDO::FETCH_OBJ)) { ?>

																				<?= "-  R$: " . $adcionaisv2->valor . " | " . $adcionaisv2->nome . "<br>"; ?>

																			<?php } ?>

																		</span><br />

																	<?php } ?>

																	<div class="checkbox-wrapper">
																		<input type="checkbox" name="itens[]" value="<?= htmlspecialchars($carpro2->id); ?>">
																	</div>

																</div>
															<?php } ?>
														</div>
														<!-- <button type="submit" name="entregar_itens" class="btn btn-success">ENTREGAR ITENS SELECIONADOS</button> -->
													</div>
												</div>
											</div>
										</form>

									</div>
						<?php
								}
							}
						}
						?>
					</div>
				</div>



				<div class="tab-pane fade" id="encerrados" role="tabpanel" aria-labelledby="encerrados-tab">
					<div class="row row-sm mg-t-20">
						<div class="col-lg-12 mg-b-20">
							<div class="card card-info" style="background-color:#fdfbe3">
								<div class="card-body pd-40">
									<table id="pedidosTable" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>PEDIDO</th>
												<th>Cliente</th>
												<th>Celular</th>
												<th>Data</th>
												<th>Hora</th>
												<th>Itens</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$pedidos_encerrados = $connect->query("SELECT * FROM pedidos WHERE idu='" . $cod_id . "' AND status='7' ORDER BY id ASC");
											while ($pedidossx = $pedidos_encerrados->fetch(PDO::FETCH_OBJ)) {
											?>
												<tr>
													<td><?= htmlspecialchars($pedidossx->idpedido); ?></td>
													<td><?= htmlspecialchars($pedidossx->nome); ?></td>
													<td><?= htmlspecialchars($pedidossx->celular); ?></td>
													<td><?= htmlspecialchars($pedidossx->data); ?></td>
													<td><?= htmlspecialchars($pedidossx->hora); ?></td>
													<td>
														<div class="item-list">
															<?php
															$produtos = $connect->query("SELECT * FROM store WHERE idsecao = '$pedidossx->idpedido' AND status = '1' ORDER BY id ASC");
															while ($carpro2 = $produtos->fetch(PDO::FETCH_OBJ)) {
																$nomepro2 = $connect->query("SELECT nome FROM produtos WHERE id = '" . $carpro2->produto_id . "' ");
																$nomeprox2 = $nomepro2->fetch(PDO::FETCH_OBJ);
																echo '<div class="item"><b>Ítem:</b> ' . htmlspecialchars($nomeprox2->nome) . ' | Ref: ' . htmlspecialchars($carpro2->referencia) . ' | Qnt: ' . htmlspecialchars($carpro2->quantidade) . '</div>';
															}
															?>
														</div>
													</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>


			</div>

		</div><!-- container -->
	</div><!-- slim-mainpanel -->

	<script src="../lib/jquery/js/jquery.js"></script>
	<script src="../lib/datatables/js/jquery.dataTables.js"></script>
	<script src="../lib/datatables-responsive/js/dataTables.responsive.js"></script>
	<script src="../lib/select2/js/select2.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

	<script>
		$(function() {
			'use strict';
			$('#datatable1').DataTable({
				responsive: true,
				language: {
					searchPlaceholder: 'Buscar...',
					sSearch: '',
					lengthMenu: '_MENU_ ítens',
				}
			});

			$('#datatable2').DataTable({
				bLengthChange: false,
				searching: false,
				responsive: true
			});

			// Select2
			$('.dataTables_length select').select2({
				minimumResultsForSearch: Infinity
			});
		});

		setTimeout(function() {
			window.location.reload(1);
		}, 15000);
	</script>

</body>

</html>