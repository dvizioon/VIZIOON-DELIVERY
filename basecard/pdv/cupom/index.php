<?php



function verificarCupom($connect)
{
    // Consulta para buscar o primeiro cupom que está marcado como padrão e ativo
    $sql = "SELECT * FROM cumpomConfig WHERE padrao = 'sim' AND ativo = 'sim' LIMIT 1";
    $stmt = $connect->prepare($sql);
    $stmt->execute();
    $cupom = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se um cupom padrão e ativo foi encontrado
    if ($cupom) {
        // Monta o array com os dados do cupom padrão e ativo
        $response = [
            'status' => 'sim',
            'valor_porcentagem' => $cupom['valor_porcentagem'],
            'quantidade_compras' => $cupom['quantidade_compras'],
            'dias_da_semana' => json_decode($cupom['dias_da_semana'], true)
        ];
    } else {
        // Se não há cupom padrão e ativo, retorna um array com status "nao"
        $response = ['status' => 'nao'];
    }

    // Retorna o array de resposta
    return $response;
}


