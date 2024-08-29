<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Pedido</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Premium Quality and Responsive UI for Dashboard.">
    <meta name="author" content="ThemePixels">
    <title>RECEBIMENTO DE PEDIDOS</title>
    <link href="../lib/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../lib/Ionicons/css/ionicons.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/slim.css">
    <style>
        .card-header {
            background-color: #28a745;
            color: white;
        }

        .card-footer {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .toolbar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .toolbar button {
            margin-left: 10px;
        }
    </style>
</head>

<body>

    <div class="slim-navbar">
        <div class="container">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" id="shareBtn">
                        <i class="icon ion-ios-undo-outline"></i>
                        <span>Compartilhar</span>
                    </a>
                </li>

                <li class="nav-item">

                    <?php
                    if (isset($_POST['id_pedido'])) {
                    ?>
                        <form action="verpedido.php" method="post" class="d-flex justify-content-center">
                            <input type="hidden" name="codigop" value="<?php print $_POST['id_pedido']; ?>" />
                            <button style="cursor: pointer;outline:none;" type="submit" class="nav-link w-100 shadow-none"><i class="icon ion-ios-analytics-outline"></i>Voltar</button>
                        </form>
                    <?php
                    } else {
                    ?>
                        <form action="pdv.php" method="post" class="d-flex justify-content-center">

                            <button style="cursor: pointer;outline:none;" type="submit" class="nav-link w-100 shadow-none"><i class="icon ion-ios-analytics-outline"></i>Voltar</button>
                        </form>
                    <?php
                    }
                    ?>

                </li>
            </ul>
        </div>
    </div>

    <?php


    if (isset($_POST['id_pedido'])) {

    ?>

        <div class="container ">
            <div class="toolbar">

                <button class="btn btn-primary" onclick="printDiv()">Imprimir</button>
                <form action="comprovante.php" method="post" class="d-flex justify-content-center">
                    <input type="hidden" name="codigop" value="<?php print $_POST['id_pedido']; ?>" />
                    <button class="btn btn-success" type="submit"><i class="fa fa-search"></i> Pesquisar Comprovante</button>
                </form>
            </div>

        </div>
        <div class="container container-content-pagamento mb-3">
            <?php
            session_start();

            if (isset($_COOKIE['pdvx'])) {
                $codigo_id = $_COOKIE['pdvx'];
            }

            include_once('../../funcoes/Conexao.php');
            include_once('../../funcoes/Key.php');

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

                if (isset($_POST['id_pedido'])) {
                    $idu_empresa = $codigo_id;
                    $id_pedido = sanitize_input($_POST['id_pedido'], $conexao);

                    if (isset($id_pedido) && !empty($id_pedido)) {

                        // Consulta ao banco de dados
                        $query = "SELECT `idu`, `nome`, `status`, `tipo`, `dados_pagamentos`, `mesa_registrada`, `data_registro`, `vsubtotal`, `vtotal`,`valor_dinheiro`,`valor_troco`   
                        FROM `registrospagamentos` WHERE `idpedido` = ?";


                        if ($stmt = $conexao->prepare($query)) {
                            $stmt->bind_param("i", $id_pedido);
                            $stmt->execute();
                            $stmt->bind_result($idu_empresa, $nome, $status, $tipo, $dados_pagamentos, $mesa_registrada, $data_registro, $vsubtotal, $vtotal, $valor_dinheiro, $valor_troco);
                            $stmt->fetch();

                            // Inicializando variáveis para a tabela
                            $resultado = json_decode($dados_pagamentos, true);
                            // var_dump($resultado);


                            if (empty($resultado) || count($resultado) === 0) {
                                echo '
                        <div class="card">
                            <div class="card-header bg-danger text-white">
                                <h4 class="card-title">Erro</h4>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-danger" role="alert">
                                    <strong>Erro:</strong> Erro ao Encontrar Pagamento...<br>
                                </div>
                            </div>
                        </div>';
                            } else {

                                $tipo_pagamento = htmlspecialchars($resultado['tipo']);
                                $tabela_linhas = '';

                                // Iterando sobre os dados para construir as linhas da tabela
                                foreach ($resultado['dados'] as $pagamento) {
                                    $metodo = htmlspecialchars($pagamento['metodo']);
                                    $valor = number_format(floatval($pagamento['quantidade']), 2, ',', '.');
                                    $tabela_linhas .= "<tr><td>{$metodo}</td><td>R$ {$valor}</td></tr>";
                                }

                                // Formatando para exibição
                                $subtotal_formatado = $vsubtotal;
                                $valor_total_formatado = $vtotal;

                                echo '<div class="card">
        <div class="card-header">
            <h1 class="card-title text-light">Detalhes do Pedido: ' . htmlspecialchars($tipo_pagamento) . '</h1>
        </div>
        <div class="card-body">
            <h5 class="card-subtitle mb-2 text-muted">Dados do Pedido:</h5>
            <ul class="list-group mb-4">
                <li class="list-group-item"><strong>ID Empresa:</strong> ' . htmlspecialchars($idu_empresa) . '</li>
                <li class="list-group-item"><strong>Nome Cliente:</strong> ' . htmlspecialchars($nome) . '</li>
                <li class="list-group-item"><strong>ID Pedido:</strong> ' . htmlspecialchars($id_pedido) . '</li>
                <li class="list-group-item"><strong>Status Pedido:</strong> ' . htmlspecialchars($status) . '</li>
                <li class="list-group-item"><strong>Tipo Pedido:</strong> ' . htmlspecialchars($tipo) . '</li>
                <li class="list-group-item"><strong>Mesa Registrada:</strong> ' . htmlspecialchars($mesa_registrada) . '</li>
                <li class="list-group-item"><strong>Data Registro:</strong> ' . htmlspecialchars($data_registro) . '</li>
                <li class="list-group-item"><strong>Subtotal Geral:</strong> R$ ' . htmlspecialchars($subtotal_formatado) . '</li>
                <li class="list-group-item"><strong>Total Geral:</strong> R$ ' . htmlspecialchars($valor_total_formatado) . '</li>
                <li class="list-group-item">
                    <strong>Dinheiro em Caixa:</strong> R$ ' . (empty($valor_dinheiro) ? "0" : htmlspecialchars($valor_dinheiro)) . '
                </li>
                <li class="list-group-item">
                    <strong>Valor Troco:</strong> R$ ' . (empty($valor_troco) ? "0" : htmlspecialchars($valor_troco)) . '
                </li>
            </ul>
            <h5 class="card-subtitle mb-2 text-muted">Dados dos Pagamentos:</h5>
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
                        <th>R$ ' . htmlspecialchars($valor_total_formatado) . '</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer text-muted">
            <p class="text-right mb-0">Obrigado por utilizar nossos serviços!</p>
        </div>
    </div>';

                                $stmt->close();
                            }
                        } else {
                            echo 'Erro ao preparar a consulta: ' . $conexao->error;
                        }
                    } else {
                        echo '
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h4 class="card-title">Erro</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger" role="alert">
                            <strong>Erro:</strong> Não foi possível buscar o histórico de pagamento...<br>
                        </div>
                    </div>
                </div>';
                    }
                } else {
                    echo '
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="card-title">Erro</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger" role="alert">
                        <strong>Erro:</strong> Não foi possível buscar o histórico de pagamento...<br>
                    </div>
                </div>
            </div>';
                }
            }

            $conexao->close();
            ?>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
            function printDiv() {
                var divContents = document.querySelector('.container-content-pagamento').innerHTML;
                var a = window.open('', '', 'height=600, width=800');
                a.document.write('<html>');
                a.document.write('<head><title>Imprimir</title>');
                a.document.write('<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">');
                a.document.write('</head>');
                a.document.write('<body>');
                a.document.write(divContents);
                a.document.write('</body></html>');
                a.document.close();
                a.print();
            }

            document.getElementById('shareBtn').addEventListener('click', function() {
                alert("Criando Copia de Compartilhamento...")
            });
        </script>


    <?php
    } else {
    ?>
        <div class="card" style="margin-top: 0.5rem;">
            <div class="card-header bg-danger text-white">
                <h4 class="card-title">Erro</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-danger" role="alert">
                    <strong>Erro:</strong> Não foi possível buscar o histórico de pagamento...<br>
                </div>
            </div>
        </div>
    <?php
    }
    ?>

</body>


</html>