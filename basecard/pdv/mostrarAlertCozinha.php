<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta de Cozinha</title>
    <style>
        .modalAletCozinha {
            display: flex;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            /* overflow: auto; */
            overflow-y: scroll;
            background-color: rgba(0, 0, 0, 0.4);

            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #fefefe;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            margin: 5% auto;
            /* position: relative; */
            display: flex;
            align-items: center;
            flex-direction: column;


        }

        .modalAletCozinha h4 {
            margin: 0;
        }

        .modalAletCozinha ul {
            list-style-type: none;
            padding: 0;
            width: 80%;

        }

        .modalAletCozinha li {
            margin: 10px 0;
            display: flex;
            flex-direction: column;
            padding: 1rem;
            width: 100%;


        }

        .modalAletCozinha li>strong {
            display: flex;
            align-items: center;

        }

        .modalAletCozinha button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 1rem;
            cursor: pointer;
            font-size: 1rem;
            border-radius: 40rem;
        }

        .alertIMG {
            display: flex;
            align-items: center;
            width: 100%;
            border: 2px solid black;
            border-radius: 1rem;
            margin: 1rem 0;
            padding: 0.5rem 1rem;

        }

        .alertIMG img {
            width: 100%;
            border-radius: 1rem;
        }

        .modalAletCozinha button:hover {
            background-color: #45a049;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 25px;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: red;
        }

        .modalAletCozinha::-webkit-scrollbar {
            height: 10px;
            width: 10px;
        }

        .modalAletCozinha::-webkit-scrollbar-track {
            border-radius: 5px;
            background-color: #dfe9eb00;
        }

        .modalAletCozinha::-webkit-scrollbar-track:hover {
            background-color: #b8c0c200;
        }

        .modalAletCozinha::-webkit-scrollbar-track:active {
            background-color: #ff000000;
        }

        .modalAletCozinha::-webkit-scrollbar-thumb {
            border-radius: 5px;
            background-color: #ff910000;
        }

        .modalAletCozinha::-webkit-scrollbar-thumb:hover {
            background-color: #ff910000;
        }

        .modalAletCozinha::-webkit-scrollbar-thumb:active {
            background-color: #ff9100;
        }
    </style>
</head>


<?php

// Consulta para obter pedidos com status 'entregue' e informações adicionais
$query = "
    SELECT c.idpedido, c.data, p.nome, p.mesa, p.vtotal, p.entrada, p.fpagamento, p.cidade, p.numero, 
           p.complemento, p.rua, p.bairro, p.troco, p.pessoas, p.obs, p.hora, p.celular, p.taxa, p.vsubtotal, 
           p.vadcionais, p.comissao, p.motoboy, p.atendente
    FROM cozinha c
    JOIN pedidos p ON c.idpedido = p.idpedido
    WHERE c.status_cozinha = 'entregue'
";
$stmt = $connect->prepare($query);
$stmt->execute();
$pedidos_entregues = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<body>

    <?php if (!empty($pedidos_entregues)): ?>
        <div id="modalPedidosEntregues" class="modalAletCozinha" style="display:block;">
            <div class="modal-content">
                <span class="close" onclick="fecharModal()">&times;</span>
                <h4>Pedidos Prontos para Entrega</h4>
                <ul>
                    <?php foreach ($pedidos_entregues as $pedido): ?>
                        <div class="alertIMG">
                            <li>
                                <!-- <p><strong>Pedido:</strong> <?= htmlspecialchars($pedido['idpedido']); ?></p> -->
                                <p> <strong>Data:</strong> <?= htmlspecialchars($pedido['data']); ?></p>
                                <p><strong>Nome:</strong> <?= htmlspecialchars($pedido['nome']); ?></p>

                                <p>
                                <form action="verpedido.php" method="post">
                                    <input type="hidden" name="codigop" value="<?php print htmlspecialchars($pedido['idpedido']); ?>" />
                                    <button style="cursor: pointer;" type="submit" class="btn btn-purple btn-sm w-100">Ir para Pedido</button>
                                    <button class="bg-danger w-100" style="cursor: pointer;" type="button" class="btn btn-purple btn-sm w-100">Informações Pedido</button>
                                </form>
                                </p>
                            </li>
                            <img src="https://placehold.co/200x150?text=<?php echo htmlspecialchars($pedido['idpedido']);  ?>" alt="ola">
                        </div>
                    <?php endforeach; ?>


                </ul>


            </div>
        </div>
        <script type="text/javascript">
            function fecharModal() {
                document.getElementById('modalPedidosEntregues').style.display = 'none';
            }

            // Fechar o modal se o usuário clicar fora da área do modal
            window.onclick = function(event) {
                if (event.target == document.getElementById('modalPedidosEntregues')) {
                    fecharModal();
                }
            }
        </script>
    <?php endif; ?>

</body>

</html>