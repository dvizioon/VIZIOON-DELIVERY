<?php
if (isset($_COOKIE['pdvx'])) {
    $cod_id = $_COOKIE['pdvx'];
} else {
    header("location: sair.php");
    exit();
}

include_once('../../../funcoes/Conexao.php');
include_once('../../../funcoes/Key.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $celular = $_POST['celular'] ?? null;
    $valor_com_desconto = $_POST['valor_com_desconto'] ?? null;
    $limite_compras = $_POST['quantidade_compras_limite'] ?? 0;
    $id_pedido = $_POST['idpedido'] ?? null;

    echo $valor_com_desconto;

    // Valida se todos os parâmetros necessários estão presentes
    if ($celular && $valor_com_desconto && $limite_compras > 0 && $id_pedido) {
        try {
            // Verifica o número de compras finalizadas (status = 5) baseado no celular
            $sql_check_compras = "SELECT id FROM cumpomClientes WHERE celular = :celular AND status = '5'";
            $stmt_check_compras = $connect->prepare($sql_check_compras);
            $stmt_check_compras->bindParam(':celular', $celular);
            $stmt_check_compras->execute();
            $compras = $stmt_check_compras->fetchAll(PDO::FETCH_ASSOC);
            $total_compras_finalizadas = count($compras);

            if ($total_compras_finalizadas > 0) {
                // Subtrai o limite do total de compras finalizadas
                $novo_saldo_compras = max(0, $total_compras_finalizadas - $limite_compras);

                // Pega os IDs das compras que serão deletadas
                $ids_to_delete = array_slice(array_column($compras, 'id'), 0, $limite_compras);
                if (!empty($ids_to_delete)) {
                    $ids_placeholders = implode(',', array_fill(0, count($ids_to_delete), '?'));

                    // Deleta as compras que foram utilizadas
                    $sql_delete_compras = "DELETE FROM cumpomClientes WHERE id IN ($ids_placeholders)";
                    $stmt_delete_compras = $connect->prepare($sql_delete_compras);

                    // Passa os IDs dinamicamente
                    foreach ($ids_to_delete as $index => $id) {
                        $stmt_delete_compras->bindValue($index + 1, $id, PDO::PARAM_INT);
                    }

                    if ($stmt_delete_compras->execute()) {
                        // Atualiza o pedido com o valor do desconto
                        $sql_update_pedido = "UPDATE pedidos SET descontos = :desconto WHERE idpedido = :idpedido";
                        $stmt_update = $connect->prepare($sql_update_pedido);
                        $stmt_update->bindParam(':desconto', $valor_com_desconto);
                        $stmt_update->bindParam(':idpedido', $id_pedido);

                        if ($stmt_update->execute()) {
                            echo "Desconto confirmado. O saldo de compras agora é: $novo_saldo_compras.";
                        } else {
                            echo "Erro ao atualizar o desconto no pedido.";
                        }
                    } else {
                        echo "Erro ao deletar as compras.";
                    }
                } else {
                    echo "Erro ao identificar as compras para deletar.";
                }
            } else {
                echo "Não há compras finalizadas para remover.";
            }
        } catch (Exception $e) {
            echo "Erro: " . $e->getMessage();
        }
    } else {
        echo "Parâmetros inválidos.";
    }

    // Redirecionar para a página `verpedido.php` com o parâmetro `codigop`
    echo '<form id="autoRedirectForm" action="../verpedido.php" method="post">';
    echo '<input type="hidden" name="codigop" value="' . htmlspecialchars($id_pedido) . '" />';
    echo '</form>';
    echo '<script type="text/javascript">
            document.getElementById("autoRedirectForm").submit();
        </script>';
    exit;
}
