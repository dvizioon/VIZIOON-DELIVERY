<?php
require_once "topo.php";

// Verifica se a variável $cod_id está definida
if (isset($cod_id)) {
    // Data do dia anterior no formato d-m-Y
    $data_anterior = date('d-m-Y', strtotime('-1 day'));

    // Consulta para obter todas as informações dos pedidos com mesas ocupadas no dia anterior
    $sql = "SELECT `idpedido`, `mesa`, `status`, `nome`, `vtotal`, `data`, `hora`, `obs`, `atendente_criador`,`celular`
            FROM `pedidos`
            WHERE `idu` = :idu AND `data` = :data AND `mesa` != '0'"; // Mesa diferente de '0' indica que está ocupada

    $stmt = $connect->prepare($sql);
    $stmt->bindParam(':idu', $cod_id);
    $stmt->bindParam(':data', $data_anterior);
    $stmt->execute();
    $mesas_ocupadas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Usuário não definido.";
}

// Verifica se a requisição é um POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['idpedido'])) {
        $idpedido = $_POST['idpedido'];

        // Verifica se o status da mesa é diferente de '0' e se a data é do dia anterior
        $sql_check = "SELECT `status`, `data` FROM `pedidos` WHERE `idpedido` = :idpedido";
        $stmt_check = $connect->prepare($sql_check);
        $stmt_check->bindParam(':idpedido', $idpedido);
        $stmt_check->execute();
        $pedido = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($pedido['status'] != '0' && $pedido['data'] == $data_anterior) {
            // Atualiza o status da mesa para '6' (Cancelada)
            $update_status = $connect->prepare("UPDATE pedidos SET status = '6' WHERE idpedido = :idpedido");
            $update_status->bindParam(':idpedido', $idpedido);
            $update_status->execute();

            // Atualiza a mesa do pedido para '0' (liberada)
            $update_mesa = $connect->prepare("UPDATE pedidos SET mesa = '0' WHERE idpedido = :idpedido");
            $update_mesa->bindParam(':idpedido', $idpedido);
            $update_mesa->execute();

            // Insere o motivo do cancelamento com um dos motivos padrões
            $motivo_cancelamento = isset($_POST['motivo_cancelamento']) ? $_POST['motivo_cancelamento'] : 'Mesa foi cancelada'; // Motivo padrão
            $idu = $cod_id; // Assumindo que $cod_id é o identificador do usuário
            $nome =  isset($_POST['nome_cancelamento']) ? $_POST['nome_cancelamento'] : $_POST['idpedido'];; // Assumindo que $nome_usuario contém o nome do usuário

            $update_registro = $connect->prepare("
                INSERT INTO motivo_cancelamento (id_pedido, motivo_cancelamento, status, idu, nome) 
                VALUES (:id_pedido, :motivo_cancelamento, '6', :idu, :nome)
            ");
            $update_registro->bindParam(':id_pedido', $idpedido);
            $update_registro->bindParam(':motivo_cancelamento', $motivo_cancelamento);
            $update_registro->bindParam(':idu', $idu);
            $update_registro->bindParam(':nome', $nome);
            $update_registro->execute();

            header("Location: liberarmesas.php?ok=1");
            exit();
        }
    }
}

?>



<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesas Ocupadas</title>
    <style>
        /* Modal Container */
        .modal {
            display: none;
            /* Oculto por padrão */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            align-items: center;
            justify-content: center;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            /* Fundo semitransparente */
        }

        /* Conteúdo do Modal */
        .modal-content {
            background-color: #fefefe;

            /* Centraliza verticalmente */
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            /* Largura do modal */
            max-width: 500px;
            border-radius: 10px;
        }

        /* Botão de Fechar */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="slim-mainpanel">
        <div class="container">
            <?php if (isset($_GET["erro"])) { ?>
                <div class="alert alert-warning" role="alert">
                    <i class="fa fa-asterisk" aria-hidden="true"></i> Erro.
                </div>
            <?php } ?>
            <?php if (isset($_GET["ok"])) { ?>
                <div class="alert alert-success" role="alert">
                    <i class="fa fa-thumbs-o-up" aria-hidden="true"></i> Sucesso.
                </div>
            <?php } ?>
            <div class="section-wrapper">
                <h2 class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Mesas Ocupadas do Dia Anterior</h2>
                <hr>
                <?php if (empty($mesas_ocupadas)) { ?>
                    <p>Nenhuma mesa ocupada no dia anterior.</p>
                <?php } else { ?>
                    <div class="row">
                        <?php foreach ($mesas_ocupadas as $mesa) { ?>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title_liberar_mesa">Mesa: <?php echo htmlspecialchars($mesa['mesa']); ?></h5>
                                        <p><strong>Nome:</strong> <?php echo htmlspecialchars($mesa['nome']); ?></p>
                                        <p><strong>Total:</strong> <?php echo number_format($mesa['vtotal'], 2, ","); ?></p>
                                        <p><strong>Data:</strong> <?php echo htmlspecialchars($mesa['data']); ?></p>
                                        <p><strong>Hora:</strong> <?php echo htmlspecialchars($mesa['hora']); ?></p>
                                        <!-- <p><strong>Observações:</strong> <?php echo htmlspecialchars($mesa['obs']); ?></p> -->
                                        <p><strong>Abriu Pedido:</strong> <?php echo htmlspecialchars($mesa['atendente_criador']); ?></p>
                                        <button class="btn-info p-2 w-100" onclick="abrirModal('<?php echo htmlspecialchars(json_encode($mesa)); ?>')">Dados Completos</button>
                                        <form action="liberarmesas.php" method="post" style="display:inline;">
                                            <input type="hidden" name="idpedido" value="<?php echo htmlspecialchars($mesa['idpedido']); ?>" />
                                            <input type="hidden" name="nome_cancelamento" value="<?php echo htmlspecialchars($mesa['nome']); ?>" />
                                            <button type="submit" class="btn-success p-2 w-100 mt-2 mb-2">Liberar Mesa</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="modalDados" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModal()">&times;</span>
            <h2>Dados Completos da Mesa</h2>
            <div id="dadosMesa"></div>
        </div>
    </div>

    <script>
        function abrirModal(dadosMesaJson) {
            var dadosMesa = JSON.parse(dadosMesaJson);
            var modal = document.getElementById("modalDados");
            var dadosMesaDiv = document.getElementById("dadosMesa");
            // console.log(dadosMesa)
            dadosMesaDiv.innerHTML = `
            <p><strong>Mesa:</strong> ${dadosMesa.mesa}</p>
            <p><strong>Nome:</strong> ${dadosMesa.nome}</p>
            <p><strong>Total:</strong> ${Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(dadosMesa.vtotal)}</p>
            <p><strong>Data:</strong> ${dadosMesa.data}</p>
            <p><strong>Telefone:</strong> ${dadosMesa.celular}</p>
            <p><strong>Hora:</strong> ${dadosMesa.hora}</p>
            <p><strong>Observações:</strong> ${dadosMesa.obs}</p>
            <p><strong>Atendente:</strong> ${dadosMesa.atendente_criador}</p>
        `;

            modal.style.display = "flex";
        }

        function fecharModal() {
            var modal = document.getElementById("modalDados");
            modal.style.display = "none";
        }
    </script>
</body>

</html>