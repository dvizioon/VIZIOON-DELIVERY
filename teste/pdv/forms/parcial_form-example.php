<?php
session_start();

if (isset($_COOKIE['pdvx'])) {
    $codigo_id = $_COOKIE['pdvx'];
}

include_once('../../../funcoes/Conexao.php');
include_once('../../../funcoes/Key.php');

function sanitize_input($input, $conexao)
{
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = sanitize_input($value, $conexao);
        }
    } else {
        $input = trim($input);
        $input = mysqli_real_escape_string($conexao, $input);
        $input = htmlspecialchars($input);
    }
    return $input;
}

$conexao = new mysqli($servidor, $usuario, $senha, $banco);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Receber os dados do formulário e sanitizá-los
    $metodoPagamento = sanitize_input($_POST['metodoPagamento'], $conexao);
    $quantidade = sanitize_input($_POST['quantidade'], $conexao);
    $idu_empresa = sanitize_input($_POST['idu_empresa'], $conexao);
    $nome_cliente = sanitize_input($_POST['nome_cliente'], $conexao);
    $id_pedido = sanitize_input($_POST['id_pedido'], $conexao);
    $status_pedido = sanitize_input($_POST['status_pedido'], $conexao);
    $tipo_pedido = sanitize_input($_POST['tipo_pedido'], $conexao);
    $mesa_pedido = sanitize_input($_POST['mesa_pedido'], $conexao);
    $data_registro = sanitize_input($_POST['data_registro'], $conexao);
    $subtotal_geral = sanitize_input($_POST['subtotal_geral'], $conexao);
    $total_geral = sanitize_input($_POST['total_geral'], $conexao);

    // Processar os dados de pagamento
    $dados = [];
    foreach ($metodoPagamento as $index => $metodo) {
        $quant = isset($quantidade[$index]) ? $quantidade[$index] : 0;
        $dados[] = [
            'metodo' => $metodo,
            'quantidade' => $quant
        ];
    }

    // Calcular o valor total dos novos pagamentos
    $novo_valor_total = 0;
    foreach ($dados as $pagamento) {
        $novo_valor_total += floatval(str_replace(',', '.', $pagamento['quantidade']));
    }

    // Verificar se o pedido já existe e obter o valor total dos pagamentos anteriores
    $query = "SELECT `dados_pagamentos` FROM `registrospagamentos` WHERE `idpedido` = ?";
    $stmt = $conexao->prepare($query);
    $stmt->bind_param("i", $id_pedido);
    $stmt->execute();
    $stmt->bind_result($dados_pagamentos_existentes);
    $stmt->fetch();
    $stmt->close();

    $valor_total_existente = 0;
    $pagamentos_existentes = [];
    if ($dados_pagamentos_existentes) {
        $pagamentos_existentes = json_decode($dados_pagamentos_existentes, true);
        if (!isset($pagamentos_existentes['dados']) || !is_array($pagamentos_existentes['dados'])) {
            $pagamentos_existentes['dados'] = [];
        }
        foreach ($pagamentos_existentes['dados'] as $pagamento) {
            $valor_total_existente += floatval(str_replace(',', '.', $pagamento['quantidade']));
        }
    } else {
        $pagamentos_existentes['dados'] = [];
    }

    // Acumular o novo valor de pagamento com o valor total dos pagamentos anteriores
    $valor_total = $valor_total_existente + $novo_valor_total;

    // Converta os valores para float antes de formatar
    $total_geral_float = floatval(str_replace(',', '.', $total_geral));

    // Formatar os valores como strings
    $valor_total_formatado = number_format($valor_total, 2, ',', '.');
    $total_geral_formatado = number_format($total_geral_float, 2, ',', '.');

    if ($valor_total >= $total_geral_float) {
        // Se o pagamento é suficiente
        if ($valor_total > $total_geral_float) {
            $troco = $valor_total - $total_geral_float;
            $troco_formatado = number_format($troco, 2, ',', '.');
            $mensagem_sucesso = '
            <div class="alert alert-success" role="alert">
                <strong>Sucesso:</strong> O pagamento foi realizado com sucesso.<br>
                <strong>Valor Pago:</strong> ' . htmlspecialchars($valor_total_formatado) . '<br>
                <strong>Valor da Conta:</strong> ' . htmlspecialchars($total_geral_formatado) . '<br>
                <strong>Troco:</strong> ' . htmlspecialchars($troco_formatado) . '
            </div>';
        } else {
            $mensagem_sucesso = '
            <div class="alert alert-success" role="alert">
                <strong>Sucesso:</strong> O pagamento foi realizado com sucesso.<br>
                <strong>Valor Pago:</strong> ' . htmlspecialchars($valor_total_formatado) . '<br>
                <strong>Valor da Conta:</strong> ' . htmlspecialchars($total_geral_formatado) . '
            </div>';
        }

        // Atualizar ou inserir o registro de pagamento
        $pagamentos_existentes['dados'] = array_merge($pagamentos_existentes['dados'], $dados);
        $dados_pagamentos_atualizados = json_encode($pagamentos_existentes, JSON_UNESCAPED_UNICODE);
        if ($codigo_id === $idu_empresa) {
            $query = "SELECT COUNT(*) as count FROM `registrospagamentos` WHERE `idpedido` = ?";
            if ($stmt = $conexao->prepare($query)) {
                $stmt->bind_param("i", $id_pedido);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();

                if ($count > 0) {
                    // Atualizar registro existente
                    $sql = "UPDATE `registrospagamentos` SET `dados_pagamentos`=?, `valor_dinheiro`=?, `valor_troco`=? WHERE `idpedido`=?";
                    if ($stmt = $conexao->prepare($sql)) {
                        $stmt->bind_param("sdii", $dados_pagamentos_atualizados, $valor_total, $troco, $id_pedido);
                        if ($stmt->execute()) {
                            echo '
                            <div class="container mt-5">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h4 class="card-title">Sucesso</h4>
                                    </div>
                                    <div class="card-body">
                                        ' . $mensagem_sucesso . '
                                    </div>
                                    <div class="card-footer">
                                        <form action="verpedido.php" method="post" class="d-flex justify-content-center">
                                            <input type="hidden" name="codigop" value="' . htmlspecialchars($id_pedido) . '" />
                                            <button style="cursor: pointer;" type="submit" class="btn btn-success btn-lg w-25 p-3">Voltar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>';
                        } else {
                            echo '
                            <div class="container mt-5">
                                <div class="card">
                                    <div class="card-header bg-danger text-white">
                                        <h4 class="card-title">Erro</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-danger" role="alert">
                                            <strong>Erro ao atualizar pagamento:</strong> ' . htmlspecialchars($stmt->error) . '
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <form action="verpedido.php" method="post" class="d-flex justify-content-center">
                                            <input type="hidden" name="codigop" value="' . htmlspecialchars($id_pedido) . '" />
                                            <button style="cursor: pointer;" type="submit" class="btn btn-danger btn-lg w-25 p-3">Voltar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>';
                        }
                        $stmt->close();
                    }
                } else {
                    // Inserir novo registro
                    $sql = "INSERT INTO `registrospagamentos`(`idu`, `nome`, `idpedido`, `status`, `tipo`, `dados_pagamentos`, `mesa_registrada`, `data_registro`, `vsubtotal`, `vtotal`, `valor_dinheiro`, `valor_troco`, `formapaga`) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    if ($stmt = $conexao->prepare($sql)) {
                        $pacial = 'parcial';
                        $valor_dinheiro = $valor_total;
                        $valor_troco = 0;

                        $stmt->bind_param(
                            "isssssssiiiis",
                            $idu_empresa,
                            $nome_cliente,
                            $id_pedido,
                            $status_pedido,
                            $tipo_pedido,
                            $dados_pagamentos_atualizados,
                            $mesa_pedido,
                            $data_registro,
                            $subtotal_geral,
                            $total_geral,
                            $valor_dinheiro,
                            $valor_troco,
                            $pacial
                        );
                        if ($stmt->execute()) {
                            echo '
                            <div class="container mt-5">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h4 class="card-title">Sucesso</h4>
                                    </div>
                                    <div class="card-body">
                                        ' . $mensagem_sucesso . '
                                    </div>
                                    <div class="card-footer">
                                        <form action="verpedido.php" method="post" class="d-flex justify-content-center">
                                            <input type="hidden" name="codigop" value="' . htmlspecialchars($id_pedido) . '" />
                                            <button style="cursor: pointer;" type="submit" class="btn btn-success btn-lg w-25 p-3">Voltar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>';
                        } else {
                            echo '
                            <div class="container mt-5">
                                <div class="card">
                                    <div class="card-header bg-danger text-white">
                                        <h4 class="card-title">Erro</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-danger" role="alert">
                                            <strong>Erro ao inserir pagamento:</strong> ' . htmlspecialchars($stmt->error) . '
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <form action="verpedido.php" method="post" class="d-flex justify-content-center">
                                            <input type="hidden" name="codigop" value="' . htmlspecialchars($id_pedido) . '" />
                                            <button style="cursor: pointer;" type="submit" class="btn btn-danger btn-lg w-25 p-3">Voltar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>';
                        }
                        $stmt->close();
                    }
                }
            }
        }
    } else {
        // Adicionar os novos pagamentos aos pagamentos existentes
        $pagamentos_existentes['dados'] = array_merge($pagamentos_existentes['dados'], $dados);

        // Atualizar o registro com os novos dados de pagamento
        $dados_pagamentos_atualizados = json_encode($pagamentos_existentes, JSON_UNESCAPED_UNICODE);
        $valor_faltando = $total_geral_float - $valor_total;
        $valor_faltando_formatado = number_format($valor_faltando, 2, ',', '.');

        echo '
        <div class="container mt-5">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="card-title">Valor Insuficiente</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        <strong>Valor Insuficiente:</strong> O valor pago até o momento é insuficiente para cobrir o valor da conta.<br>
                        <strong>Valor Pago:</strong> ' . htmlspecialchars(number_format($valor_total, 2, ',', '.')) . '<br>
                        <strong>Valor Total da Conta:</strong> ' . htmlspecialchars($total_geral_formatado) . '<br>
                        <strong>Valor Faltando:</strong> ' . htmlspecialchars($valor_faltando_formatado) . '
                    </div>
                </div>
                <div class="card-footer">
                    <form action="verpedido.php" method="post" class="d-flex justify-content-center">
                        <input type="hidden" name="codigop" value="' . htmlspecialchars($id_pedido) . '" />
                        <button style="cursor: pointer;" type="submit" class="btn btn-warning btn-lg w-25 p-3">Voltar</button>
                    </form>
                </div>
            </div>
        </div>';

        // Atualizar ou inserir o registro de pagamento
        $query = "SELECT COUNT(*) as count FROM `registrospagamentos` WHERE `idpedido` = ?";
        if ($stmt = $conexao->prepare($query)) {
            $stmt->bind_param("i", $id_pedido);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                $sql = "UPDATE `registrospagamentos` SET `dados_pagamentos`=? WHERE `idpedido`=?";
                if ($stmt = $conexao->prepare($sql)) {
                    $stmt->bind_param("si", $dados_pagamentos_atualizados, $id_pedido);
                    $stmt->execute();
                    $stmt->close();
                }
            } else {
                $sql = "INSERT INTO `registrospagamentos`(`idu`, `nome`, `idpedido`, `status`, `tipo`, `dados_pagamentos`, `mesa_registrada`, `data_registro`, `vsubtotal`, `vtotal`, `valor_dinheiro`, `valor_troco`, `formapaga`) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                if ($stmt = $conexao->prepare($sql)) {
                    $date = date("Y-m-d H:i:s");
                    $pacial = 'parcial';
                    $valor_dinheiro = 0;
                    $valor_troco = 0;

                    $stmt->bind_param(
                        "isssssssiiiis",
                        $idu_empresa,
                        $nome_cliente,
                        $id_pedido,
                        $status_pedido,
                        $tipo_pedido,
                        $dados_pagamentos_atualizados,
                        $mesa_pedido,
                        $data_registro,
                        $subtotal_geral,
                        $total_geral,
                        $valor_dinheiro,
                        $valor_troco,
                        $pacial
                    );
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }

    $conexao->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>