<?php
require_once "topo.php";

// Consulta SQL para pegar as informações da tabela 'config'
$informacoes = $connect->query("SELECT * FROM config WHERE id='$cod_id'");
// Usando fetchObject() para pegar o resultado como um objeto
$resultado = $informacoes->fetchObject();
$cpf = $resultado->cpf;
// var_dump($cpf);

// Protocolo (http ou https)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";

// Nome do servidor (ex: www.exemplo.com)
$host = $_SERVER['HTTP_HOST'];

// Caminho da URL (ex: /diretorio/pagina.php)
$uri = $_SERVER['REQUEST_URI'];

// URL completa
$url = $protocol . $host;

$urlMontada = "$url/provendas/admin/";
// echo $urlMontada;



?>


<div class="slim-mainpanel">
	<div class="container">

		<!-- <div class="section-wrapper">
			<label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Movimentos por Data</label>
			<hr>
			<form action="relatorios/pordata.php" method="post">
				<input type="hidden" class="form-control" name="idusr" value="<?= $cod_id; ?>">
				<div class="form-layout">
					<div class="row">
						<div class="col-lg-3">
							<div class="form-group">
								<label class="form-control-label">Inicial: <span class="tx-danger">*</span></label>
								<input type="text" class="form-control" id="dateMask" name="data_i" placeholder="MM/DD/YYYY" required>
							</div>
						</div>
						<div class="col-lg-3">
							<div class="form-group">
								<label class="form-control-label">Final: <span class="tx-danger">*</span></label>
								<input type="text" class="form-control" id="dateMask2" name="data_f" placeholder="MM/DD/YYYY" required>
							</div>
						</div>
						<div class="col-lg-3">
							<div class="form-group">
								<label class="form-control-label" style="color:#FFFFFF">..</label><br />
								<button class="btn btn-primary bd-0">Gerar <i class="fa fa-arrow-right"></i></button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		 -->

		<!-- Adicionando um iframe responsivo com design aprimorado -->
		<div class="custom-iframe-container">
			<div class="iframe-header">
				<span>Dashboard Integrado</span>
			</div>
			<div class="embed-responsive embed-responsive-16by9">
				<iframe class="embed-responsive-item" src="<?php echo $urlMontada; ?>" frameborder="0" allowfullscreen></iframe>
			</div>
		</div>

	</div><!-- container -->
</div><!-- slim-mainpanel -->

<!-- Scripts -->
<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../lib/jquery.maskedinput/js/jquery.maskedinput.js"></script>

<script>
	$(function() {
		'use strict'

		// Input Masks
		$('#dateMask').mask('99-99-9999');
		$('#dateMask2').mask('99-99-9999');



	});
</script>

<script>
	// Certifique-se de que o iframe está carregado antes de tentar acessar o seu conteúdo
	window.onload = function() {
		var cpf = "<?php echo $cpf; ?>"; // Obtém o valor do PHP

		// Função para formatar o CPF
		function formatarCPF(cpf) {
			return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
		}

		var cpfFormatado = formatarCPF(cpf); // Aplica a formatação ao CPF

		// Seleciona o iframe
		var iframe = document.querySelector('iframe'); // Substitua por um seletor mais específico se necessário

		// Verifica se o iframe e seu conteúdo estão disponíveis
		if (iframe && iframe.contentWindow) {
			// Acessa o documento dentro do iframe
			var iframeDocument = iframe.contentWindow.document;

			// Seleciona o campo dentro do iframe usando o ID 'id_sc_field_login'
			var loginField = iframeDocument.getElementById('id_sc_field_login');

			// console.log(loginField); // Verifica se o campo foi encontrado

			if (loginField) {
				// Define o valor do campo com o CPF formatado
				loginField.value = cpfFormatado;

				// Dispara um evento de input para forçar a atualização do valor
				var event = new Event('input', {
					bubbles: true
				});
				loginField.dispatchEvent(event); // Atualiza o campo de forma adequada
			}
		}
	};
</script>




<!-- Estilos para o iframe personalizado -->
<style>
	.custom-iframe-container {
		position: relative;
		background-color: #f8f9fa;
		border: 1px solid #dee2e6;
		border-radius: 10px;
		box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
		overflow: hidden;
	}

	.iframe-header {
		background-color: #007bff;
		color: #fff;
		padding: 10px 15px;
		font-size: 18px;
		font-weight: bold;
		text-align: center;
	}

	.embed-responsive {
		position: relative;
		display: block;
		width: 100%;
		padding: 0;
		overflow: hidden;

		/* Proporção de 16:9 */
	}

	.embed-responsive .embed-responsive-item {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		border: none;
	}

	@media (max-width: 768px) {
		.iframe-header {
			font-size: 16px;
		}
	}
</style>

</body>

</html>