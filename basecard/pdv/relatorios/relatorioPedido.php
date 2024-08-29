<?php
function relatorioPedido($idpedido, $connect)
{
    include_once('../../funcoes/Conexao.php');
    include_once('../../funcoes/Key.php');

    // Obtém os detalhes do pedido
    $query_pedido = "SELECT * FROM pedidos WHERE idpedido = :idpedido";
    $stmt = $connect->prepare($query_pedido);
    $stmt->bindParam(':idpedido', $idpedido, PDO::PARAM_INT);
    $stmt->execute();
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        return "Pedido não encontrado.";
    }

    // Obtém o motivo do cancelamento
    $query_motivo = "SELECT * FROM motivo_cancelamento WHERE id_pedido = :id_pedido";
    $stmt_motivo = $connect->prepare($query_motivo);
    $stmt_motivo->bindParam(':id_pedido', $idpedido, PDO::PARAM_INT);
    $stmt_motivo->execute();
    $motivo = $stmt_motivo->fetch(PDO::FETCH_ASSOC);

    // Insere o motivo do cancelamento, se não existir
    if ($pedido['status'] == 6 && !$motivo && isset($_POST['motivo_cancelamento']) && !empty($_POST['motivo_cancelamento'])) {
        $motivo_cancelamento = htmlspecialchars($_POST['motivo_cancelamento']);
        $query_insert = "INSERT INTO motivo_cancelamento (idu, nome, id_pedido, status, motivo_cancelamento) VALUES (:idu, :nome, :id_pedido, :status, :motivo_cancelamento)";
        $stmt_insert = $connect->prepare($query_insert);
        $stmt_insert->bindParam(':idu', $pedido['idu'], PDO::PARAM_STR);
        $stmt_insert->bindParam(':nome', $pedido['nome'], PDO::PARAM_STR);
        $stmt_insert->bindParam(':id_pedido', $idpedido, PDO::PARAM_INT);
        $stmt_insert->bindParam(':status', $pedido['status'], PDO::PARAM_INT);
        $stmt_insert->bindParam(':motivo_cancelamento', $motivo_cancelamento, PDO::PARAM_STR);
        $stmt_insert->execute();

        
    }

    ob_start(); // Inicia o buffer de saída
?>
    <style>
        .container-relatorio {
            border: 1px dashed #000;
            display: flex;
            padding: 1rem;
            width: 100%;
            flex-direction: column;
        }

        .content-relatorio {
            display: flex;
            padding: 1rem;
            width: 100%;
            flex-direction: column;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
        }

        .row-relatorio {
            display: flex;
            padding: 1rem;
            width: 100%;
            flex-direction: column;
        }

        .content-relatorio .row-relatorio {
            margin-bottom: 10px;
        }

        .content-relatorio .label {
            font-weight: bold;
            font-size: 1.4rem;
        }

        .cancel-form {
            margin-top: 20px;
            text-align: center;
        }

        .cancel-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #000;
            margin-bottom: 10px;
        }

        .cancel-form button {
            padding: 10px 20px;
            background-color: #ff4d4d;
            color: #fff;
            border: 1px solid #000;
            cursor: pointer;
        }

        .cancel-form button:hover {
            background-color: #e60000;
        }

        .label {
            font-weight: bold;
            font-size: 1.4rem;

        }

        .motivo {
            background-color: #ff4d4d;
            padding: 1rem;
            color: white;
            font-size: 1rem;
            border-radius: 0.5rem;
        }
    </style>

    <div class="container-relatorio">
        <div class="header">
            <h1>Relatório do Pedido #<?php echo $idpedido; ?></h1>
        </div>


        <?php if ($pedido['status'] == 6) : ?>
            <div class="row-relatorio ">
                <span class="label">Motivo do Cancelamento:</span>
                <?php if ($motivo) : ?>
                    <p class="motivo"> <?php echo nl2br(htmlspecialchars($motivo['motivo_cancelamento'])); ?></p>
                <?php else : ?>
                    <form method="post" class="cancel-form">
                        <input type="hidden" name="codigop" value="<?php print $pedido['idpedido']; ?>" />
                        <textarea name="motivo_cancelamento" rows="4" placeholder="Informe o motivo do cancelamento"></textarea>
                        <button type="submit">Salvar Motivo</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

<?php
    return ob_get_clean(); // Retorna o conteúdo do buffer de saída
}
?>