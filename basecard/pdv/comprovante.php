<?php

if (isset($_COOKIE['pdvx'])) {
    $cod_id = $_COOKIE['pdvx'];
} else {
    header("location: sair.php");
}

session_start();
date_default_timezone_set('America/Sao_Paulo');
include_once('../../funcoes/Conexao.php');
include_once('../../funcoes/Key.php');


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Pedido</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Premium Quality and Responsive UI for Dashboard.">
    <meta name="author" content="ThemePixels">
    <title>RECEBIMENTO DE PEDIDOS</title>
    <link href="../lib/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../lib/Ionicons/css/ionicons.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/slim.css">
    <style>
        .card-header {
            background-color: #28a745;
            color: white;
        }

        .card-footer {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .toolbar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .toolbar button {
            margin-left: 10px;
        }
    </style>
</head>

<body>

    <div class="slim-navbar">
        <div class="container">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" id="shareBtn">
                        <i class="icon ion-ios-undo-outline"></i>
                        <span>Compartilhar</span>
                    </a>
                </li>

                <li class="nav-item">
                    <form action="pdv.php" method="post" class="d-flex justify-content-center">
                        <button style="cursor: pointer;outline:none;" type="submit" class="nav-link w-100 shadow-none"><i class="icon ion-ios-analytics-outline"></i>Voltar</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <?php
    if (isset($_POST['codigop'])) {

        $pegadadosgerais   = $connect->query("SELECT * FROM config WHERE id='$cod_id'");
        $dadosgerais    = $pegadadosgerais->fetch(PDO::FETCH_OBJ);
        $nomeempresa     = $dadosgerais->nomeempresa;

        date_default_timezone_set('' . $dadosgerais->fuso . '');

        $codigop  = $_POST['codigop'];

        $pedido    = $connect->query("SELECT * FROM pedidos WHERE idpedido='$codigop'");
        $pedido    = $pedido->fetch(PDO::FETCH_OBJ);
        $celcli    = $pedido->celular;

        // var_dump($pedido);

        $produtoscay   = $connect->query("SELECT * FROM store WHERE idsecao = '$codigop' ORDER BY id DESC");

        $produtoscaxy   = $connect->query("SELECT * FROM store WHERE idsecao = '$codigop' ORDER BY id DESC");

        $produtosca   = $connect->query("SELECT * FROM store WHERE idsecao = '$codigop' ORDER BY id DESC");
        $produtoscx   = $produtosca->rowCount();


        $item      = $connect->query("SELECT * FROM store WHERE idsecao='$codigop'");
        $opcionais = $connect->query("SELECT * FROM store_o WHERE ids='$codigop'");

        // Define a consulta
        $query = "SELECT * FROM `registrospagamentos` WHERE `idpedido` = :idpedido";

        // Prepara a consulta
        $stmt = $connect->prepare($query);
        $stmt->bindParam(':idpedido', $codigop, PDO::PARAM_INT);

        // Executa a consulta
        $stmt->execute();

        // Obtém os resultados
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        function formatCurrency($num)
        {
            if (preg_match('/' . "," . '/', $num)) {
                return formatValorMoedaDatabase($num);
            } else {
                $num = formatMoedaBr($num);
                return formatValorMoedaDatabase($num);
            }
        }
        function formatValorMoedaDatabase($num)
        {
            return str_replace(',', '.', preg_replace('#[^\d\,]#is', '', $num));
        }
        function formatMoedaBr($num)
        {
            return $num;
        }

        if (!function_exists('truncarTexto')) {
            /**
             * Trunca o texto após o primeiro espaço e adiciona '...' se o texto for maior que o comprimento mínimo.
             *
             * @param string $texto O texto a ser truncado.
             * @param int $tamanho Máximo comprimento do texto antes do truncamento.
             * @param int $comprimento_minimo Comprimento mínimo para truncar o texto.
             * @return string Texto truncado com '...' adicionado após o primeiro espaço.
             */
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
        }

    ?>



        <div class="container ">
            <div class="toolbar">
                <button class="btn btn-primary" onclick="printDiv('print')">Imprimir</button>
                <form action="historicopagamento.php" method="post" class="d-flex justify-content-center">
                    <input type="hidden" name="id_pedido" value="<?php print $codigop; ?>" />
                    <button class="btn btn-success" type="submit"><i class="fa fa-search"></i> Pesquisar Pagamento</button>
                </form>
            </div>

            <div style="width: 100%;height:100%;">
                <div id="modal-paid">
                    <div class="card card-people-list pd-15 mg-b-10" style="background-color:#fdfbe3;padding:1rem;">

                        <div id="print" style="font-family: Arial;">
                            <center>
                                <p class="tx-15"><strong>RESUMO DO PEDIDO</strong></p>
                            </center>

                            <center>
                                <p class="tx-12">Comanda Balcão</span></p>

                                <center>
                                    <p class="tx-12"><?= $pedido->data; ?> às <?= $pedido->hora; ?></span></p>
                                    <center>
                                        <p class="tx-12">Nº <?= $codigop; ?></span></p>
                                        <hr />
                                        <?php
                                        while ($carpro = $produtosca->fetch(PDO::FETCH_OBJ)) {
                                            $nomepro  = $connect->query("SELECT nome FROM produtos WHERE id = '" . $carpro->produto_id . "'");
                                            $nomeprox = $nomepro->fetch(PDO::FETCH_OBJ);
                                        ?>

                                            <p style="margin-left:10px;" align="left"><span class="tx-12"><b>** Item: </b><?php print $nomeprox->nome; ?></span></p>

                                            <?php if ($carpro->tamanho != "N") { ?>
                                                <p style="margin-left:10px;" align="left"><span class="tx-12"><b>- Tamanho: </b><?php print $carpro->tamanho; ?></span></p>
                                            <?php } ?>

                                            <p style="margin-left:10px;" align="left"><span class="tx-12"><b>- Qnt:</b> <?php print $carpro->quantidade; ?></span></p>

                                            <p style="margin-left:10px;" align="left"><span class="tx-12"><b>- V. Unitário:</b> <?php echo "R$: " . $carpro->valor; ?></span></p>

                                            <?php if ($carpro->obs) { ?>
                                                <p style="margin-left:10px;" align="left"><span class="tx-12"><b>- Obs:</b> <?php echo $carpro->obs; ?></span></p>
                                            <?php } else { ?>
                                                <p style="margin-left:10px;" align="left"><span class="tx-12"><b>- Obs:</b> Não</span></p>
                                            <?php } ?>

                                            <?php
                                            $meiom  = $connect->query("SELECT * FROM store_o WHERE idp = '" . $carpro->idpedido . "' AND status = '1' AND idu='$cod_id' AND meioameio='1'");
                                            $meiomc = $meiom->rowCount();
                                            ?>

                                            <?php if ($meiomc > 0) { ?>
                                                <p style="margin-left:10px;" align="left"><span class="tx-12"><b>* <?= $meiomc; ?> Sabores:</b></span></p>
                                                <p style="margin-left:10px;" align="left"><span class="tx-12">
                                                        <?php while ($meiomv = $meiom->fetch(PDO::FETCH_OBJ)) { ?>
                                                            <?= $meiomv->nome . "<br>"; ?>
                                                        <?php } ?>
                                                    </span></p>
                                            <?php } ?>

                                            <?php
                                            $adcionais  = $connect->query("SELECT * FROM store_o WHERE idp = '" . $carpro->idpedido . "' AND status = '1' AND idu='$cod_id' AND meioameio='0' AND id_referencia='$carpro->referencia'");
                                            $adcionaisc = $adcionais->rowCount();
                                            ?>

                                            <?php if ($adcionaisc > 0) { ?>
                                                <p style="margin-left:10px;" align="left"><span class="tx-12"><b>* Adicionais/Ingredientes:</b></p>
                                                <p style="margin-left:10px;" align="left"><span class="tx-12">
                                                        <?php while ($adcionaisv = $adcionais->fetch(PDO::FETCH_OBJ)) { ?>
                                                            <?= "-  R$: " . $adcionaisv->valor . " | " . $adcionaisv->nome . "<br>"; ?>
                                                        <?php } ?>
                                                    </span></p>
                                            <?php } ?>
                                            <center>=========================</center>
                                            </p>
                                        <?php } ?>

                                        <?php
                                        $nome = str_replace('%20', ' ', $pedido->nome);
                                        ?>

                                        <br>
                                        <center><strong>DADOS DO CLIENTE</strong></center>
                                        <hr />
                                        <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Nome: </b><?= $nome; ?></span></p>
                                        <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Celular: </b><?= $pedido->celular; ?></span></p>
                                        <?php if ($pedido->mesa > 0) { ?>
                                            <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Mesa: </b><?= $pedido->mesa; ?></span></p>
                                            <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Pessoa na Mesa: </b><?= $pedido->pessoas; ?></span></p>
                                            <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Obs: </b><?= $pedido->obs; ?></span></p>
                                        <?php } ?>
                                        <?php if ($pedido->fpagamento == "DINHEIRO" || $pedido->fpagamento == "CARTAO" || $pedido->fpagamento == "CARTÃO") { ?>
                                            <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Rua: </b><?= $pedido->rua; ?></span></p>
                                            <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Nº: </b><?= $pedido->numero; ?></span></p>
                                            <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Bairro: </b><?= $pedido->bairro; ?></span></p>
                                            <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Complemento: </b><?= $pedido->complemento; ?></span></p>
                                        <?php } ?>
                                        <br>
                                        <center><strong>PAGAMENTO</strong></center>
                                        <hr />
                                        <?php

                                        if ($pedido->fpagamento == "DINHEIRO") {
                                            print $delivery = "<p style=\"margin-left:10px;\" align=\"left\"><span class=\"tx-12\"><b>Dinheiro na Entrega</b></span></p>";
                                        }
                                        if ($pedido->fpagamento == "CARTAO" || $pedido->fpagamento == "CARTÃO") {
                                            print $delivery = "<p style=\"margin-left:10px;\" align=\"left\"><span class=\"tx-12\"><b>Cartão na Entrega</b></span></p>";
                                        }
                                        if ($pedido->fpagamento == "MESA") {
                                            print $delivery = "<p style=\"margin-left:10px;\" align=\"left\"><span class=\"tx-12\"><b>Na Mesa</b></span></p>";
                                        }
                                        if ($pedido->fpagamento == "BALCAO") {
                                            print $delivery = "<p style=\"margin-left:10px;\" align=\"left\"><span class=\"tx-12\"><b>No Balcão</b></span></p>";
                                        }
                                        ?>
                                        <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Subtotal: R$: </b><?= formatMoedaBr(formatCurrency($pedido->vsubtotal)) ?></span></p>
                                        <?php if ($pedido->vadcionais > 0.00) { ?>
                                            <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Adicionais: R$: </b><?= formatMoedaBr(formatCurrency($pedido->vadcionais)) ?></span></p>
                                        <?php } ?>
                                        <?php if ($pedido->taxa > 0) { ?>
                                            <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Taxa de Entrega: R$: </b><?= formatMoedaBr(formatCurrency($pedido->taxa)) ?></span></p>
                                            <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Total Geral: </b> R$: <?= formatMoedaBr(formatCurrency(($pedido->vtotal))) ?></span></p>
                                        <?php } else { ?>
                                            <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Total Geral: </b> R$: <?= formatMoedaBr(formatCurrency($pedido->vtotal)) ?></span></p>
                                        <?php } ?>
                                        <?php if ($pedido->troco > 0) { ?>
                                            <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Troco para: R$: </b><?= formatMoedaBr(formatCurrency($pedido->troco)) ?></span></p>
                                            <?php $ValorDoTroco = $pedido->troco - $pedido->vtotal ?>
                                            <p style="margin-left:10px;" align="left"><span class="tx-12"><b>Valor do Troco: R$: </b><?= formatMoedaBr(formatCurrency($ValorDoTroco)) ?></span></p>
                                        <?php } ?>
                                        <br>

                                        <?php

                                        // Define a consulta
                                        $query = "SELECT * FROM `registrospagamentos` WHERE `idpedido` = :idpedido";
                                        // Prepara a consulta
                                        $stmt = $connect->prepare($query);
                                        $stmt->bindParam(':idpedido', $codigop, PDO::PARAM_INT);
                                        // Executa a consulta
                                        $stmt->execute();
                                        // Obtém os resultados
                                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                        // Verifica se há resultados
                                        if (count($results) > 0) {
                                            // Loop pelos resultados
                                            foreach ($results as $row) {
                                                $nome_search = $row['nome'];
                                                $texto_truncado_nome = truncarTexto($nome_search, 9, 5);

                                                $data_search = $row['data_registro'];
                                                $texto_truncado_data = truncarTexto($data_search, 5, 5);

                                                $jsonPagamento = $row['dados_pagamentos'];

                                                if (!empty($jsonPagamento)) {
                                                    $pagamentoArray = json_decode($jsonPagamento, true);

                                                    if (json_last_error() === JSON_ERROR_NONE && isset($pagamentoArray['dados']) && !empty($pagamentoArray['dados'])) {

                                                        echo '
                                                        <hr>
                                                        <br>
                                                        <center><strong>PEDIDO PAGO</strong></center>';

                                                        echo '<div style="margin-left:10px;">';
                                                        // echo '<p class="tx-12" style="margin-left:0px;" align="left"><b>Formas de Pagamento:</b></p>';
                                                        echo '<p style="margin-left:0px;" align="left"><span class="tx-12"><b>Tipo de Pagamento: </b>';
                                                        echo htmlspecialchars($pagamentoArray['tipo']);
                                                        echo '</span></p>';

                                                        if ($pagamentoArray['tipo'] === "rateio") {
                                                            echo '<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">';
                                                            echo '<thead>';
                                                            echo '<tr>';
                                                            echo '<th>Método</th>';
                                                            echo '<th>Quantidade</th>';
                                                            echo '</tr>';
                                                            echo '</thead>';
                                                            echo '<tbody>';

                                                            foreach ($pagamentoArray['dados'] as $pagamento) {
                                                                // Remover caracteres não numéricos da quantidade e converter para float
                                                                $quantidadeLimpa = preg_replace('/[^0-9,\.]/', '', $pagamento['quantidade']);
                                                                $quantidadeFloat = floatval(str_replace(',', '.', $quantidadeLimpa));

                                                                // Formatar a quantidade como moeda
                                                                $quantidadeFormatada = number_format($quantidadeFloat, 2, ',', '.');
                                                                echo '<tr>';
                                                                echo '<td>' . htmlspecialchars($pagamento['metodo']) . '</td>';
                                                                echo '<td> R$:' . htmlspecialchars($quantidadeFormatada) . '</td>';
                                                                echo '</tr>';
                                                            }

                                                            echo '</tbody>';
                                                            echo '</table>';
                                                            echo '</div>';
                                                        } else if ($pagamentoArray['tipo'] == "a vista") {

                                                            echo '<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">';
                                                            echo '<thead>';
                                                            echo '<tr>';
                                                            echo '<th>Método</th>';
                                                            echo '<th>Quantidade</th>';
                                                            echo '</tr>';
                                                            echo '</thead>';
                                                            echo '<tbody>';


                                                            foreach ($pagamentoArray['dados'] as $pagamento) {
                                                                // Remover caracteres não numéricos da quantidade e converter para float
                                                                $quantidadeLimpa = preg_replace('/[^0-9,\.]/', '', $pagamento['quantidade']);
                                                                $quantidadeFloat = floatval(str_replace(',', '.', $quantidadeLimpa));

                                                                // Formatar a quantidade como moeda
                                                                $quantidadeFormatada = number_format($quantidadeFloat, 2, ',', '.');
                                                                echo '<tr>';
                                                                echo '<td>' . htmlspecialchars($pagamento['metodo']) . '</td>';
                                                                echo '<td> R$:' . htmlspecialchars($quantidadeFormatada) . '</td>';
                                                                echo '</tr>';
                                                            }

                                                            echo '</tbody>';
                                                            echo '</table>';
                                                            echo '</div>';
                                                        } else if ($pagamentoArray['tipo'] == "parcial") {

                                                            echo '<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">';
                                                            echo '<thead>';
                                                            echo '<tr>';
                                                            echo '<th>Método</th>';
                                                            echo '<th>Quantidade</th>';
                                                            echo '</tr>';
                                                            echo '</thead>';
                                                            echo '<tbody>';


                                                            foreach ($pagamentoArray['dados'] as $pagamento) {
                                                                // Remover caracteres não numéricos da quantidade e converter para float
                                                                $quantidadeLimpa = preg_replace('/[^0-9,\.]/', '', $pagamento['quantidade']);
                                                                $quantidadeFloat = floatval(str_replace(',', '.', $quantidadeLimpa));

                                                                // Formatar a quantidade como moeda
                                                                $quantidadeFormatada = number_format($quantidadeFloat, 2, ',', '.');

                                                                echo '<tr>';
                                                                echo '<td>' . htmlspecialchars($pagamento['metodo']) . '</td>';
                                                                echo '<td> R$ ' . htmlspecialchars($quantidadeFormatada) . '</td>';
                                                                echo '</tr>';
                                                            }


                                                            echo '</tbody>';
                                                            echo '</table>';
                                                            echo '</div>';
                                                        }
                                                    } else {
                                                        echo '<p style="margin-left:0px;" align="left"><span class="tx-12"><b>Erro ao decodificar os dados de pagamento.</b></span></p>';
                                                    }
                                                } else {
                                                    echo '<p>Pagamento não Reconhecido...</p>';
                                                }
                                            }

                                            if ($row['valor_dinheiro'] && $row['valor_troco']) {

                                                echo '<div style="margin-left:10px;margin-top:1rem;">';
                                                echo '<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">';
                                                echo '<thead>';
                                                echo '<tr>';
                                                echo '<th>Caixa Recebido</th>';
                                                echo '<th>Troco</th>';
                                                echo '</tr>';
                                                echo '</thead>';
                                                echo '<tbody>';



                                                // Remover caracteres não numéricos da quantidade e converter para float
                                                $quantidadeLimpa = preg_replace('/[^0-9,\.]/', '', $pagamento['quantidade']);
                                                $quantidadeFloat = floatval(str_replace(',', '.', $quantidadeLimpa));

                                                // Formatar a quantidade como moeda
                                                $quantidadeFormatadaDinheiro = number_format($quantidadeFloat, 2, ',', '.');
                                                echo '<tr>';
                                                echo '<td> R$:' . htmlspecialchars($quantidadeFormatadaDinheiro) . '</td>';
                                                echo '<td> R$:' . htmlspecialchars(number_format($row['valor_troco'], 2, ',', '.')) . '</td>';
                                                echo '</tr>';

                                                echo '</tbody>';
                                                echo '</table>';
                                                echo '</div>';

                                            }
                                        } else {
                                            echo '<p><strong>Nenhum histórico de pagamento encontrado para o pedido.</strong></p> <br>';
                                        }

                                        ?>

                                        <p style="margin-left:10px;" align="right"><span class="tx-11"><b><?= date("d-m-Y H:i:s"); ?></b></span></p>
                                    </center>
                                </center>
                            </center>

                        </div>

                    </div>
                </div>


                <script language="javascript">
                    function printDiv(DivID) {

                        var disp_setting = "toolbar=yes,location=no,";
                        disp_setting += "directories=yes,menubar=yes,";
                        disp_setting += "scrollbars=yes,width=450, height=600, left=100, top=25";
                        var content_vlue = document.getElementById(DivID).innerHTML;
                        var docprint = window.open("", "", disp_setting);
                        docprint.document.open();
                        docprint.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"');
                        docprint.document.write('"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">');
                        docprint.document.write('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">');
                        docprint.document.write('<head><title>COMANDA BALCAO</title>');
                        docprint.document.write('<style type="text/css">body{ margin:0px;');
                        docprint.document.write('font-family:verdana,Arial;color:#000;');
                        docprint.document.write('font-family:Verdana, Geneva, sans-serif; font-size:12px;}');
                        docprint.document.write('a{color:#000;text-decoration:none;} </style>');
                        docprint.document.write('</head><body onLoad="self.print()">');
                        docprint.document.write(content_vlue);
                        docprint.document.write('</body></html>');
                        docprint.document.close();
                        docprint.focus();
                    }
                </script>
            </div>

        </div>

    <?php

    } else {
    ?>

        <div class="card" style="margin-top: 0.5rem;">
            <div class="card-header bg-danger text-white">
                <h4 class="card-title">Erro</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-danger" role="alert">
                    <strong>Erro:</strong> Não foi possível buscar o Comprovante...<br>
                </div>
            </div>
        </div>

    <?php
    }
    ?>

    <body>

</html>