<?php
session_start();
if (isset($_COOKIE['pdvx'])) {
    $idu = $_COOKIE['pdvx'];
} else {
    header("location: sair.php");
    exit();
}
include_once('../../../funcoes/Conexao.php');
include_once('../../../funcoes/Key.php');

if (isset($_POST['action']) && $_POST['action'] == 'filtrarPedidos') {
    try {
        $dataInicio = isset($_POST['dateStart']) ? $_POST['dateStart'] : null;
        $dataFim = isset($_POST['dateEnd']) ? $_POST['dateEnd'] : null;

        $filtroData = "";
        if ($dataInicio && $dataFim) {
            // Intervalo de datas entre o início e o fim
            $filtroData = " AND data >= :dataInicio AND data <= :dataFim";
        } elseif ($dataInicio && !$dataFim) {
            // Se apenas a data inicial for fornecida, faz a contagem apenas desse dia
            $filtroData = " AND data = :dataInicio";
        }

        // Consulta para pedidos finalizados
        $query_finalizados = $connect->prepare("
            SELECT COUNT(*) as total FROM pedidos WHERE status = 5" . $filtroData);
        if ($dataInicio) {
            $query_finalizados->bindParam(':dataInicio', $dataInicio);
        }
        if ($dataFim) {
            $query_finalizados->bindParam(':dataFim', $dataFim);
        }
        $query_finalizados->execute();
        $total_pedidos_finalizados = $query_finalizados->fetch(PDO::FETCH_OBJ)->total;

        // Consulta para pedidos cancelados
        $query_cancelados = $connect->prepare("
            SELECT COUNT(*) as total FROM pedidos WHERE status = 6" . $filtroData);
        if ($dataInicio) {
            $query_cancelados->bindParam(':dataInicio', $dataInicio);
        }
        if ($dataFim) {
            $query_cancelados->bindParam(':dataFim', $dataFim);
        }
        $query_cancelados->execute();
        $total_pedidos_cancelados = $query_cancelados->fetch(PDO::FETCH_OBJ)->total;

        // Retorna a resposta JSON com os valores
        echo json_encode([
            'total_finalizados' => $total_pedidos_finalizados,
            'total_cancelados' => $total_pedidos_cancelados
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['error' => 'Invalid action']);
}
