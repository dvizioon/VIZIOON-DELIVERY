<form id="parcial-form" method="POST" action="./forms/parcial_form.php">
    <!-- Valores inputados -->
    <?php
    $nome = str_replace('%20', ' ', $pedido->nome);
    ?>

    <input type="<?= $typeControl; ?>" name="idu_empresa" value="<?= $cod_id; ?>">
    <input type="<?= $typeControl; ?>" name="nome_cliente" value="<?= $nome; ?>">
    <input type="<?= $typeControl; ?>" name="id_pedido" value="<?= $codigop ?>">
    <input type="<?= $typeControl; ?>" name="status_pedido" value="2">
    <input type="<?= $typeControl; ?>" name="tipo_pedido" value="<?= $delivery ?>">
    <input type="<?= $typeControl; ?>" name="mesa_pedido" value="<?= $pedido->pessoas; ?>">
    <input type="<?= $typeControl; ?>" name="data_registro" value="<?= date("d-m-Y H:i:s"); ?>">
    <input type="<?= $typeControl; ?>" name="nome_atendente" value="<?= $nome_funcionario; ?>">
    <?php
    if ($comissao_ativa) {
    ?>
        <?php if ($total_comissao): ?>
            <input type="<?= $typeControl; ?>" name="valor_comissao" value="<?php echo number_format(($total_comissao['total_comissao'] / 100) * $pedido->vtotal, 2, ',', '.'); ?>">
        <?php endif; ?>

    <?php
    };
    ?>

    <input type="<?= $typeControl; ?>" name="subtotal_geral" value="<?= formatMoedaBr(formatCurrency($pedido->vtotal)) ?>">
    <?php if ($pedido->vadcionais > 0.00) { ?>
        <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Adicionais: R$: </b><?= formatMoedaBr(formatCurrency($pedido->vadcionais)) ?></span></p>
    <?php } ?>
    <?php if ($pedido->taxa > 0) { ?>
        <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Taxa de Entrega: R$: </b><?= formatMoedaBr(formatCurrency($pedido->taxa)) ?></span></p>
        <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Total Geral: </b> R$: <?= formatMoedaBr(formatCurrency(($pedido->vtotal))) ?></span></p>
    <?php } else { ?>
        <input type="<?= $typeControl; ?>" name="total_geral" value="<?= formatMoedaBr(formatCurrency($pedido->vtotal)) ?>">
    <?php } ?>
    <?php if ($pedido->troco > 0) { ?>
        <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Troco para: R$: </b><?= formatMoedaBr(formatCurrency($pedido->troco)) ?></span></p>
        <?php $ValorDoTroco = $pedido->troco - $pedido->vtotal ?>
        <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Valor do Troco: R$: </b><?= formatMoedaBr(formatCurrency($ValorDoTroco)) ?></span></p>
    <?php } ?>

    <div class="mt-3">
        <div class="form-group">
            <label for="modal_finalizacao-metodoPagamento-parcial">Método de Pagamento &#8674; [<span class="text-success"> Parcial </span>]</label>
        </div>
        <div class="value-container d-flex container-parcial">
            <!-- Container inicial -->
            <div class="container-box-items">
                <select style="width:100%;" name="metodoPagamento[]" class="modal_finalizacao-metodoPagamento-parcial">
                    <?php foreach ($metodospagamentos as $metodo) { ?>
                        <option value="<?php echo htmlspecialchars($metodo->metodopagamento); ?>">
                            <?php echo htmlspecialchars($metodo->metodopagamento); ?>
                        </option>
                    <?php } ?>
                </select>
                <input style="width:100%;" type="text" name="quantidade[]" class="parcial-value dinheiro" value="<?php echo !empty($valor_total_faltando) ? formatMoedaBr(formatCurrency($valor_total_faltando)) : formatMoedaBr(formatCurrency($pedido->vtotal)); ?>">
                <div class="tab-buttons-simples">
                    <button type="button" onclick="alert('Vc não pode Remover...')">-</button>
                    <button type="button" onclick="addContainerParcial()">+</button>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex" style="gap:1rem; align-items:center;justify-content:flex-end;">
        <button type="submit" class="modal_finalizacao-btn-salvar">Confirmar Pedido <i class="fa fa-arrow-right mg-l-5"></i></button>
        <button type="button" class="modal_finalizacao-btn-close" onclick="closeModal()">Fechar</button>
    </div>
</form>

<script>
    // Obtém o valor total inicial formatado pelo PHP
    const valorTotalInicialParcialFormatado = "<?php echo !empty($valor_total_faltando) ? formatMoedaBr(formatCurrency($valor_total_faltando)) : formatMoedaBr(formatCurrency($pedido->vtotal)); ?>";

    // Converte o valor total inicial formatado para número
    const valorTotalInicialParcial = parseFloat(valorTotalInicialParcialFormatado.replace(',', '.'));

    // Função para calcular o valor total dos inputs
    function calcularValorTotalParcial() {
        const values = document.querySelectorAll('.parcial-value');
        return Array.from(values).reduce((total, input) => {
            const value = parseFloat(input.value.replace(',', '.'));
            return total + (isNaN(value) ? 0 : value);
        }, 0);
    }

    // Função para adicionar um novo container
    function addContainerParcial() {
        const container = document.querySelector('.container-parcial');
        const valorAtual = calcularValorTotalParcial();
        const valorRestante = valorTotalInicialParcial - valorAtual;

        if (valorRestante <= 0) {
            alert('O valor restante é insuficiente para adicionar mais um container.');
            return;
        }

        const newContainer = document.createElement('div');
        newContainer.classList.add('container-box-items');
        newContainer.innerHTML = `
            <select style="width:100%;" name="metodoPagamento[]" class="modal_finalizacao-metodoPagamento-parcial">
                <?php foreach ($metodospagamentos as $metodo) { ?>
                    <option value="<?php echo htmlspecialchars($metodo->metodopagamento); ?>">
                        <?php echo htmlspecialchars($metodo->metodopagamento); ?>
                    </option>
                <?php } ?>
            </select>
            <input style="width:100%;" type="text" name="quantidade[]" class="parcial-value dinheiro" value="${valorRestante.toFixed(2).replace('.', ',')}">
            <div class="tab-buttons-simples">
                <button type="button" onclick="removeContainerParcial(this)">-</button>
                <button type="button" onclick="addContainerParcial()">+</button>
            </div>
        `;
        container.appendChild(newContainer);
    }

    // Função para remover um container
    function removeContainerParcial(button) {
        var container = button.parentElement.parentElement;
        container.remove();
        updateValuesParcial();
    }

    // Função para atualizar os valores totais
    function updateValuesParcial() {
        const valorAtual = calcularValorTotalParcial();
        console.log("Total Calculado: ", valorAtual);
    }

    // Função para fechar o modal
    function closeModalParcial() {
        document.getElementById('modal_finalizacao-parcial').style.display = 'none';
    }
</script>

<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../js/moeda.js"></script>


<script>
    $('.dinheiro').mask('#.##0,00', {
        reverse: true
    });
</script>