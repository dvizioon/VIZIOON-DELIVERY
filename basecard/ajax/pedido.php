<?php
session_start();

include_once('../../funcoes/Conexao.php');
include_once('../../funcoes/Key.php');

$id_cliente = $_SESSION['id_cliente'];

if (isset($_POST["user_idx"])) {
	$idu = $_POST["user_idx"];

	$empresa = $connect->query("SELECT * FROM config WHERE id='$idu'");
	$dadosempresa = $empresa->fetch(PDO::FETCH_OBJ);

	$produtosca = $connect->query("SELECT * FROM store WHERE idsecao = '$id_cliente' AND status='0' AND idu='$idu' ORDER BY id DESC");
	$produtoscx = $produtosca->rowCount();

	if ($produtoscx > 0) {
		$somando = $connect->query("SELECT valor, SUM(CAST(valor AS DECIMAL(10, 2)) * quantidade) AS soma FROM store WHERE idsecao='" . $id_cliente . "' AND status='0' AND idu='$idu'");
		$somando = $somando->fetch(PDO::FETCH_OBJ);
	}
}
?>

<center class="mg-b-5"><span class="tx-14">Como você deseja receber os itens deste pedido?</span></center>
<div class="slim-card-title mg-b-15">
	<?php if ($dadosempresa->delivery == 1) { ?>
		<a href="./delivery"><button type="button" class="btn btn-success btn-block mg-b-15">No endereço - (Delivery)</button></a>
	<?php } ?>
	<?php if ($dadosempresa->balcao == 1) { ?>
		<a href="./balcao"><button type="button" class="btn btn-info btn-block mg-b-15">Retirar no Balcão</button></a>
	<?php } ?>
</div>

