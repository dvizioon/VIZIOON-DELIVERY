
<?php

function consultarQuantidadeCompras($connect, $idu = null, $celular = null)
{
    // Verifica se pelo menos um dos parâmetros (idu ou celular) foi fornecido
    if (empty($idu) && empty($celular)) {
        return json_encode(["status" => "error", "message" => "IDU ou número de celular devem ser fornecidos."]);
    }

    // Define a consulta SQL com base nos parâmetros fornecidos
    $sql = "SELECT COUNT(*) as total_compras FROM cumpomClientes WHERE 1=1";

    if (!empty($idu)) {
        $sql .= " AND idu = :idu";
    }
    if (!empty($celular)) {
        $sql .= " AND celular = :celular";
    }

    $stmt = $connect->prepare($sql);

    // Liga os parâmetros dinamicamente
    if (!empty($idu)) {
        $stmt->bindParam(':idu', $idu);
    }
    if (!empty($celular)) {
        $stmt->bindParam(':celular', $celular);
    }

    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se a consulta retornou algum resultado
    if (!$result || $result['total_compras'] == 0) {
        return json_encode(0); // Retorna 0 se não houver compras
    }

    // Retorna o total de compras como um número simples
    return json_encode($result['total_compras']);
}

function addCumpoCliente($connect, $configuracoesCupom, $tipo, $dadosCliente = [], $idpedido = "", $totalGeral = "")
{
    // Verifica se o cupom está ativo
    if ($configuracoesCupom['status'] !== 'sim') {
        return "Cupom não está ativo.";
    }

    // Obtendo o dia da semana atual
    $diaAtual = strtolower(date('l')); // Retorna o dia da semana em inglês (Monday, Tuesday, etc.)
    $diasSemanaPortugues = [
        'monday' => 'segunda',
        'tuesday' => 'terca',
        'wednesday' => 'quarta',
        'thursday' => 'quinta',
        'friday' => 'sexta',
        'saturday' => 'sabado',
        'sunday' => 'domingo'
    ];

    // Traduzir o dia da semana para português
    $diaSemanaAtual = $diasSemanaPortugues[$diaAtual] ?? null;

    // Verifica se o dia atual está disponível no cupom
    if ($configuracoesCupom['dias_da_semana'][$diaSemanaAtual] !== 'sim') {
        return "Cupom não está disponível hoje.";
    }

    // Se o tipo for "criar", adiciona o cupom para o cliente
    if ($tipo === 'criar') {
        // Verifica se os dados do cliente estão completos
        if (empty($dadosCliente) || count($dadosCliente) < 6) { // Deve ter 6 parâmetros
            return "Dados do cliente incompletos. Não é possível adicionar o cupom.";
        }

        // Destrói os dados em variáveis separadas
        list($idu, $idpedido, $nome, $data, $celular, $status) = $dadosCliente;

        // Verifica se algum dos dados está vazio
        if (empty($idu) || empty($idpedido) || empty($nome) || empty($data) || empty($celular) || empty($status)) {
            return "Todos os campos de dados do cliente são obrigatórios.";
        }

        // Insere o cupom para o cliente na tabela cumpomClientes
        $sql_insert = "INSERT INTO cumpomClientes (idu, idpedido, nome, data, celular, status) 
                       VALUES (:idu, :idpedido, :nome, :data, :celular, :status)";
        $stmt_insert = $connect->prepare($sql_insert);
        $stmt_insert->bindParam(':idu', $idu);
        $stmt_insert->bindParam(':idpedido', $idpedido);
        $stmt_insert->bindParam(':nome', $nome);
        $stmt_insert->bindParam(':data', $data);
        $stmt_insert->bindParam(':celular', $celular);
        $stmt_insert->bindParam(':status', $status);

        // Tenta executar a query e retorna a mensagem adequada
        if ($stmt_insert->execute()) {
            return "Cupom adicionado com sucesso para o cliente.";
        } else {
            // Captura o erro e retorna a mensagem detalhada
            $errorInfo = $stmt_insert->errorInfo();
            return "Erro ao adicionar o cupom para o cliente: " . $errorInfo[2];
        }
    }

    return "Ação inválida.";
}



// function mostrarCumpoCliente($connect, $configuracoesCupom, $tipo, $celular = "", $totalGeral = "", $id_pedido = "")
// {
//     if ($tipo == "mostrar") {
//         // Verifica se o cupom está ativo
//         if ($configuracoesCupom['status'] !== 'sim') {
//             return "Cupom não está ativo.";
//         }

