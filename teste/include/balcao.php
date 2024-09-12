<?php
if ($produtoscx == 0) {
	header("location: " . $site . "");
	exit;
}
?>

<style>
	.loader {
		width: 30px;
		height: 30px;
		border-radius: 50%;
		position: relative;
		animation: rotate 1s linear infinite
	}

	.loader::before {
		content: "";
		box-sizing: border-box;
		position: absolute;
		inset: 0px;
		border-radius: 50%;
		border: 5px solid #000;
		animation: prixClipFix 2s linear infinite;
	}

	@keyframes rotate {
		100% {
			transform: rotate(360deg)
		}
	}

	@keyframes prixClipFix {
		0% {
			clip-path: polygon(50% 50%, 0 0, 0 0, 0 0, 0 0, 0 0)
		}

		25% {
			clip-path: polygon(50% 50%, 0 0, 100% 0, 100% 0, 100% 0, 100% 0)
		}

		50% {
			clip-path: polygon(50% 50%, 0 0, 100% 0, 100% 100%, 100% 100%, 100% 100%)
		}

		75% {
			clip-path: polygon(50% 50%, 0 0, 100% 0, 100% 100%, 0 100%, 0 100%)
		}

		100% {
			clip-path: polygon(50% 50%, 0 0, 100% 0, 100% 100%, 0 100%, 0 0)
		}
	}
</style>
<div class="slim-navbar sticky-top" style="background-color:<?php print $dadosempresa->cormenu; ?>">
	<div class="container">
		<ul class="nav">
			<li class="nav-item">
				<a class="nav-link" href="./balcao" style="color:#FFFFFF; background-color:#0099CC">
					RETIRADA NO BALCÃO
				</a>
			</li>
		</ul>
	</div>
</div>

