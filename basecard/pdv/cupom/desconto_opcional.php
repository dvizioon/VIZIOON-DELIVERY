<?php
header('Content-Type: application/json');

// Desabilita a exibição de erros no navegador e habilita o log de erros
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();

// Verifica se o cookie 'pdvx' está definido
if (!isset($_COOKIE['pdvx'])) {
    echo json_encode(['success' => false, 'message' => 'Cookie não encontrado.']);
    exit();
}

include_once('../../../funcoes/Conexao.php');
include_once('../../../funcoes/Key.php');

try {
    // Verifica se a ação foi enviada
    if (!isset($_POST['acaoDesconto'])) {
        throw new Exception('Ação não especificada.');
    }

    // Define a variável mais descritiva
    $acaoDesconto = $_POST['acaoDesconto'];

    if ($acaoDesconto === "update") {
        // Verifica se os dados foram enviados via POST
        if (!isset($_POST['valorDescontoOpcional']) || !isset($_POST['idpedido'])) {
            throw new Exception('Campos obrigatórios não preenchidos.');
        }

        // Extrai os dados do POST
        $valorDescontoOpcional = $_POST['valorDescontoOpcional'];
        $idPedido = $_POST['idpedido'];

        // Prepara a query de atualização
        $sql_update_desconto_opcional = "UPDATE pedidos SET desconto_opcional = :desconto_opcional WHERE idpedido = :idpedido";
        $stmt = $connect->prepare($sql_update_desconto_opcional);
        $stmt->bindParam(':desconto_opcional', $valorDescontoOpcional);
        $stmt->bindParam(':idpedido', $idPedido);

        // Executa a query e verifica o sucesso
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Desconto aplicado com sucesso!']);
        } else {
            throw new Exception('Erro ao aplicar o desconto.');
        }
    } elseif ($acaoDesconto === "delete") {
        // Verifica se o idPedido foi enviado
        if (!isset($_POST['idpedido'])) {
            throw new Exception('ID do pedido não especificado.');
        }

        $idPedido = $_POST['idpedido'];

        // Prepara a query para definir o desconto como 0 (deletar)
        $sql_delete_desconto_opcional = "UPDATE pedidos SET desconto_opcional = 0 WHERE idpedido = :idpedido";
        $stmt = $connect->prepare($sql_delete_desconto_opcional);
        $stmt->bindParam(':idpedido', $idPedido);

        // Executa a query e verifica o sucesso
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Desconto removido com sucesso!']);
        } else {
            throw new Exception('Erro ao remover o desconto.');
        }
    } else {
        throw new Exception('Ação inválida.');
    }
} catch (Exception $e) {
    // Captura e retorna erros como JSON
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