<?php if ($produtoscx > 0) { ?>
	<div class="card card-people-list pd-15" style="background-color:<?php print htmlspecialchars($dadosempresa->corcarrinho); ?>">
		<div class="media-list">
			<?php
			while ($carpro = $produtosca->fetch(PDO::FETCH_OBJ)) {
				if ($carpro) {
					$nomepro = $connect->query("SELECT nome FROM produtos WHERE id = '" . $carpro->produto_id . "' AND idu='$idu'");
					$nomeprox = $nomepro->fetch(PDO::FETCH_OBJ);

					// Calcular total do item, incluindo opcionais
					$valorItem = (float) $carpro->valor;
					$quantidadeItem = (int) $carpro->quantidade;
					$totalItem = $valorItem * $quantidadeItem;

					$opcionais = $connect->query("SELECT valor, quantidade FROM store_o WHERE idp = '" . $carpro->idpedido . "' AND status = '0' AND idu='$idu' AND meioameio='0' AND id_referencia='" . $carpro->referencia . "'");
					while ($valork = $opcionais->fetch(PDO::FETCH_OBJ)) {
						$valorop = (float) $valork->valor;
						$quantop = (int) $valork->quantidade;
						$totalItem += $valorop * $quantop;
					}
			?>
					<div id="accordion<?php print htmlspecialchars($carpro->id); ?>" class="accordion-one mg-b-20" role="tablist" aria-multiselectable="true">
						<div>
							<div role="tab" id="headingTwo">
								<a class="collapsed tx-gray-800 transition" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo<?php print htmlspecialchars($carpro->id); ?>" aria-expanded="false" aria-controls="collapseTwo">
									<b>- Item:</b> <?php print htmlspecialchars($nomeprox->nome); ?> <b style="color:#FF0000"><i class="fa fa-angle-down mg-l-5"></i></b>
								</a>
							</div>

							<div id="collapseTwo<?php print htmlspecialchars($carpro->id); ?>" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
								<div>
									<div style="padding:0.3rem 0.1rem;border:1px solid #ccc; border-radius:0.5rem;">
										<span class="tx-12"><i class="fa fa-square tx-8 mg-r-5"></i> Ref: <strong><?php print htmlspecialchars($carpro->referencia); ?></strong></span>
									</div>

									<?php if ($carpro->tamanho != "N") { ?>
										<div><span class="tx-12"><i class="fa fa-square tx-8 mg-r-5"></i> Tamanho: <strong><?php print htmlspecialchars($carpro->tamanho); ?></strong></span></div>
									<?php } ?>
									<div><span class="tx-12"><i class="fa fa-square tx-8 mg-r-5"></i> Qnt: <strong><?php print htmlspecialchars($carpro->quantidade); ?></strong></span></div>
									<div>
										<span class="tx-12">
											<?php echo "<i class=\"fa fa-square tx-8 mg-r-5\"></i> V. Unitário: <strong>R$: " . number_format($valorItem, 2, ',', '.') . "</strong>"; ?>
										</span>
									</div>
									<div><span class="tx-12">
											<?php
											$meiom = $connect->query("SELECT * FROM store_o WHERE idp = '" . $carpro->idpedido . "' AND status = '0' AND idu='$idu' AND meioameio='1' AND id_referencia='$carpro->referencia'");
											$meiomc = $meiom->rowCount();
											?>
											<?php if ($meiomc > 0) { ?>
												<i class="fa fa-square mg-r-5"></i> <?= $meiomc; ?> Sabores:<br><strong>
													<?php while ($meiomv = $meiom->fetch(PDO::FETCH_OBJ)) {
														echo "- " . htmlspecialchars($meiomv->nome) . "<br>";
													} ?>
												</strong>
											<?php } ?>

											<?php
											$adcionais = $connect->query("SELECT valor, nome FROM store_o WHERE idp = '" . $carpro->idpedido . "' AND status = '0' AND idu='$idu' AND meioameio='0' AND id_referencia='" . $carpro->referencia . "'");
											$adcionaisc = $adcionais->rowCount();
											?>
											<?php if ($adcionaisc > 0) { ?>
												<i class="fa fa-plus-square mg-r-5"></i> Adicionais/Ingredientes:<br>
												<?php while ($adcionaisv = $adcionais->fetch(PDO::FETCH_OBJ)) {
													echo "- R$: " . number_format((float)$adcionaisv->valor, 2, ',', '.') . " | <strong>" . htmlspecialchars($adcionaisv->nome) . "</strong><br>";
												} ?>
											<?php } ?>
										</span></div>
									<div class="row mg-t-10">
										<div class="col-7"><strong>Total do Item</strong></div>
										<div class="col-5"><strong>R$: <?= number_format($totalItem, 2, ',', '.'); ?></strong></div>
									</div>
									<div align="right">
										<a href="./?apagaritemp=<?= htmlspecialchars($carpro->idpedido); ?>&iditemp=<?= htmlspecialchars($carpro->id); ?>" style="color:#CC0000">
											<i class="fa fa-minus-square mg-r-5 tx-danger tx-9"></i><span class="tx-12">Excluir</span>
										</a>
									</div>
								</div>
							</div>
							<hr>
						</div>
					</div>
			<?php
				}
			} ?>

			<div class="row">
				<div class="col-7">SubTotal</div>
				<div class="col-5">R$: <?php if (isset($somando->soma)) {
											echo number_format($somando->soma, 2, ',', '.');
										} else {
											print "0,00";
										} ?></div>
			</div>

			<?php
			// Calcular o total adicional
			$totalAdicionais = 0;
			$produtosca->execute(); // Reset the cursor to loop again for cálculo adicional
			while ($carpro = $produtosca->fetch(PDO::FETCH_OBJ)) {
				$opcionais = $connect->query("SELECT valor, quantidade FROM store_o WHERE idp = '" . $carpro->idpedido . "' AND status = '0' AND idu='$idu' AND meioameio='0' AND id_referencia='" . $carpro->referencia . "'");
				while ($valork = $opcionais->fetch(PDO::FETCH_OBJ)) {
					$quantop = (int) $valork->quantidade;
					$valorop = (float) $valork->valor;
					$totalAdicionais += $valorop * $quantop;
				}
			}
			$opctg = number_format($totalAdicionais, 2, ',', '.');
			?>
			<?php if ($totalAdicionais > 0) { ?>
				<div class="row mg-t-10">
					<div class="col-7">Valores Adicionais</div>
					<div class="col-5">R$: <?= $opctg; ?></div>
				</div>
			<?php } ?>
			<div class="row mg-t-10">
				<div class="col-7"><strong>Valor do Pedido</strong></div>
				<div class="col-5"><strong>R$: <?php if (isset($somando->soma)) {
													$geral = $somando->soma + $totalAdicionais;
													echo number_format($geral, 2, ',', '.');
												} else {
													print "0,00";
												} ?></strong></div>
			</div>

			<?php if (isset($geral) && $geral > $dadosempresa->dfree) { ?>
				<hr>
				<div class="alert alert-success" role="alert" style="margin-bottom:-5px">
					<strong class="tx-success"><i class="fa fa-thumbs-o-up mg-r-5"></i> Entrega Grátis.</strong>
				</div>
			<?php } ?>
		</div>
	</div>
<?php } ?>