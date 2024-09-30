<?php
require_once "topo.php";
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
				<iframe class="embed-responsive-item" src="https://cardapio.sistemaproweb.com.br/provendas/admin/" frameborder="0" allowfullscreen></iframe>
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