//         // Obtendo o dia da semana atual
//         $diaAtual = strtolower(date('l'));
//         $diasSemanaPortugues = [
//             'monday' => 'segunda',
//             'tuesday' => 'terca',
//             'wednesday' => 'quarta',
//             'thursday' => 'quinta',
//             'friday' => 'sexta',
//             'saturday' => 'sabado',
//             'sunday' => 'domingo'
//         ];
//         $diaSemanaAtual = $diasSemanaPortugues[$diaAtual] ?? null;

//         // Verifica se o cupom está disponível no dia atual
//         if ($configuracoesCupom['dias_da_semana'][$diaSemanaAtual] !== 'sim') {
//             return "Cupom não está disponível hoje.";
//         }

//         $celular = preg_replace('/[^0-9]/', '', $celular); // Remove caracteres não numéricos

//         // Verificar o número de compras do cliente baseado no celular
//         $sql_count = "SELECT COUNT(*) as total FROM cumpomClientes WHERE celular = :celular";
//         $stmt_count = $connect->prepare($sql_count);
//         $stmt_count->bindParam(':celular', $celular);
//         $stmt_count->execute();
//         $result = $stmt_count->fetch(PDO::FETCH_ASSOC);
//         $total_compras = $result['total'] ?? 0;

//         $limite_compras = $configuracoesCupom['quantidade_compras'] ?? 0;
//         $compras_restantes = max($limite_compras - $total_compras, 0);

//         // Calcula a porcentagem de progresso em relação ao limite de compras
//         $progresso = ($limite_compras > 0) ? min(($total_compras / $limite_compras) * 100, 100) : 0;

//         // Verifica se o cliente atingiu ou ultrapassou o limite de compras para o desconto
//         if ($total_compras >= $limite_compras && $limite_compras > 0) {
//             // Calcula o desconto
//             $desconto = ($totalGeral * $configuracoesCupom['valor_porcentagem']) / 100;
//             $valorComDesconto = $totalGeral - $desconto;

//             // Exibe o modal com a opção de confirmar o desconto
//             return mostrarModalDesconto($valorComDesconto, $configuracoesCupom['valor_porcentagem'], $celular, $totalGeral, $total_compras, $limite_compras, $connect, $id_pedido);
//         }

//         // Exibe o card com o progresso de compras
//         return '
//             <div class="card mt-4" style="border: 2px solid #007bff; border-radius: 10px;">
//                 <div class="card-body text-center">
//                     <h5 class="card-title" style="color: #007bff;">Progresso de Compras</h5>
//                     <p class="card-text">Cliente realizou <strong>' . $total_compras . '</strong> de <strong>' . $limite_compras . '</strong> compras para obter o desconto.</p>

//                     <!-- Barra de progresso visual -->
//                     <div class="progress" style="height: 20px;">
//                         <div class="progress-bar" role="progressbar" style="width: ' . $progresso . '%; background-color: #28a745;" aria-valuenow="' . $progresso . '" aria-valuemin="0" aria-valuemax="100">' . round($progresso) . '%</div>
//                     </div>

//                     <p style="margin-top: 10px; color: #999;">Faltam <strong>' . $compras_restantes . '</strong> compras para atingir o limite e receber o desconto.</p>

//                     <!-- Botão para finalizar compras -->
//                     <!-- 
//                     <button class="btn btn-primary mt-3" id="finalizarCompraBtn">Finalizar Compra</button>
//                     -->
//                 </div>
//             </div>

//             <!-- Script para ação do botão de finalizar compra -->
//             <script>
//                 $(document).ready(function() {
//                     $("#finalizarCompraBtn").on("click", function() {
//                         // Reabertura do modal ou execução de outras ações
//                         $("#modalDesconto").modal("show");
//                     });
//                 });
//             </script>
//         ';
//     }

//     return "Ação inválida.";
// }

