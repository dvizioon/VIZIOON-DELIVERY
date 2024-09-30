<?php
session_start();

// Verifica se a sessão contém os dados necessários
if (!isset($_SESSION['mensagem_usuario']) || !isset($_SESSION['mensagem_whatsapp'])) {
    unset($_SESSION['id_cliente']);
    session_write_close();
    header("Location: ./"); // Redireciona se não houver dados
    exit();
}

// Recupera os dados da sessão
$dados_usuario = $_SESSION['mensagem_usuario'];
$mensagem_whatsapp = $_SESSION['mensagem_whatsapp'];

// Limpa os dados da sessão após exibição
unset($_SESSION['mensagem_usuario']);
unset($_SESSION['mensagem_whatsapp']);

// Extrai o primeiro nome do usuário
$nome_usuario = explode(' ', $dados_usuario['nome'])[0];

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            width: 90%;
            max-width: 800px;
            padding: 1rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            font-size: 18px;
            color: #fff;
            background-color: #28a745;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #218838;
        }

        img {
            border-radius: 8px;
            max-width: 100%;
            height: auto;
        }

        .message-box {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #e9ecef;
            display: inline-block;
            font-size: 18px;
            margin-bottom: 1rem;
        }

        .responsive-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
        }

        .responsive-table thead {
            background-color: #f4f4f4;
        }

        .responsive-table th,
        .responsive-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .responsive-table th {
            background-color: #f4f4f4;
        }

        .responsive-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table-rp {
            width: 100%;
            /* background: #218838; */
            overflow: hidden;
            overflow: auto;
            height: 12rem;
        }

        @media (max-width: 768px) {

            html {
                font-size: 70%;
            }

            img {
                width: 50%;
            }

            .table-rp {
                width: 100%;
                /* background: #218838; */
                overflow: hidden;
                overflow: auto;
                height: 12rem;
            }
        }
    </style>
</head>

<body>


    <div class="container">



        <h1>Pedido Confirmado!</h1>
        <div class="section-wrapper">
            <p class="message-box">Obrigado por comprar na <?php echo $dados_usuario['nome_empresa']; ?>, <?php echo htmlspecialchars($nome_usuario); ?>!</p>
            <p>Seu pedido foi recebido com sucesso.</p>
            <img src="img/fim.gif" width="250" alt="Finalização">
            <div class="table-rp">
                <?php echo $dados_usuario['pedidos']; ?>
            </div>

            <p>CLIQUE ABAIXO PARA FINALIZAR</p>
            <form id="whatsapp-form" action="javascript:void(0);">
                <input type="hidden" id="celular" value="<?php echo htmlspecialchars($dados_usuario['celcli']); ?>">
                <input type="hidden" id="mensagem" value="<?php echo htmlspecialchars($mensagem_whatsapp, ENT_QUOTES, 'UTF-8'); ?>">
                <button type="button" class="btn" onclick="enviarMensagem()">FINALIZAR</button>
            </form>
        </div>
    </div>

    <script type="text/javascript">
        function enviarMensagem() {
            let celular = document.querySelector('#celular').value;
            let mensagem = document.querySelector('#mensagem').value;
            let url = `https://api.whatsapp.com/send?phone=${celular}&text=${encodeURIComponent(mensagem)}`;
            window.open(url, '_blank');

            window.location.href = "./"
            unset($_SESSION['id_cliente']);
            session_write_close();
        }
    </script>
</body>

</html>