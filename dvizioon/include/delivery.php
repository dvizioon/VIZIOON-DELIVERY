<?php
if ($produtoscx == 0) {
	header("location: " . $site . "");
	exit;
}

$pegadadospagamentos = $connect->query("SELECT * FROM metodospagamentos WHERE idu='$idu'");
$metodospagamentos = $pegadadospagamentos->fetchAll(PDO::FETCH_OBJ);

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
};



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
};

$emailEmpresa =  $dadosempresa->email;;
$emailEmpresa = truncarTexto($emailEmpresa, 17, 5);


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
				<a class="nav-link" href="./delivery" style="color:#FFFFFF; background-color:#00CC00">
					PEDIDO DELIVERY
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
				<?php if (isset($_GET["troco"])) { ?>
					<div class="alert alert-warning" role="alert">
						<i class="fa fa-asterisk" aria-hidden="true"></i> Valor do troco não pode ser menor do que o valor total do pedido.
					</div>
				<?php } ?>

				<div class="card card-people-list pd-15 mg-b-10 d-none d-lg-block">
					<div class="slim-card-title"><i class="fa fa-caret-right"></i> CATEGORIAS</div>
					<div class="media-list">
						<?php while ($cathome = $categorias->fetch(PDO::FETCH_OBJ)) { ?>
							<div class="media">
								<a href="./#<?php echo $cathome->id; ?>">
									<img src="img/categoria/<?php if (!$cathome->url) {
																echo "off.jpg";
															} else {
																print $cathome->url;
															} ?>" style="width:30px; height:30px;" alt="">
								</a>
								<div class="media-body">
									<a href="./#<?php echo $cathome->id; ?>" style="color:#000000"><?php print $cathome->nome; ?></a>
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
																			} ?>" style="width:30px; height:30px;" alt="">
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

					<div class="row">
						<div class="col-lg-12">
							<div align="center"><i class="fa fa-hourglass-end mg-r-5" aria-hidden="true"></i>O Tempo aproximado para entrega é de <b><?php print $dadosempresa->timerdelivery; ?></b></div>
						</div>
					</div>

					<hr>

					<?php if ($somando->soma > $dadosempresa->dfree) { ?>
						<div class="row mg-b-10">
							<div align="center" class="col-lg-12">
								<div class="alert alert-success" role="alert">
									<strong class="tx-success"><i class="fa fa-thumbs-o-up mg-r-5"></i> Entrega Grátis.</strong>
								</div>
							</div>
						</div>
					<?php } ?>

					<div class="media-list">

						<div class="row" style="margin-top:-30px">
							<div class="col-lg-12">
								<div class="slim-card-title"><i class="fa fa-caret-right"></i> INFORME OS DADOS ABAIXO</div>
							</div>
						</div>
						<br>
						<form action="delivery_ok" method="post">

							<div class="row">


								<div class="col-lg-12">
									<div class="form-group">
										<label class="form-control-label">Bairros e Regiões atendidas : <span class="tx-danger">*</span></label>
										<?php if (empty($_GET["bairro"])) { ?>
											<select id="select-taxa" class="form-control select2-show-search" required>
											<?php } else { ?>
												<select id="select-taxa" class="form-control select2-show-search">
												<?php  } ?>
												<option value="">Selecione...</option>
												<?php
												$lerbanco  = $connect->query("SELECT * FROM bairros WHERE idu='" . $idu . "'");
												while ($taxabairro = $lerbanco->fetch(PDO::FETCH_OBJ)) {
												?>
													<option value="delivery&bairro=<?= $taxabairro->id; ?>"><?php echo $taxabairro->bairro; ?></option>
												<?php } ?>
												</select>
									</div>
								</div>

							</div>


							<?php $taxa = "0.00"; ?>
							<?php if (isset($_GET["bairro"])) { ?>

								<div class="row">
									<div class="col-lg-12">

										<div class="alert alert-dark" style="border-left: 4px solid #17a2b8; padding: 15px; background-color: #f8f9fa;">
											<i class="fa fa-info-circle" aria-hidden="true" style="color: #17a2b8;"></i>
											<strong>Por favor, informe seu número de telefone.</strong> Isso ajudará a agilizar o processo caso você já tenha um cadastro em nossa empresa.
										</div>


									</div>

								</div>

								<div class="row">
									<div class="col-lg-12">

										<div class="form-group">
											<label class="form-control-label">Nº do seu WhatsApp: <span class="tx-danger">*</span></label>
											<div class="input-group">
											</div>
											<!-- <input type="text" id="cel" placeholder="(99)99999-9999" name="wps" class="form-control" <?php if (isset($_COOKIE['celcli'])) { ?> value="<?php print $_COOKIE['celcli']; ?>" <?php } ?> required> -->
											<input type="text" id="cel" placeholder="(99)99999-9999" name="wps" class="form-control" required>
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
											<label class="form-control-label">Bairro/Região: </label>
											<div class="input-group">
												<?php if (isset($_GET["bairro"])) {
													$idbairro = $_GET["bairro"];
													$pegabairro = $connect->query("SELECT * FROM bairros WHERE id='" . $idbairro . "'");
													$pegabairro	= $pegabairro->fetch(PDO::FETCH_OBJ); ?>
													<input type="text" class="form-control" value="<?= $pegabairro->bairro; ?>" disabled="disabled">
													<input type="hidden" name="bairro" value="<?= $pegabairro->bairro; ?>">
												<?php } else { ?>
													<input type="text" class="form-control" value="Aguardando" disabled="disabled">
													<input type="hidden" name="bairro" value="0">
												<?php } ?>
											</div>
										</div>
									</div>



									<?php if (isset($_GET["bairro"])) {
										$idbairro = $_GET["bairro"];
										$pegataxa = $connect->query("SELECT * FROM bairros WHERE id='" . $idbairro . "'");
										$pegataxa	= $pegataxa->fetch(PDO::FETCH_OBJ);
									?>

										<?php if ($somando->soma > $dadosempresa->dfree) { ?>
											<input type="hidden" name="taxa" value="0.00">
											<?php $taxa = "0.00"; ?>
										<?php } else { ?>
											<input type="hidden" name="taxa" value="<?= $pegataxa->taxa; ?>">
											<?php $taxa = $pegataxa->taxa; ?>
										<?php } ?>

									<?php } ?>


									<div class="loading-informaceos d-flex align-items-center gap-3 justify-content-center w-100 " style="gap:0.5rem;">

									</div>

								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="form-group">
											<label class="form-control-label">Informe seu CEP: </label>
											<div class="input-group">
												<input type="text" class="form-control" name="cep" id="cep" value="" maxlength="8">
											</div>
										</div>
										<div class="alert alert-warning">
											<i class="fa fa-info-circle" aria-hidden="true"></i> Por favor, preencha o <strong>CEP</strong>. Caso o endereço não seja encontrado automaticamente, o campo será liberado para preenchimento manual.
										</div>

									</div>

								</div>

								<div class="row">
									<div class="col-lg-12">
										<div class="form-group">
											<label class="form-control-label">Endereço: </label>
											<div class="input-group">
												<input type="text" class="form-control" name="rua" id="rua" value="" required>
											</div>
										</div>
									</div>

								</div>

								<div class="row">

									<div class="col-lg-3">
										<div class="form-group">
											<label class="form-control-label">Nº: <span class="tx-danger">*</span></label>
											<div class="input-group">
												<!-- <input type="number" class="form-control" id="casa" name="numero" maxlength="5" <?php if (isset($_COOKIE['numero'])) { ?> value="<?php print $_COOKIE['numero']; ?>" <?php } ?> required> -->
												<input type="number" class="form-control" id="casa" name="numero" maxlength="5" required>
											</div>
										</div>
									</div>

									<div class="col-lg-9">
										<div class="form-group">
											<label class="form-control-label">Complemento/Ponto de Referência: </label>
											<!-- <input class="form-control" type="text" name="complemento" id="complemento" <?php if (isset($_COOKIE['comp'])) { ?> value="<?php print $_COOKIE['comp']; ?>" <?php } ?> maxlength="50"> -->
											<input class="form-control" type="text" name="complemento" id="complemento" maxlength="50">
										</div>
									</div>

								</div>

								<hr>

								<div class="row">


									<div class="col-lg-12">
										<div class="form-group">
											<label class="form-control-label">Primeiro Nome: <span class="tx-danger">*</span></label>
											<div class="input-group">
												<!-- <input type="text" id="nome" name="nome" class="form-control" maxlength="30" <?php if (isset($_COOKIE['nomecli'])) { ?> value="<?php print $_COOKIE['nomecli']; ?>" <?php } ?> required> -->
												<input type="text" id="nome" name="nome" class="form-control" maxlength="30" required>
											</div>
										</div>
									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-lg-6">
										<div class="form-group">
											<label class="form-control-label">Forma de Pagamento: <span class="tx-danger">*</span></label>

											<select id="options" class="form-control" onChange="verifica(this.value)" name="fmpgto" required>
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
											<label class="form-control-label">Troco?: </label>
											<div class="input-group">
												<input type="text" name="troco" id="troco" value="0,00" class="dinheiro form-control">
											</div>
										</div>
									</div>
								</div>

								<input type="hidden" name="cidade" value="<?php echo $dadosempresa->cidade; ?>">
								<input type="hidden" name="uf" value="<?php echo $dadosempresa->uf; ?>">


							<?php } ?>

					</div>
				</div>

			</div>



			<div class="col-md-3">
				<div class="card card-people-list pd-15 mg-b-10" style="background-color:<?php print $dadosempresa->corcarrinho; ?>">
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
					<?php if ($taxa > 0) { ?>
						<div class="row mg-t-10" style="color: #FF3333">
							<div class="col-7"><strong>Taxa de Entrega</strong></div>
							<div class="col-5"><strong>R$: <?= $taxa; ?> </strong></div>
						</div>
					<?php } ?>

					<div class="row  mg-t-10">
						<div class="col-7 tx-16"><strong>Total Geral</strong></div>
						<div class="col-5 tx-16"><strong>R$:<?php if (isset($somando->soma)) {
																$geral = $somando->soma + $sumx + $taxa;
																echo $gx = number_format($geral, 2, ',', '.');
															} else {
																print "0,00";
															} ?>
							</strong>
						</div>
					</div>

					<input type="hidden" name="totalg" class="form-control" value="<?= $geral; ?>">

					<hr>
					<button type="submit" class="btn btn-success" name="cart">Concluir Pedido <i class="fa fa-arrow-right mg-l-5"></i></button>
					</form>


				</div>


				<div class="card card-people-list pd-15 mg-b-10" style="background-color:<?php print $dadosempresa->corcarrinho; ?>; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">

					<!-- Seção para mensagem de contato -->
					<div class="alert alert-warning" role="alert">

						<div class="border border-danger p-2 mb-3">
							<i class="fa fa-info-circle" aria-hidden="true"></i>
							Se alguma informação estiver incorreta, por favor, entre em contato com a nossa equipe para correções.
						</div>

						<div class="d-flex flex-column " style="gap:0.5rem;"><strong>Whatsapp:</strong>
							<?php
							$telefone = preg_replace('/[^0-9]/', '', $dadosempresa->celular); // Remove caracteres não numéricos
							?>
							<a href="https://wa.me/<?php echo $telefone; ?>" target="_blank" style="text-decoration: none; color: #25d366; font-weight: bold; border: 1px solid #25d366; padding: 5px 10px; border-radius: 5px; display: inline-flex; align-items: center;">
								<i class="fa fa-whatsapp" style="font-size: 16px; margin-right: 5px;"></i>
								<?php echo $dadosempresa->celular; ?>
							</a>

							<a style="text-decoration: none; color: #007bff; font-weight: bold; border: 1px solid #007bff; padding: 5px 10px; border-radius: 5px; display: inline-flex; align-items: center;">
								<i class="fa fa-envelope" style="font-size: 16px; margin-right: 5px;"></i>
								<?php echo $emailEmpresa; ?>
							</a>


						</div>
					</div>
				</div>





			</div>

		</div>
	</div>



	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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




					// Faz a requisição AJAX usando jQuery
					$.ajax({
						url: './include/verificarTelefone.php', // URL do arquivo PHP que processará a requisição
						type: 'POST', // Método de envio
						data: {
							telefone: telefone,
							id_empresa: <?php echo $idu; ?> // Envia o ID da empresa
						}, // Dados que estão sendo enviados
						dataType: 'json', // Espera que a resposta seja um JSON
						success: function(response) {
							// Se o telefone não está vazio, exibe o loading
							loadingUser.html(`
                    <span class="loader"></span>
                    <p>Carregando Dados...</p>
                `);
							// Manipula a resposta recebida do servidor
							// console.log("Resposta do servidor: ", response);

							// Verifica se a resposta indica que o telefone existe
							if (response.existe) {
								// Exibe a modal informando que o telefone foi encontrado
								setTimeout(() => loadingUser.html(``), 2000);


								// Preenche os campos com os dados retornados
								$('#nome').val(response.nome);
								$('#rua').val(response.endereco).prop('disabled', false);
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

									$.ajax({
										url: './include/verificarAniversario.php', // Arquivo PHP que processa a verificação do aniversário
										type: 'POST',
										data: {
											telefone: telefone,
											id_empresa: <?php echo $idu; ?> // Envia o ID da empresa
										},
										success: function(response) {
											if (response.trim()) { // Verifica se a resposta não está vazia
												// Insere a modal no body da página e exibe automaticamente
												$('body').append(response);
												$('#modalAniversario').modal('show'); // Exibe a modal de aniversário
												$('#fecharModalBtn').on('click', function() {
													$('#modalAniversario').remove(); // Remove a modal do DOM
												});

											}
										},
										error: function(xhr, status, error) {
											console.error("Erro ao verificar o aniversário: ", status, error);
										}
									});


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
					$('.dtn').html(`
											<label class="form-control-label">Data de Nascimento: <span class="tx-danger">*</span></label>
											<input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
										`);
				}
			});
		});
	</script>