function mostrarCumpoCliente($connect, $configuracoesCupom, $tipo, $celular = "", $totalGeral = "", $id_pedido = "")
{
    if ($tipo == "mostrar") {
        // Verifica se o cupom está ativo
        if ($configuracoesCupom['status'] !== 'sim') {
            return "Cupom não está ativo.";
        }

        // Obtendo o dia da semana atual
        $diaAtual = strtolower(date('l'));
        $diasSemanaPortugues = [
            'monday' => 'segunda',
            'tuesday' => 'terca',
            'wednesday' => 'quarta',
            'thursday' => 'quinta',
            'friday' => 'sexta',
            'saturday' => 'sabado',
            'sunday' => 'domingo'
        ];
        $diaSemanaAtual = $diasSemanaPortugues[$diaAtual] ?? null;

        // Verifica se o cupom está disponível no dia atual
        if ($configuracoesCupom['dias_da_semana'][$diaSemanaAtual] !== 'sim') {
            return "Cupom não está disponível hoje.";
        }

        $celular = preg_replace('/[^0-9]/', '', $celular); // Remove caracteres não numéricos

        // Verificar o número de compras do cliente baseado no celular
        $sql_count = "SELECT COUNT(*) as total FROM cumpomClientes WHERE celular = :celular";
        $stmt_count = $connect->prepare($sql_count);
        $stmt_count->bindParam(':celular', $celular);
        $stmt_count->execute();
        $result = $stmt_count->fetch(PDO::FETCH_ASSOC);
        $total_compras = $result['total'] ?? 0;

        $limite_compras = $configuracoesCupom['quantidade_compras'] ?? 0;
        $compras_restantes = max($limite_compras - $total_compras, 0);

        // Calcula a porcentagem de progresso em relação ao limite de compras
        $progresso = ($limite_compras > 0) ? min(($total_compras / $limite_compras) * 100, 100) : 0;

        // Verifica se o cliente atingiu ou ultrapassou o limite de compras para o desconto
        if ($total_compras >= $limite_compras && $limite_compras > 0) {
            // Calcula o desconto
            $desconto = ($totalGeral * $configuracoesCupom['valor_porcentagem']) / 100;
            $valorComDesconto = $totalGeral - $desconto;

            // Exibe o card de progresso mesmo que o cliente tenha atingido o limite
            return '
                <div class="card mt-4" style="border: 2px solid #007bff; border-radius: 10px;">
                    <div class="card-body text-center">
                        <h5 class="card-title" style="color: #007bff;">Progresso de Compras</h5>
                        <p class="card-text">Cliente realizou <strong>' . $total_compras . '</strong> de <strong>' . $limite_compras . '</strong> compras para obter o desconto.</p>
                        
                        <!-- Barra de progresso visual -->
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" style="width: ' . $progresso . '%; background-color: #28a745;" aria-valuenow="' . $progresso . '" aria-valuemin="0" aria-valuemax="100">' . round($progresso) . '%</div>
                        </div>

                        <p style="margin-top: 10px; color: #999;">Você já atingiu o limite de compras e pode receber o desconto!</p>

                        <!-- Botão para abrir o modal de desconto -->
                        <button class="btn btn-success mt-3" id="aplicarDescontoBtn">Aplicar Desconto</button>
                    </div>
                </div>

                <!-- Script para abrir o modal de desconto -->
                <script>
                    $(document).ready(function() {
                        $("#aplicarDescontoBtn").on("click", function() {
                            // Exibir o modal de desconto
                            $("#modalDesconto").modal("show");
                        });
                    });
                </script>

                ' . mostrarModalDesconto($valorComDesconto, $configuracoesCupom['valor_porcentagem'], $celular, $totalGeral, $total_compras, $limite_compras, $connect, $id_pedido) . '
            ';
        }

        // Exibe o card com o progresso de compras quando o limite ainda não foi atingido
        return '
            <div class="card mt-4" style="border: 2px solid #007bff; border-radius: 10px;">
                <div class="card-body text-center">
                    <h5 class="card-title" style="color: #007bff;">Progresso de Compras</h5>
                    <p class="card-text">Cliente realizou <strong>' . $total_compras . '</strong> de <strong>' . $limite_compras . '</strong> compras para obter o desconto.</p>
                    
                    <!-- Barra de progresso visual -->
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar" role="progressbar" style="width: ' . $progresso . '%; background-color: #28a745;" aria-valuenow="' . $progresso . '" aria-valuemin="0" aria-valuemax="100">' . round($progresso) . '%</div>
                    </div>

                    <p style="margin-top: 10px; color: #999;">Faltam <strong>' . $compras_restantes . '</strong> compras para atingir o limite e receber o desconto.</p>
                </div>
            </div>
        ';
    }

    return "Ação inválida.";
}


