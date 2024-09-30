<!-- Metodo Simples -->



<form id="simples-form" method="POST" action="./forms/simples_form.php">
    <!-- Valores inputados -->
    <!-- Valor Total não Replicado... -->
    <?php


    // Configurações do Desconto
    $valorTotalPedidoDescontadoSimples = isset($pedido->descontos) && $pedido->descontos !== "0" ? $pedido->descontos : $pedido->vtotal;
    $valorTotal = isset($pedido->desconto_opcional) && $pedido->desconto_opcional !== "0" ? $valorTotalPedidoDescontadoSimples - $pedido->desconto_opcional : $valorTotalPedidoDescontado;
    $typeControl = "hidden";
    ?>

    <?php
    // echo isset($pedido->descontos) && $pedido->descontos !== "0" ? "<br> <p class='text-danger'>De:".$pedido->vtotal. "</p> <p class='text-success'>Para:" . $pedido->descontos . "</p>" : "";
    ?>


    <input type="<?= $typeControl; ?>" name="idu_empresa" value="<?= $cod_id; ?>">
    <input type="<?= $typeControl; ?>" name="nome_cliente" value="<?= $nome; ?>">
    <input type="<?= $typeControl; ?>" name="id_pedido" value="<?= $codigop ?>">
    <input type="<?= $typeControl; ?>" name="status_pedido" value="5">
    <input type="<?= $typeControl; ?>" name="tipo_pedido" value="<?= $delivery ?>">
    <input type="<?= $typeControl; ?>" name="mesa_pedido" value="<?= $pedido->pessoas; ?>">
    <input type="<?= $typeControl; ?>" name="data_registro" value="<?= date("d-m-Y H:i:s"); ?>">
    <input type="<?= $typeControl; ?>" name="subtotal_geral" id="subtotal_geral" value="<?= formatMoedaBr(formatCurrency($valorTotal)) ?>">
    <input type="<?= $typeControl; ?>" name="total_geral" id="total_geral" value="<?= formatMoedaBr(formatCurrency($valorTotal)) ?>">
    <input type="<?= $typeControl; ?>" name="nome_metodo_pagamento" id="nome_metodo_pagamento">
    <input type="<?= $typeControl; ?>" name="nome_atendente" value="<?= $nome_funcionario; ?>">
    <input type="<?= $typeControl; ?>" name="celular" value="<?= $pedido->celular; ?>">
    <?php
    if ($comissao_ativa) {
    ?>

        <?php if ($total_comissao): ?>
            <input type="<?= $typeControl; ?>" name="valor_comissao" value="<?php echo number_format(($total_comissao['total_comissao'] / 100) * $valorTotal, 2, ',', '.'); ?>">
        <?php endif; ?>

    <?php
    };
    ?>

    <div class="form-group mt-3">
        <label for="modal_finalizacao-metodoPagamento">Método de Pagamento &#8674; [<span class="text-success"> Simples </span>]</label>
        <select class="form-control" name="metodo_pagamento" id="modal_finalizacao-metodoPagamento">
            <?php foreach ($metodospagamentos as $metodo) { ?>
                <option value="<?php echo htmlspecialchars($metodo->id); ?>">
                    <?php echo htmlspecialchars($metodo->metodopagamento); ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div>
        <h4>Calculo Manual</h4>
        <div style="display:flex; gap:1rem;">
            <div class="w-100">
                <label for="valor_dinheiro">Valor em dinheiro</label>
                <input class="form-control" type="text" id="valor_dinheiro" name="valor_dinheiro" oninput="atualizarValores()" placeholder="Digite o valor em dinheiro">
            </div>
            <div class="w-100">
                <label for="valor_troco">Troco</label>
                <input class="form-control disabled-input" type="text" id="valor_troco" name="valor_troco" readonly placeholder="O valor do troco será calculado automaticamente">
            </div>
        </div>
    </div>

    <div class="d-flex flex-row flex-md-row justify-content-end align-items-center mt-3 " style="gap:1rem; align-items:center;justify-content:flex-end;">
        <button class="modal_finalizacao-btn-salvar" type="submit">Confirmar Pedido <i class="fa fa-arrow-right mg-l-5"></i></button>
        <button type="button" class="modal_finalizacao-btn-close" onclick="closeModal()">Fechar</button>
    </div>
</form>



<!-- Script pro Simples -->
<script>
    function atualizarValores() {
        const metodoPagamentoSelecionado = document.getElementById('modal_finalizacao-metodoPagamento').selectedOptions[0].text.toLowerCase();
        const valorDinheiro = parseFloat(document.getElementById('valor_dinheiro').value.replace(',', '.')) || 0;
        const totalGeral = parseFloat(document.getElementById('total_geral').value.replace(',', '.')) || 0;
        const valorTroco = Math.max(0, valorDinheiro - totalGeral);

        document.getElementById('valor_troco').value = valorTroco.toFixed(2).replace('.', ',');
    }

    function atualizarCampoValorDinheiro() {
        const metodoPagamento = document.getElementById('modal_finalizacao-metodoPagamento').selectedOptions[0].text.toLowerCase();
        const campoValorDinheiro = document.getElementById('valor_dinheiro');
        const campoValorTroco = document.getElementById('valor_troco');

        if (metodoPagamento === 'dinheiro') {
            campoValorDinheiro.disabled = false;
            campoValorTroco.classList.remove('disabled-input');
        } else {
            campoValorDinheiro.disabled = true;
            campoValorTroco.classList.add('disabled-input');
            campoValorTroco.value = '';
        }
    }

    document.getElementById('modal_finalizacao-metodoPagamento').addEventListener('change', function() {
        atualizarCampoValorDinheiro();
        atualizarValores();
    });

    // Inicializa o estado dos campos ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {

        var selectMetodos = document.getElementById('modal_finalizacao-metodoPagamento');
        var campoNomeMetodo = document.getElementById('nome_metodo_pagamento');

        function atualizarNomeMetodo() {
            var selectedOption = selectMetodos.options[selectMetodos.selectedIndex];
            campoNomeMetodo.value = selectedOption.text;
        }

        selectMetodos.addEventListener('change', atualizarNomeMetodo);
        atualizarNomeMetodo();
        atualizarCampoValorDinheiro();
    });
</script>