<div class="slim-mainpanel">
	<div class="container">

		<div class="row mg-t-10">

			<div class="col-md-3">

				<?php if (isset($_GET["erro"])) { ?>
					<div class="alert alert-warning" role="alert">
						<i class="fa fa-asterisk" aria-hidden="true"></i> Nº do celular inválido.
					</div>
				<?php } ?>



				<div class="card card-people-list pd-15 mg-b-10 d-none d-lg-block">
					<div class="slim-card-title"><i class="fa fa-caret-right"></i> CATEGORIAS</div>
					<div class="media-list">
						<?php
						while ($cathome = $categorias->fetch(PDO::FETCH_OBJ)) {
							$qntp = $connect->query("SELECT id FROM produtos WHERE categoria = '" . $cathome->id . "' AND status='1'");
							$qntp = $qntp->rowCount();
						?>
							<div class="media">
								<a href="./#<?php echo $cathome->id; ?>">
									<img src="img/categoria/<?php if (!$cathome->url) {
																echo "off.jpg";
															} else {
																print $cathome->url;
															} ?>" style="width:40px; height:40px; border-radius: 100%;" alt="">
								</a>
								<div class="media-body">
									<a href="./#<?php echo $cathome->id; ?>" style="color:#000000"><?php print $cathome->nome; ?> (<?php print $qntp; ?>)</a>
								</div>
								<a href="./#<?php echo $cathome->id; ?>" style="color:#000000"><i class="fa fa-chevron-circle-right"></i></a>
							</div>
						<?php } ?>
					</div>
				</div>



				<div id="accordion" class="accordion-one mg-b-10 d-lg-none" role="tablist" aria-multiselectable="true">
					<div class="card">
						<div class="card-header" role="tab" id="headingTwo">
							<a class="collapsed tx-gray-800 transition" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
								<i class="fa fa-bars mg-r-10"></i> CONTINUAR COMPRANDO
							</a>
						</div>
						<div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
							<div class="card-body">
								<div class="card-people-list pd-5">
									<div class="media-list" style="margin-top:-10px">
										<?php while ($cathomem = $categoriasm->fetch(PDO::FETCH_OBJ)) { ?>
											<div class="media">
												<a href="./#<?php echo $cathomem->id; ?>">
													<img src="img/categoria/<?php if (!$cathomem->url) {
																				echo "off.jpg";
																			} else {
																				print $cathomem->url;
																			} ?>" style="width:30px; height:30px; border-radius: 100%;" alt="">
												</a>
												<div class="media-body">
													<a href="./#<?php echo $cathomem->id; ?>" style="color:#000000"><?php print $cathomem->nome; ?></a>
												</div>
												<a href="./#<?php echo $cathomem->id; ?>" style="color:#000000"><i class="fa fa-chevron-circle-right"></i></a>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>

			<div class="col-md-6">

				<div class="card card-people-list pd-15 mg-b-10">
					<div class="slim-card-title"><i class="fa fa-caret-right"></i> INFORME SEUS DADOS ABAIXO</div>
					<div class="media-list">

						<form action="balcao_ok" method="post">
							<div class="row">
								<div class="col-6">
									<div class="form-group">
										<label class="form-control-label">Nº Celular: <span class="tx-danger">*</span></label>
										<input type="test" id="cel" placeholder="(99)99999-9999" name="wps" class="form-control" <?php if (isset($_COOKIE['celcli'])) { ?> value="<?php print $_COOKIE['celcli']; ?>" <?php } ?> required>
									</div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label class="form-control-label">Nome: <span class="tx-danger">*</span></label>
										<input type="text" id="nome" name="nome" class="form-control" maxlength="30" <?php if (isset($_COOKIE['nomecli'])) { ?> value="<?php print $_COOKIE['nomecli']; ?>" <?php } ?> required>
									</div>
								</div>
							</div>
							<div class="loading-informaceos d-flex align-items-center gap-3 justify-content-center w-100 " style="gap:0.5rem;">

							</div>
							<hr>

							<div align="center" style="color:#FF3333"><i class="fa fa-hourglass-end mg-r-5" aria-hidden="true"></i> Tempo aproximado para retirada é de <b><?php print $dadosempresa->timerbalcao; ?></b></div>

					</div>
				</div>

			</div>

			<div class="col-md-3">

				<div class="card card-people-list pd-15 mg-b-10" style="background-color:<?php print $dadosempresa->corcarrinho; ?>">

					<div class="slim-card-title"><i class="fa fa-caret-right"></i> TOTAL DO PEDIDO</div>
					<hr />
					<input type="hidden" name="subtotal" class="form-control" value="<?php echo $subtotal = number_format($somando->soma, 2, ',', ' ');  ?>">
					<?php
					echo "<span class='border border-info p-2 rounded mb-2'><strong>ID Pedido: " . $id_cliente . "</strong></span>";
					$opcionais  = $connect->query("SELECT valor, quantidade FROM store_o WHERE ids = '" . $id_cliente . "' AND status = '0' AND idu='$idu' AND meioameio='0'");
					$sumx = 0;
					while ($valork = $opcionais->fetch(PDO::FETCH_OBJ)) {
						$quantop = $valork->quantidade;
						$valorop = $valork->valor;
						$totais = $valorop * $quantop;
						$sumx += $totais;
					}


					?>
					<input type="hidden" name="adcionais" class="form-control" value="<?php echo number_format($sumx, 2, ',', ' ');  ?>">


					<div class="row">
						<div class="col-6 tx-14"><strong>Total Geral</strong></div>
						<?php


						?>
						<div class="col-6 tx-14"><strong>R$: <?php if (isset($somando->soma)) {
																	$geral = $somando->soma + $sumx;
																	echo number_format($geral, 2, ',', ' ');
																} else {
																	print "0,00";
																} ?></strong></div>
						<input type="hidden" name="totalg" class="form-control" value="<?php echo number_format($geral, 2, ',', ' ');  ?>">
					</div>
					<hr>
					<button type="submit" class="btn btn-success" name="cart">Concluir Pedido <i class="fa fa-arrow-right mg-l-10"></i></button>
					</form>

				</div>
			</div>
		</div>


		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

		<script>
			$(document).ready(function() {
				// Quando o campo de telefone perde o foco (blur), enviamos a requisição
				$('#cel').on('blur', function() {
					var telefone = $(this).val();

					// Remove os parênteses, espaços e traços do telefone
					telefone = telefone.replace(/[^\d]/g, '');


					const loadingUser = $(".loading-informaceos");

					loadingUser.html(`
					<span class="loader"></span>
					<p>Carregando Dados...</p>
				`)

					// Verifica se o telefone não está vazio
					if (telefone) {
						// Faz a requisição AJAX usando jQuery
						$.ajax({
							url: './include/verificarTelefone.php', // URL do arquivo PHP que processará a requisição
							type: 'POST', // Método de envio
							data: {
								telefone: telefone,
								id_empresa: <?php echo $idu; ?>
							}, // Dados que estão sendo enviados
							dataType: 'json', // Espera que a resposta seja um JSON
							success: function(response) {
								// Manipula a resposta recebida do servidor
								// console.log("Resposta do servidor: ", response);

								// Verifica se a resposta indica que o telefone existe
								if (response.existe) {
									// Exibe a modal informando que o telefone foi encontrado
									setTimeout(() => loadingUser.html(``), 2000)

									$('#nome').val(response.nome);
									// $('#rua').val(response.bairro);
									// $('#endereco').val(response.endereco);
									// $('#complemento').val(response.complemento);
									// $('#cep').val(response.cep);
									// $('#casa').val(response.casa);
									// $('#primeiro_nome').val(response.primeiro_nome);

									// Fecha a modal após 2 segundos

								} else {
									loadingUser.html(``)
									// Não exibe a modal se o telefone não for encontrado
									// alert('Número de telefone não encontrado.');
								}
							},
							error: function(xhr, status, error) {
								console.error("Erro na requisição AJAX: ", status, error);
								alert('Erro ao buscar os dados. Tente novamente.');
							}
						});
					}
				});
			});
		</script>