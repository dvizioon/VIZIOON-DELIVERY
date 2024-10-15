<?php
if ($produtoscx == 0) {
	header("location: " . $site . "");
	exit;
}

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

								<div class="col-12">
									<div class="form-group">
										<label class="form-control-label">Nº Celular: <span class="tx-danger">*</span></label>
										<!-- <input type="test" id="cel" placeholder="(99)99999-9999" name="wps" class="form-control" <?php if (isset($_COOKIE['celcli'])) { ?> value="<?php print $_COOKIE['celcli']; ?>" <?php } ?> required> -->
										<input type="test" id="cel" placeholder="(99)99999-9999" name="wps" class="form-control" required>
									</div>
								</div>

							</div>
							<!-- <div class="row">

								<div class="col-lg-12">
									<div class="form-group dtn">
										<label class="form-control-label">Data de Aniversario: <span class="tx-danger">*</span></label>
										<input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
									</div>
								</div>

							</div> -->

							<div class="row">
								<div class="col-12">
									<div class="form-group">
										<label class="form-control-label">Nome: <span class="tx-danger">*</span></label>
										<!-- <input type="text" id="nome" name="nome" class="form-control" maxlength="30" <?php if (isset($_COOKIE['nomecli'])) { ?> value="<?php print $_COOKIE['nomecli']; ?>" <?php } ?> required> -->
										<input type="text" id="nome" name="nome" class="form-control" maxlength="30" required>
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

					// Remove caracteres não numéricos (parênteses, traços, espaços, etc.) do número de telefone
					telefone = telefone.replace(/[^\d]/g, '');

					// Seleciona o elemento de carregamento para exibir uma mensagem de "Carregando"
					const loadingUser = $(".loading-informaceos");

					// Define o conteúdo HTML para o indicador de carregamento (spinner e mensagem)
					loadingUser.html(`
                <span class="loader"></span>
                <p>Carregando Dados...</p>
            `);

					// Verifica se o telefone não está vazio
					if (telefone) {
						// Se o campo de telefone tiver um valor, faz uma requisição AJAX para o servidor
						$.ajax({
							url: './include/verificarTelefone.php', // URL do script PHP que verificará o número de telefone
							type: 'POST', // Tipo de requisição (POST)
							data: {
								telefone: telefone, // Envia o número de telefone
								id_empresa: <?php echo $idu; ?> // Envia o ID da empresa
							}, // Dados enviados na requisição
							dataType: 'json', // Espera que o servidor responda em formato JSON
							success: function(response) {
								// Se a requisição for bem-sucedida, esta função será chamada
								// Verifica se o telefone existe na resposta recebida do servidor
								if (response.existe) {
									// Se o telefone existir, exibe as informações e remove o loading após 2 segundos
									setTimeout(() => loadingUser.html(``), 2000);

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
										// console.log(dataFormatada)

										const dataOrginal = revertData(dataFormatada);
										// console.log(dataOrginal);

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



										// $('.dtn').html(`
										// 	<label class="form-control-label">Data de Nascimento <span class="text-success">(Encontrada)</span>: <span class="tx-danger">*</span></label>
										// 	<input type="date" class="form-control" value="${dataOrginal}" disabled required>
										// `);

									} else {
										// 		$('.dtn').html(`
										//     <label class="form-control-label">Data de Nascimento: <span class="tx-danger">*</span></label>
										//     <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
										// `);
									}


								} else {
									// Se o telefone não existir, limpa o loading
									loadingUser.html(``);
									// $('.dtn').html(`
									// 		<label class="form-control-label">Data de Nascimento: <span class="tx-danger">*</span></label>
									// 		<input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
									// 	`);
								}
							},
							error: function(xhr, status, error) {
								// Em caso de erro na requisição, exibe uma mensagem de erro
								console.error("Erro na requisição AJAX: ", status, error);
								// alert('Erro ao buscar os dados. Tente novamente.');
							}
						});
					} else {
						// Se o campo de telefone estiver vazio, remove o loading
						loadingUser.html('');
						// $('.dtn').html(`
						// 					<label class="form-control-label">Data de Nascimento: <span class="tx-danger">*</span></label>
						// 					<input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
						// 				`);
						// (Opcional) Você pode adicionar uma mensagem para alertar o usuário que o campo está vazio
						// alert('Por favor, insira um número de telefone válido.');
					}
				});
			});
		</script>