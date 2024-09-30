<?php if ($opcionaisxk >= 1): ?>
	<?php
	// Consultar o grupo e verificar se existe
	$qntsQuery = $connect->query("SELECT * FROM grupos WHERE Id='" . $opcionaisxl->idgrupo . "' AND obrigatorio='3' AND status='1'");
	$qnts = $qntsQuery->fetch(PDO::FETCH_OBJ);

	// Verifica se $qnts é um objeto válido antes de acessar suas propriedades
	if ($qnts): ?>
		<hr>
		<div class="slim-card-title" style="color:#CC3300">
			Escolha até <?php print $qnts->quantidade; ?> sabores<span style="color:#FF0000"> *</span>
		</div>
		<p style="font-size:12px">- Será cobrado o maior valor do item selecionado.</p>
		<center style="font-size:12px; color:#FF0000" id="Mensagemmeioameio"></center>
		<div class="row mg-t-10">
			<?php
			// Consultar os opcionais
			$grupomeioQuery = $connect->query("SELECT * FROM grupos WHERE Id='" . $opcionaisxl->idgrupo . "' AND obrigatorio='3' AND status='1'");
			$grupomeios = $grupomeioQuery->fetch(PDO::FETCH_OBJ);

			$opcionaismeioQuery = $connect->query("SELECT * FROM opcionais WHERE idg='" . $grupomeios->Id . "' AND status='1'");
			while ($opcionaismeios = $opcionaismeioQuery->fetch(PDO::FETCH_OBJ)): ?>
				<div class="col-9">
					<input type="checkbox" name="meioameios[]" id="idmeioameio" value="<?php print $opcionaismeios->opnome; ?>,<?php print $opcionaismeios->valor; ?>"> <!-- Removido o required -->
					- <span><?php print $opcionaismeios->opnome; ?></span>

					<?php if ($opcionaismeios->opdescricao != "N"): ?>
						<p style="font-size:10px; color:#CCCC00">- <?php print $opcionaismeios->opdescricao; ?></p>
					<?php endif; ?>
				</div>
				<div class="col-3 tx-13 mg-t-10">R$: <?php print $opcionaismeios->valor; ?></div>
			<?php endwhile; ?>
		</div>
		<script>
			$(document).ready(function() {
				// Passar a quantidade máxima do PHP para JavaScript
				var NumeroMaximo = <?php echo $qnts->quantidade; ?>;

				// Valida se ao menos um checkbox foi selecionado antes do envio
				$('form').submit(function(event) {
					if ($("input[id='idmeioameio']:checked").length == 0) {
						$('#Mensagemmeioameio').html('Por favor, selecione pelo menos um sabor.');
						event.preventDefault(); // Impede o envio do formulário
					}
				});

				// Limita o número máximo de seleções permitidas
				$("input[id='idmeioameio']").click(function() {
					$("#Mensagemmeioameio").empty();
					if ($("input[id='idmeioameio']").filter(':checked').length > NumeroMaximo) {
						$('#Mensagemmeioameio').html('Máximo de ' + NumeroMaximo + ' sabores.');
						$(this).prop('checked', false); // Desmarca o checkbox se exceder o limite
					}
				});
			});
		</script>
	<?php else: ?>
		<!-- Mensagem de erro se o grupo não for encontrado -->
		<div class="slim-card-title" style="color:#CC3300">
			Grupo de opcionais não encontrado.
		</div>
	<?php endif; ?>
<?php endif; ?>