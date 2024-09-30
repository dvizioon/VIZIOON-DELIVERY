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

// if (isset($_GET["apagaritem"])) {
// 	$idel = $_GET['apagaritem'];
// 	$idce = $_GET['idce'];
// 	$delitem = $connect->query("DELETE FROM store WHERE idpedido='$idel'");
// 	$delopci = $connect->query("DELETE FROM store_o WHERE idp='$idel'");
// 	if ($delitem) {
// 		header("location: pdvpedidoeditar.php?idpedido=" . $idce . "");
// 		exit;
// 	}
// }

// if (
// 	isset($_GET["apagaritem"]) && isset($_GET["iditem"])
// ) {
// 	$idpedido = $_GET['apagaritem'];  // ID do pedido (para redirecionamento)
// 	$iditem = $_GET['iditem'];        // ID do item a ser excluído

// 	try {
// 		// Inicia a transação
// 		$connect->beginTransaction();

// 		// Prepara a exclusão do item específico na tabela 'store'
// 		$delitemStmt = $connect->prepare("DELETE FROM store WHERE id = :iditem");
// 		$delitemStmt->bindParam(':iditem', $iditem, PDO::PARAM_INT);
// 		$delitem = $delitemStmt->execute();

// 		// Prepara a exclusão das opções específicas na tabela 'store_o'
// 		$delopciStmt = $connect->prepare("DELETE FROM store_o WHERE idp = :idpedido AND id = :iditem");
// 		$delopciStmt->bindParam(':idpedido', $idpedido, PDO::PARAM_INT);
// 		$delopciStmt->bindParam(':iditem', $iditem, PDO::PARAM_INT);
// 		$delopci = $delopciStmt->execute();

// 		// Commit da transação
// 		$connect->commit();

// 		// Verifica se ambas as exclusões foram bem-sucedidas
// 		if ($delitem && $delopci) {
// 			// Redireciona para a página de edição do pedido após a exclusão
// 			header("Location: pdvpedidoeditar.php?idpedido=" . $idpedido);
// 			exit;
// 		} else {
// 			// Caso ocorra algum erro na exclusão
// 			echo "Erro ao excluir o item.";
// 		}
// 	} catch (PDOException $e) {
// 		// Rollback da transação em caso de erro
// 		$connect->rollBack();
// 		echo "Erro: " . $e->getMessage();
// 	}
// }


if (isset($_GET["apagaritem"]) && isset($_GET["iditem"])
) {
	$idpedido = $_GET['apagaritem'];  // ID do pedido (para redirecionamento)
	$iditem = $_GET['iditem'];        // ID do item a ser excluído

	try {
		// Inicia a transação
		$connect->beginTransaction();

		// Consulta para obter o valor da coluna 'referencia' baseado no id
		$pegar_referencia_stmt = $connect->prepare("SELECT referencia FROM store WHERE id = :iditem");
		$pegar_referencia_stmt->bindParam(':iditem', $iditem, PDO::PARAM_INT);
		$pegar_referencia_stmt->execute();

		// Verifica se a consulta retornou algum resultado
		if ($pegar_referencia_stmt->rowCount() > 0) {
			// Obtém o resultado da consulta
			$pegar_referencia_result = $pegar_referencia_stmt->fetch(PDO::FETCH_ASSOC);
			// Obtém o valor da coluna 'referencia'
			$referencia = $pegar_referencia_result['referencia'];

			// Consulta a tabela store_o para verificar se existe algum registro com o id_referencia igual à $referencia
			$verifica_referencia_stmt = $connect->prepare("SELECT * FROM store_o WHERE id_referencia = :referencia");
			$verifica_referencia_stmt->bindParam(':referencia', $referencia, PDO::PARAM_STR);
			$verifica_referencia_stmt->execute();

			// Verifica se a consulta retornou algum resultado
			if ($verifica_referencia_stmt->rowCount() > 0) {
				// Prepara a exclusão das opções específicas na tabela 'store_o'
				$delopci_stmt = $connect->prepare("DELETE FROM store_o WHERE id_referencia = :referencia");
				$delopci_stmt->bindParam(':referencia', $referencia, PDO::PARAM_STR);
				$delopci = $delopci_stmt->execute();

				if (!$delopci) {
					// Rollback da transação se não conseguir excluir da tabela store_o
					$connect->rollBack();
					echo "Erro ao excluir as opções da tabela store_o.";
					exit;
				}
			}

			// Prepara a exclusão do item específico na tabela 'store'
			$delitem_stmt = $connect->prepare("DELETE FROM store WHERE id = :iditem");
			$delitem_stmt->bindParam(':iditem', $iditem, PDO::PARAM_INT);
			$delitem = $delitem_stmt->execute();

			if ($delitem) {
				// Commit da transação
				$connect->commit();

				// Redireciona para a página de edição do pedido após a exclusão
				header("Location: pdvpedidoeditar.php?idpedido=" . $idpedido);
				exit;
			} else {
				// Rollback da transação se não conseguir excluir da tabela store
				$connect->rollBack();
				echo "Erro ao excluir o item da tabela store.";
			}
		} else {
			// Rollback da transação se a referência não for encontrada na tabela store
			$connect->rollBack();
			echo "Nenhuma referência encontrada para o id especificado na tabela store.";
		}
	} catch (PDOException $e) {
		// Rollback da transação em caso de erro
		$connect->rollBack();
		echo "Erro: " . $e->getMessage();
	}
}
// $_GET['idpedido'] = preg_replace("/[^0-9]/", "", $_GET['idpedido']);
//mudar Session ID...
$_SESSION["pedido_id_pdv"] = $_GET['idpedido'];

