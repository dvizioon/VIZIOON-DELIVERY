<?php

if (isset($_COOKIE['pdvx'])) {
    $cod_id = $_COOKIE['pdvx'];
} else {
    header("location: sair.php");
}

$stmt = $connect->prepare("SELECT * FROM efeitosSonoros WHERE idu = ? AND padrao = 'h'");
$stmt->execute([$cod_id]);
$efeito_padrao = $stmt->fetch(PDO::FETCH_OBJ);

// var_dump($efeito_padrao);

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

function renderTable($pedidos)
{
    foreach ($pedidos as $pedidossx) {
        global $efeito_padrao;


        $status_verificar = "";
        if ($pedidossx->status == 1) {
            // echo basename($efeito_padrao->caminho);
            $status = "<button class=\"btn btn-warning btn-sm\">Novo Pedido</button>";
            $status_verificar = "Novo Pedido";
            if (empty($efeito_padrao)) {
                echo "
            <script>
                var audio = new Audio('./sounds/campainha.mp3');
                audio.addEventListener('canplaythrough', function() {
                    audio.play();
                });
            </script>
            ";
            } else {
                echo "
            <script>
                var audio = new Audio('./sounds/" . htmlspecialchars(basename($efeito_padrao->caminho)) . "');
                audio.addEventListener('canplaythrough', function() {
                    audio.play();
                });
            </script>
            ";
            }
        } elseif ($pedidossx->status == 2) {
            $status_verificar = "Pedido Aceito";
            $status = "<button class=\"btn btn-info w-100 h-100 btn-sm\">Pedido Aceito</button>";
        } elseif ($pedidossx->status == 3) {
            $status_verificar = "Saiu para entrega";
            $status = "<button class=\"btn btn-warning w-100 h-100 btn-sm\">Saiu para entrega</button>";
        } elseif ($pedidossx->status == 4) {
            $status_verificar = "Disponivel para retirada";
            $status = "<button class=\"btn btn-purple w-100 h-100 btn-sm\">Disponivel para retirada</button>";
        } elseif ($pedidossx->status == 5) {
            $status_verificar = "Finalizado";
            $status = "<button class=\"btn btn-success w-100 h-100 btn-sm\">Finalizado</button>";
        } elseif ($pedidossx->status == 6) {
            $status_verificar = "Cancelado";
            $status = "<button class=\"btn btn-danger w-100 h-100 btn-sm\">Cancelado</button>";
        } elseif ($pedidossx->status == 7) {
            $status_verificar = "Entregue Cozinha";
            $status = "<button class=\"btn btn-purple btn-sm\">Entregue Cozinha</button>";
        } elseif ($pedidossx->status == 9) {
            $status_verificar = "Pagamento Incompleto";
            $status = "<button class=\"btn btn-warning btn-sm\">Parcial</button>";
        }

        // $delivery = $pedidossx->fpagamento == "DINHEIRO" || $pedidossx->fpagamento == "CARTAO" ? "<span style=\"color:#FF0000\">DELIVERY</span>" : $pedidossx->fpagamento;
        // Suponha que $pedidossx->fpagamento seja uma string JSON
        // ["Delivery","PIX"] -> Delivery éo Tipo é Pix éa Forma de Pagamento


        // Decodifica o JSON para um array PHP e acessa o primeiro elemento
        $primeiro_elemento_delivery  = (is_array($delivery_array = json_decode($pedidossx->fpagamento, true)) && !empty($delivery_array))
            ? $delivery_array[0]
            : null;

        // var_dump($primeiro_elemento_delivery);

        $delivery = $primeiro_elemento_delivery == "DELIVERY" ? "<span style=\"color:#FF0000\">DELIVERY</span>" : $pedidossx->fpagamento;

        // echo $delivery;

        $nome = $pedidossx->nome;
        $texto_truncado = truncarTexto($nome, 7, 5);

        $data = $pedidossx->data;
        $hora = $pedidossx->hora;
        $texto_truncado_data = truncarTexto($data, 5, 5);
        $texto_truncado_hora = truncarTexto($hora, 9, 5);
?>
        <tr>
            <td class="d-table-cell d-flex" style="align-items:center;gap:0.5rem"><?php print $pedidossx->id; ?> <i class="fa fa-reorder d-block d-md-none" style="font-size: 1.5rem;" aria-hidden="true"></i></td>
            <td class="d-table-cell"><?php print $pedidossx->idpedido; ?></td>
            <td class="d-none d-sm-table-cell" title="<?= $data . ' ' . $hora ?>"><?php print $texto_truncado_data; ?></td>
            <td class="d-none d-sm-table-cell"><?php print $delivery; ?></td>
            <td class="d-none d-sm-table-cell" title="<?= $nome ?>"><?php print htmlspecialchars($texto_truncado); ?></td>
            <td class="<?= $pedidossx->mesa == 0 ? 'bg-success' : 'bg-danger'; ?> text-light text-center d-table-cell" style="font-size: 1.0rem;margin:0 auto;"><?php echo $pedidossx->mesa == 0 ? "0" : $pedidossx->mesa; ?></td>
            <td class="d-none d-sm-table-cell"><a href="https://api.whatsapp.com/send?phone=55<?= $pedidossx->celular; ?>&text=Olá" target="_blank"><img src="../img/wp.png" style="width:15px" /> <?php print $pedidossx->celular; ?></a></td>
            <td class="d-none d-sm-table-cell">R$ <?php print formatMoedaBr(formatCurrency($pedidossx->vtotal)); ?></td>
            <td class="d-table-cell"><?php print $status; ?></td>
            <?php if ($status_verificar == "Finalizado") { ?>
                <td class="d-none d-sm-table-cell">
                    <form action="comprovante.php" method="post">
                        <input type="hidden" name="codigop" value="<?php print $pedidossx->idpedido; ?>" />
                        <button style="cursor: pointer;border:none;background:none;" onclick="PrintMe('modal-paid')" class=" text-center"><i class="fa fa-money text-success" style="font-size: 1.5rem;" aria-hidden="true"></i></button>
                    </form>
                </td>
            <?php } else { ?>
                <td class="text-light text-center d-none d-sm-table-cell"><i class="fa fa-money text-danger" style="font-size: 1.5rem;" aria-hidden="true"></i></td>
            <?php } ?>
            <td align="center">
                <div class="card p-3 mb-3 d-block d-md-none">
                    <h5 class="card-title">Pedido</h5>
                    <p><strong>ID do Pedido:</strong> <?php print $pedidossx->idpedido; ?></p>
                    <p><strong>Data:</strong> <?php print $data . ' às ' . $hora; ?></p>
                    <p><strong>Tipo:</strong> <?php print $delivery; ?></p>
                    <p><strong>Cliente:</strong> <?php print htmlspecialchars($nome); ?></p>
                    <p><strong>Mesa:</strong>
                        <?php if ($pedidossx->mesa == 0) { ?>
                            <span style="font-size: 1.0rem;">0</span>
                        <?php } else { ?>
                            <span style="font-size: 1.0rem;"><?php echo $pedidossx->mesa; ?></span>
                        <?php } ?>
                    </p>
                    <p><strong>WhatsApp:</strong>
                        <a href="https://api.whatsapp.com/send?phone=55<?= $pedidossx->celular; ?>&text=Olá" target="_blank">
                            <img src="../img/wp.png" style="width:15px" /> <?php print $pedidossx->celular; ?>
                        </a>
                    </p>
                    <p><strong>Total:</strong> R$ <?php print formatMoedaBr(formatCurrency($pedidossx->vtotal)); ?></p>
                    <p style="width: 100%; overflow:hidden;"><strong>Pedido:</strong> <?php print $status; ?></p>
                </div>

                <form action="verpedido.php" method="post">
                    <input type="hidden" name="codigop" value="<?php print $pedidossx->idpedido; ?>" />
                    <button style="cursor: pointer;" type="submit" class="btn btn-purple btn-sm w-100"><i class="fa fa-eye" aria-hidden="true"></i></button>
                </form>
                <div class="w-100 p-1"></div>
            </td>

            <?php
            if ($status_verificar === "Novo Pedido") {
                echo "";
            ?>

            <?php
            } else if ($status_verificar === "Cancelado") {
                echo "";
            ?>

            <?php
            } else {

            ?>
                <td>
                    <?php if ($status_verificar != "Finalizado") { ?>
                        <a href="pdvpedidoeditar.php?idpedido=<?php print $pedidossx->idpedido; ?>"><button class="btn btn-warning btn-sm w-100"><i class="fa fa-pencil" aria-hidden="true"></i></button></a>
                    <?php } ?>
                </td>

            <?php
            }

            ?>
        </tr>
<?php
    }
}
?>