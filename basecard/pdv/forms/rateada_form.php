<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucesso!</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-header {
            background-color: #28a745;
            color: white;
        }

        .card-footer {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>

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
        $valor_dinheiro = isset($_POST['valor_dinheiro']) ? sanitize_input($_POST['valor_dinheiro'], $conexao) : '0';
        $valor_troco = isset($_POST['valor_troco']) ? sanitize_input($_POST['valor_troco'], $conexao) : '0';

        // Processar os dados de pagamento
        $dados = [];
        foreach ($metodoPagamento as $index => $metodo) {
            $quant = isset($quantidade[$index]) ? $quantidade[$index] : 0;
            $dados[] = [
                'metodo' => $metodo,
                'quantidade' => $quant
            ];
        }

        // Criar o array com o rótulo e os dados
        $resultado = [
            'tipo' => 'rateio',
            'dados' => $dados
        ];

        $valor_total = 0;
        foreach ($dados as $pagamento) {
            $valor_total += floatval($pagamento['quantidade']);
        }

        // Converta os valores para float antes de formatar
        $valor_total_float = floatval($valor_total);
        $total_geral_float = floatval($total_geral);

        // Formatar os valores como strings
        $valor_total_formatado = number_format(
            $valor_total_float,
            2,
            ',',
            '.'
        );
        $total_geral_formatado = number_format(
            $total_geral_float,
            2,
            ',',
            '.'
        );

        if ($valor_total_float !== $total_geral_float) {

            echo '
            <div class="container mt-5">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h4 class="card-title">Erro</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger" role="alert">
                            <strong>Erro:</strong> Os valores não coincidem.<br>
                            <strong>Valor Pago:</strong> ' . htmlspecialchars($valor_total_formatado) . '<br>
                            <strong>Valor da Conta:</strong> ' . htmlspecialchars($total_geral_formatado) . '
                        </div>
                    </div>
                    <div class="card-footer">
                        <form action="../verpedido.php" method="post" class="d-flex justify-content-center">
								<input type="hidden" name="codigop" value="' . htmlspecialchars($id_pedido) . '" />
								<button style="cursor: pointer;" type="submit" class="btn btn-danger btn-lg w-25 p-3 ">Voltar</button>
								</form>
                    </div>
                </div>
            </div>';
        } else {
            if ($codigo_id === $idu_empresa) {
                // Verificar se o pedido já existe
                $query = "SELECT COUNT(*) as count FROM `registrospagamentos` WHERE `idpedido` = ?";
                if ($stmt = $conexao->prepare($query)) {
                    $stmt->bind_param("i", $id_pedido);
                    $stmt->execute();
                    $stmt->bind_result($count);
                    $stmt->fetch();
                    $stmt->close();

                    if ($count > 0) {

                        $sql = "UPDATE `registrospagamentos` 
            SET `idu`=?, `nome`=?, `status`=?, `tipo`=?, `dados_pagamentos`=?, `mesa_registrada`=?, `data_registro`=?, `vsubtotal`=?, `vtotal`=?, `valor_dinheiro`=?, `valor_troco`=? ,`formapaga`=?
            WHERE `idpedido`=?";
                    } else {

                        $sql = "INSERT INTO `registrospagamentos` 
            (`idu`, `nome`, `idpedido`, `status`, `tipo`, `dados_pagamentos`, `mesa_registrada`, `data_registro`, `vsubtotal`, `vtotal`, `valor_dinheiro`, `valor_troco`,`formapaga`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?)";
                    }

                    $dados_pagamentos = json_encode($resultado, JSON_UNESCAPED_UNICODE);

                    // echo $dados_pagamentos;

                    if ($stmt = $conexao->prepare($sql)) {
                        // if ($count > 0) {
                        //     $stmt->bind_param("issssssiii", $idu_empresa, $nome_cliente, $status_pedido, $tipo_pedido, $dados_pagamentos, $mesa_pedido, date("d-m-Y H:i:s"), $subtotal_geral, $total_geral, $id_pedido);
                        // } else {
                        //     $stmt->bind_param("issssssiii", $idu_empresa, $nome_cliente, $id_pedido, $status_pedido, $tipo_pedido, $dados_pagamentos, $mesa_pedido, date("d-m-Y H:i:s"), $subtotal_geral, $total_geral);
                        // }

                        $date = date("Y-m-d H:i:s");
                        $tipo_pagamento_nome =  'rateio';

                        if ($stmt = $conexao->prepare($sql)) {
                            if ($count > 0) {
                                $stmt->bind_param(
                                    "isssssssiiiis",
                                    $idu_empresa,
                                    $nome_cliente,
                                    $status_pedido,
                                    $tipo_pedido,
                                    $dados_pagamentos,
                                    $mesa_pedido,
                                    $date,
                                    $subtotal_geral,
                                    $total_geral,
                                    $id_pedido,
                                    $valor_dinheiro,
                                    $valor_troco,
                                    $tipo_pagamento_nome
                                );
                            } else {
                                $stmt->bind_param(
                                    "isssssssiiiis",
                                    $idu_empresa,
                                    $nome_cliente,
                                    $id_pedido,
                                    $status_pedido,
                                    $tipo_pedido,
                                    $dados_pagamentos,
                                    $mesa_pedido,
                                    $date,
                                    $subtotal_geral,
                                    $total_geral,
                                    $valor_dinheiro,
                                    $valor_troco,
                                    $tipo_pagamento_nome
                                );
                            }
                        }


                        if ($stmt->execute()) {
                            //Configurando Mesas e Liberando Serviços , Finalizando pedido
                            $update_status = $conexao->query("UPDATE pedidos SET status='5' WHERE idpedido='" . $id_pedido . "'");
                            if (!$update_status) {
                                echo "Erro ao atualizar o status da mesa: " . $conexao->error;
                            }

                            $update_mesa = $conexao->query("UPDATE pedidos SET mesa='0' WHERE idpedido='" . $id_pedido . "'");
                            if (!$update_mesa) {
                                echo "Erro ao atualizar a mesa: " . $conexao->error;
                            }

                            // Inicializando variáveis para a tabela
                            $tipo_pagamento = htmlspecialchars($resultado['tipo']);
                            $tabela_linhas = '';

                            // Iterando sobre os dados para construir as linhas da tabela
                            foreach ($resultado['dados'] as $pagamento) {
                                $metodo = htmlspecialchars($pagamento['metodo']);
                                $valor = htmlspecialchars($pagamento['quantidade']);
                                $tabela_linhas .= "<tr><td>{$metodo}</td><td>{$valor}</td></tr>";
                            }

                            
                             // Pagamento Pago com Sucesso Valor do Pedido ;;;;;;;;;;;;;;;;;;;;;;;
                            // Sanitizar e formatar o valor da comissão corretamente e Atendimento
                            $total_comissao = isset($_POST['valor_comissao']) ? floatval(str_replace(',', '.', str_replace('.', '', $_POST['valor_comissao']))) : 0.00;
                            $nome_atendente = isset($_POST['nome_atendente']) ? sanitize_input($_POST['nome_atendente'], $conexao) : "";

                            // Verificar se já existe uma entrada para o pedido específico
                            $sql_verifica_pedido = "SELECT COUNT(*) as count FROM `pedidos` WHERE `idpedido` = :idpedido";
                            $stmt_verifica_pedido = $connect->prepare($sql_verifica_pedido);
                            $stmt_verifica_pedido->bindParam(':idpedido', $id_pedido, PDO::PARAM_STR);
                            $stmt_verifica_pedido->execute();
                            $result_pedido = $stmt_verifica_pedido->fetch(PDO::FETCH_ASSOC);

                            if ($result_pedido['count'] > 0) {
                                // Se já existe, faça a atualização
                                $sql_pedido = "UPDATE `pedidos` 
                   SET `comissao` = :comissao, `atendente` = :atendente 
                   WHERE `idpedido` = :idpedido";
                            } else {
                                // Se não existe, faça a inserção
                                $sql_pedido = "INSERT INTO `pedidos` (`idpedido`, `comissao`, `atendente`) 
                   VALUES (:idpedido, :comissao, :atendente)";
                            }

                            // Preparar a declaração SQL
                            $stmt_pedido = $connect->prepare($sql_pedido);

                            // Vincular parâmetros
                            $stmt_pedido->bindParam(
                                ':idpedido',
                                $id_pedido,
                                PDO::PARAM_STR
                            );
                            $stmt_pedido->bindParam(':comissao', $total_comissao, PDO::PARAM_STR);
                            $stmt_pedido->bindParam(':atendente', $nome_atendente, PDO::PARAM_STR);

                            // Executar a declaração
                            $stmt_pedido->execute();

                            // Formatando para exibição
                            $valor_total_formatado = number_format($valor_total, 2, ',', '.');
                            echo '<div class="container mt-5 mb-5">
                                    <div class="card">
                                        <div class="card-header bg-success text-white">
                                            <h1 class="card-title">Pagamento Feito com Sucesso!</h1>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-subtitle mb-2 text-muted">Dados inseridos ou atualizados:</h5>
                                            <ul class="list-group">
                                                <li class="list-group-item"><strong>ID Empresa:</strong> ' . htmlspecialchars($idu_empresa) . '</li>
                                                <li class="list-group-item"><strong>Nome Cliente:</strong> ' . htmlspecialchars($nome_cliente) . '</li>
                                                <li class="list-group-item"><strong>ID Pedido:</strong> ' . htmlspecialchars($id_pedido) . '</li>
                                                <li class="list-group-item"><strong>Status Pedido:</strong> ' . htmlspecialchars($status_pedido) . '</li>
                                                <li class="list-group-item"><strong>Tipo Pedido:</strong> ' . htmlspecialchars($tipo_pedido) . '</li>
                                                <li class="list-group-item"><strong>Data Registro:</strong> ' . htmlspecialchars($data_registro) . '</li>
                                                 <li class="list-group-item"><strong>SubTotal Pedido:</strong> ' . htmlspecialchars($subtotal_geral) . '</li>
                                                 <li class="list-group-item">
                   
                                                <li class="list-group-item">
                                                    <strong>Dados Pagamentos:</strong>
                                                    <table class="table table-bordered mt-3">
                                                        <thead>
                                                            <tr>
                                                                <th>Método</th>
                                                                <th>Valor</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            ' . $tabela_linhas . '
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>Valor Total</th>
                                                                <th>' . htmlspecialchars($valor_total_formatado) . '</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="card-footer text-muted">
                                        <p>Redirecionando para a página inicial em <span id="countdown">5</span> segundos...</p>
                                            <p class="text-right mb-0">Obrigado por utilizar nossos serviços!</p>
                                        </div>
                                    </div>
                                </div>';


                            echo '<script>
                                var seconds = 5;
                                var countdown = document.getElementById("countdown");

                                // Criando o formulário dinamicamente
                                var form = document.createElement("form");
                                form.action = "../verpedido.php";
                                form.method = "post";

                                // Adicionando o campo oculto ao formulário
                                var input = document.createElement("input");
                                input.type = "hidden";
                                input.name = "codigop";
                                input.value = "' . htmlspecialchars($id_pedido, ENT_QUOTES, 'UTF-8') . '"; // Passando o valor do PHP para o JavaScript
                                form.appendChild(input);

                                // Criando e adicionando um botão de submit ao formulário
                                var button = document.createElement("button");
                                button.type = "submit";
                                button.style.display = "none"; // Ocultando o botão
                                form.appendChild(button);

                                // Adicionando o formulário ao corpo do documento
                                document.body.appendChild(form);

                                setInterval(function() {
                                    seconds--;
                                    countdown.textContent = seconds;
                                    if (seconds <= 0) {
                                        // Enviar o formulário
                                        form.submit();
                                    }
                                }, 1000);
                            </script>';

                            $_SESSION['updateScreen'] = true;

                            exit();
                        } else {
                            echo 'Erro ao inserir ou atualizar os dados: ' . $stmt->error;
                        }
                        $stmt->close();
                    }
                } else {
                    echo 'Erro ao preparar a consulta: ' . $conexao->error;
                }
            } else {
                echo 'Erro: ID da empresa não corresponde ao ID do usuário.';
            }
        }
    }

    $conexao->close();
    ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</body>

</html>