$id_cliente     = $_SESSION["pedido_id_pdv"];
$idPedido = $_GET['idpedido'];


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
							Editar Pedido n° <?= $id_cliente; ?>
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

			<div class="alert alert-danger mg-t-20" role="alert">
				<i class="fa fa-times" aria-hidden="true"></i> ATENÇÃO: Ao editar o pedido do cliente (Acrescentando ou Excluindo) produtos, você deve finalizar clicando nos botões a direita (DELIVERY, BALCÃO, MESA).
			</div>

			<div class="row mg-t-10">

				<div class="col-md-3">

					<div class="card card-people-list pd-15 mg-b-10 d-none d-lg-block">
						<div class="slim-card-title"><i class="fa fa-caret-right"></i> CATEGORIAS</div>
						<div class="media-list">
							<?php
							while ($cathome = $categorias->fetch(PDO::FETCH_OBJ)) {
								$qntp = $connect->query("SELECT id FROM produtos WHERE categoria = '" . $cathome->id . "' AND status='1'");
								$qntp = $qntp->rowCount();

							?>
								<div class="media">
									<a href="#<?php echo $cathome->id; ?>">
										<img src="../img/categoria/<?php if (!$cathome->url) {
																		echo "off.jpg";
																	} else {
																		print $cathome->url;
																	} ?>" style="width:40px; height:40px; border-radius: 100%;" alt="">
									</a>
									<div class="media-body">
										<a href="#<?php echo $cathome->id; ?>" style="color:#000000"><?php print $cathome->nome; ?> (<?php print $qntp; ?>)</a>
									</div>
									<a href="#<?php echo $cathome->id; ?>" style="color:#000000"><i class="fa fa-chevron-circle-right"></i></a>
								</div>
							<?php }

							?>
						</div>
					</div>
				</div>

				<div class="col-md-6">

					<?php
					$buscacategorias 	= $connect->query("SELECT * FROM categorias WHERE idu='$idu' ORDER BY posicao ASC");
					while ($exibircategoria = $buscacategorias->fetch(PDO::FETCH_OBJ)) {
					?>

						<div class="card card-people-list pd-15 mg-b-10" id="<?php echo $exibircategoria->id; ?>">
							<div class="slim-card-title"><i class="fa fa-caret-right"></i> <?php print $exibircategoria->nome; ?></div>
							<div class="media-list">

								<?php
								$buscaprodutos = $connect->query("SELECT * FROM produtos WHERE categoria = '" . $exibircategoria->id . "' AND status='1'");
								while ($exibirlista = $buscaprodutos->fetch(PDO::FETCH_OBJ)) {
									// var_dump($exibirlista);
								?>

									<div class="media">
										<a href="<?php echo $site; ?>produto&id=<?php echo $exibirlista->id; ?>">
											<img src="../img/fotos_produtos/<?php if (!$exibirlista->foto) {
																				echo "off.jpg";
																			} else {
																				print $exibirlista->foto;
																			} ?>" style="width:80px; height:65px;" class="img-thumbnail">
										</a>

										<div class="media-body">

											<span class="tx-15"><strong><?php print $exibirlista->nome; ?></strong></span>
											<p class="tx-12 mg-r-5"><?php print $exibirlista->ingredientes; ?></p>
											<p class="tx-14"><strong><?php if ($exibirlista->valor > "0.00") { ?>
													<?php print "<strong>R$:</strong> " . number_format($exibirlista->valor, 2, ',', '.');
																		} ?></strong></p>
										</div>



										<?php if ($exibirlista->valor > "0.00") { ?>
											<div align="left"><button type="button" class="btn btn-success btn-sm view_data2" id="<?php echo $exibirlista->id; ?>">
													<i class="fa fa-cart-plus mg-r-5"></i> Incluir</button>
											</div>
										<?php } else { ?>
											<div align="left"><button type="button" class="btn btn-info btn-sm view_data2" id="<?php echo $exibirlista->id; ?>">
													<i class="fa fa-plus-circle mg-r-5"></i>Opções</button>
											</div>
										<?php } ?>


									</div>
								<?php } ?>
							</div>
						</div>
					<?php }  ?>

				</div>

				<div class="col-md-3 d-sm-block d-md-block d-lg-block">
					<?php include('carrinho_editar.php'); ?>
				</div>



			</div>
		</div>

		<div id="visulUsuarioModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h6 class="modal-title" id="visulUsuarioModalLabel">Detalhes do Produto</h6>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<span id="visul_usuario"></span>
					</div>
				</div>
			</div>
		</div>

		<div id="visulProdutoModal3" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h6 class="modal-title" id="visulUsuarioModalLabel">Delivery</h6>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<span id="visul_usuario3"></span>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-outline-info" data-dismiss="modal">Fechar</button>
					</div>
				</div>
			</div>
		</div>

		<script src="../lib/jquery/js/jquery.js"></script>
		<script src="../lib/bootstrap/js/bootstrap.js"></script>
		<script>
			$(document).ready(function() {
				$(window).scroll(function() {
					if ($(this).scrollTop() > 100) {
						$('a[href="#top"]').fadeIn();
					} else {
						$('a[href="#top"]').fadeOut();
					}
				});

				$('a[href="#top"]').click(function() {
					$('html, body').animate({
						scrollTop: 0
					}, 800);
					return false;
				});
			});
		</script>
		<script>
			$(document).ready(function() {
				$(document).on('click', '.view_data2', function() {
					var user_id = $(this).attr("id");
					// Verificar se há valor na variável "user_id".
					if (user_id !== '') {
						var dados = {
							user_id: user_id,
							id_pedido: <?php echo json_encode($idPedido); ?> // Certifica-se de que o valor PHP seja corretamente formatado como JSON
						};
						$.post('ajax/produton2.php', dados, function(retorna) {
							// console.log(retorna);
							// Carregar o conteúdo para o usuário
							$("#visul_usuario").html(retorna);
							$('#visulUsuarioModal').modal('show');
						}).fail(function(jqXHR, textStatus, errorThrown) {
							console.error("Erro na requisição AJAX:", textStatus, errorThrown);
						});
					}
				});
			});
		</script>

		<script>
			$(document).on('click', '.number-spinner button', function() {
				var btn = $(this),
					oldValue = btn.closest('.number-spinner').find('input').val().trim(),
					newVal = 0;

				if (btn.attr('data-dir') == 'up') {
					newVal = parseInt(oldValue) + 1;
				} else {
					if (oldValue > 1) {
						newVal = parseInt(oldValue) - 1;
					} else {
						newVal = 1;
					}
				}
				btn.closest('.number-spinner').find('input').val(newVal);
			});
		</script>


</body>

</html>