function mostrarModalDesconto($valorComDesconto, $valor_porcentagem, $celular, $valor_original, $total_compras, $quantidade_compras_limite, $connect, $id_pedido)
{
    // Consulta para pegar o total de compras feitas pelo cliente baseado no celular (independente do status)
    $sql_total_compras = "SELECT COUNT(*) as total_compras FROM cumpomClientes WHERE celular = :celular";
    $stmt_total_compras = $connect->prepare($sql_total_compras);
    $stmt_total_compras->bindParam(':celular', $celular);
    $stmt_total_compras->execute();
    $result_total_compras = $stmt_total_compras->fetch(PDO::FETCH_ASSOC);
    $total_compras_realizadas = $result_total_compras['total_compras'] ?? 0;

    // Calcula o valor do desconto por compra
    $desconto_por_compra = $valor_original * ($valor_porcentagem / 100);

    // Exibe o número total de compras feitas e finalizadas no card
    $card_content = '
        <div style="border: 2px solid #007bff; border-radius: 5px; margin: 5px; padding: 10px; background-color: #f8f9fa; text-align: center;">
            <p><strong>Total de Compras Feitas: ' . $total_compras_realizadas . '</strong></p>
        </div>
    ';

    // HTML do modal com os botões de incrementar e decrementar limite
    return '
        <!-- Modal -->
        <div id="modalDesconto" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
       
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                
                    <div class="modal-header" style="background-color: #007bff; color: white;">
                        <h5 class="modal-title" id="modalLabel">Desconto Disponível</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" style="color: white;">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <!-- Informações sobre o desconto -->
                        <div class="mb-3 text-center">
                            <p>Você está elegível para um desconto de <strong>' . $valor_porcentagem . '%</strong> por cada compra!</p>
                            <p>Valor original: <strong>R$ ' . number_format($valor_original, 2, ',', '.') . '</strong></p>
                            <p>Valor com desconto: <strong id="valorComDesconto">R$ ' . number_format($valor_original - $desconto_por_compra, 2, ',', '.') . '</strong></p>
                        </div>

                        <!-- Contador para selecionar o limite personalizado -->
                        <div class="text-center">
                            <label>Escolha o limite de compras para aplicar o desconto:</label>
                            <div class="d-flex justify-content-center align-items-center">
                                <button class="btn btn-outline-primary" id="decrementBtn" onclick="decrementarLimite()">-</button>
                                <input type="text" id="quantidadeLimite" class="form-control text-center mx-2" value="' . $quantidade_compras_limite . '" style="max-width: 60px;" readonly>
                                <button class="btn btn-outline-primary" id="incrementBtn" onclick="incrementarLimite()">+</button>
                            </div>
                            <p><small>Limite Máximo: ' . $total_compras_realizadas . ' compras</small></p>
                        </div>

                        <!-- Card com as informações do cliente -->
                        ' . $card_content . '
                    </div>

                    <!-- Rodapé do Modal -->
                    <div class="modal-footer d-flex justify-content-between">
                        <form action="./cupom/confirmar_desconto.php" method="POST">
                            <input type="hidden" name="celular" value="' . htmlspecialchars($celular) . '">
                            <input type="hidden" name="idpedido" value="' . htmlspecialchars($id_pedido) . '">
                            <input type="hidden" name="valor_com_desconto" value="' . htmlspecialchars($valor_original - $desconto_por_compra) . '" id="hiddenDesconto">
                            <input type="hidden" name="quantidade_compras_limite" value="' . htmlspecialchars($quantidade_compras_limite) . '" id="hiddenLimite">
                            <button type="submit" class="btn btn-success">Confirmar Desconto</button>
                        </form>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Script para exibir o modal e gerenciar o incremento/decremento -->
        <script>
            var limiteAtual = ' . $quantidade_compras_limite . ';
            var limiteMaximo = ' . $total_compras_realizadas . ';
            var valorOriginal = ' . $valor_original . ';
            var descontoPorCompra = ' . $desconto_por_compra . ';

            function incrementarLimite() {
                if (limiteAtual + 1 <= limiteMaximo) { // Incrementa em 1 por vez
                    limiteAtual += 1;
                    document.getElementById("quantidadeLimite").value = limiteAtual;
                    atualizarDesconto();
                }
            }

            function decrementarLimite() {
                if (limiteAtual - 1 >= 0) { // Decrementa em 1 por vez
                    limiteAtual -= 1;
                    document.getElementById("quantidadeLimite").value = limiteAtual;
                    atualizarDesconto();
                }
            }

            function atualizarDesconto() {
                // Calcula o desconto total baseado no número de compras
                var descontoTotal = descontoPorCompra * limiteAtual;
                var novoValorComDesconto = valorOriginal - descontoTotal;
                
                // Atualiza os valores no DOM
                document.getElementById("valorComDesconto").textContent = "R$ " + novoValorComDesconto.toFixed(2).replace(".", ",");
                
                // Atualiza o valor que será enviado no formulário
                document.getElementById("hiddenDesconto").value = novoValorComDesconto.toFixed(2);
                document.getElementById("hiddenLimite").value = limiteAtual;
            }

            $(document).ready(function() {
                $("#modalDesconto").modal("show");

                $("#modalDesconto .close, .btn-secondary").on("click", function() {
                    $("#modalDesconto").modal("hide");
                });
            });
        </script>
    ';
}
