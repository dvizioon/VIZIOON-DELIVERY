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

        // Captura dos dados com valores padrão
        $idu_empresa = isset($_POST['idu_empresa']) ? sanitize_input($_POST['idu_empresa'], $conexao) : '';
        $nome_cliente = isset($_POST['nome_cliente']) ? sanitize_input($_POST['nome_cliente'], $conexao) : '';
        $id_pedido = isset($_POST['id_pedido']) ? sanitize_input($_POST['id_pedido'], $conexao) : '';
        $metodo_pagamento = isset($_POST['metodo_pagamento']) ? sanitize_input($_POST['metodo_pagamento'], $conexao) : '';
        $nome_metodo_pagamento = isset($_POST['nome_metodo_pagamento']) ? sanitize_input($_POST['nome_metodo_pagamento'], $conexao) : '';
        $status_pedido = isset($_POST['status_pedido']) ? sanitize_input($_POST['status_pedido'], $conexao) : '';
        $tipo_pedido = isset($_POST['tipo_pedido']) ? sanitize_input($_POST['tipo_pedido'], $conexao) : '';
        $mesa_pedido = isset($_POST['mesa_pedido']) ? sanitize_input($_POST['mesa_pedido'], $conexao) : '';
        $data_registro = isset($_POST['data_registro']) ? sanitize_input($_POST['data_registro'], $conexao) : '';
        $subtotal_geral = isset($_POST['subtotal_geral']) ? sanitize_input($_POST['subtotal_geral'], $conexao) : '';
        $total_geral = isset($_POST['total_geral']) ? sanitize_input($_POST['total_geral'], $conexao) : '';
        $valor_dinheiro = isset($_POST['valor_dinheiro']) ? sanitize_input($_POST['valor_dinheiro'], $conexao) : '0';
        $valor_troco = isset($_POST['valor_troco']) ? sanitize_input($_POST['valor_troco'], $conexao) : '0';
        // $nome_atendente = isset($_POST['nome_atendente']) ? sanitize_input($_POST['nome_atendente'], $conexao) : 'Sem Nome';
        // $total_comissao = isset($_POST['valor_comissao']) ? $_POST['valor_comissao']: "";
        // echo $total_comissao;
            // echo $nome_atendente;
            // var_dump($tipo_pedido);

        // Remove formatos dos valores para salvar no banco
        $total_geral = floatval(str_replace(',', '.', str_replace('.', '', $total_geral)));
        $subtotal_geral = floatval(str_replace(',', '.', str_replace('.', '', $subtotal_geral)));

        $resultado = [
            'tipo' => 'a vista',
            'dados' => [
                ["metodo" => $nome_metodo_pagamento, "quantidade" => $total_geral]
            ]
        ];

        $dados_pagamentos = json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);


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


            $date = date("Y-m-d H:i:s");
            $tipo_pagamento_nome =  'a vista';



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

                if ($stmt->execute()) {
                    // Configurando Mesas e Liberando Serviços, Finalizando pedido
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
                        $valor = htmlspecialchars(number_format($pagamento['quantidade'], 2, ',', '.'));
                        $tabela_linhas .= "<tr><td>{$metodo}</td><td>{$valor}</td></tr>";
                    }

                    // Formatar os valores totais como strings
                    $subtotal_geral_formatado = number_format($subtotal_geral, 2, ',', '.');
                    $total_geral_formatado = number_format($total_geral, 2, ',', '.');


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
                                                <li class="list-group-item"><strong>SubTotal Pedido:</strong> ' . htmlspecialchars($subtotal_geral_formatado) . '</li>
                                                <li class="list-group-item">
                                                    <strong>Dinheiro em Caixa:</strong> R$ ' . (empty($valor_dinheiro) ? "0" : htmlspecialchars($valor_dinheiro)) . '
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>Valor Troco:</strong> R$ ' . (empty($valor_troco) ? "0" : htmlspecialchars($valor_troco)) . '
                                                </li>
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
                                                    </table>
                                                </li>
                                                <li class="list-group-item"><strong>Total Pedido:</strong> ' . htmlspecialchars($total_geral_formatado) . '</li>
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
                } else {
                    echo "Erro ao inserir ou atualizar dados: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Erro na preparação da query: " . $conexao->error;
            }
        } else {
            echo "Erro na preparação da query de contagem: " . $conexao->error;
        }

        $conexao->close();
    } else {
        echo "Método de requisição inválido.";
    }
    ?>

</body>

</html>