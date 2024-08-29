<?php
// Função para sanitizar entrada
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

if (isset($cod_id)) {


// Função para renderizar o bloco de motoboy
function render_bloco_motoboy($motoboys, $motoboy_atual, $id_pedido)
{
    ob_start(); // Começa a captura da saída


    $nome_motoboy = truncarTexto($motoboy_atual, 10, 5)
?>
    <!-- Bloco HTML para seleção de Motoboy -->
    <div class="container">
        <div class="bloco_motoboy">
            <?php if ($motoboy_atual): ?>

                <button type="button" class="btn btn-primary" onclick="openModalMotoboy() ">Mudar Motoboy <?php echo $nome_motoboy; ?> <i class="fa fa-motorcycle mg-r-10" style="font-size:16px"></i></button>

                <!-- Modal -->
                <div id="motoboyModal" class="modal-motoboy">
                    <div class="modal-content">
                        <span class="close" onclick="closeModalMotoboy()">&times;</span>
                        <h2><?php echo $motoboy_atual ? "Trocar Motoboy" : "Selecionar Motoboy"; ?></h2>
                        <form action="./verpedido.php" method="post" id="motoboyFormTrocar">
                            <div class="form-group">
                                <label for="motoboySelect">Selecione o Motoboy:</label>
                                <select id="motoboySelect" name="motoboy_nome" class="form-control" required>
                                    <option value="">Selecione</option>
                                    <?php foreach ($motoboys as $motoboy): ?>
                                        <option value="<?php echo htmlspecialchars($motoboy['nome']); ?>" <?php echo ($motoboy['nome'] == $motoboy_atual) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($motoboy['nome']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <input type="hidden" name="idpedido" value="<?php echo htmlspecialchars($id_pedido); ?>" />
                            <input type="hidden" name="codigop" value="<?php echo htmlspecialchars($id_pedido); ?>" />
                            <button type="submit" class="btn btn-primary">Confirmar</button>
                        </form>
                    </div>
                </div>

                <style>
                    /* Estilos para o modal */
                    .modal-motoboy {
                        display: none;
                        position: fixed;
                        z-index: 999;
                        left: 0;
                        top: 0;
                        width: 100%;
                        height: 100%;
                        overflow: auto;
                        background-color: rgba(0, 0, 0, 0.5);
                    }

                    .modal-content {
                        background-color: #fff;
                        margin: 15% auto;
                        padding: 20px;
                        border: 1px solid #888;
                        width: 80%;
                        max-width: 500px;
                    }

                    .close {
                        color: #aaa;
                        float: right;
                        font-size: 28px;
                        font-weight: bold;
                    }

                    .close:hover,
                    .close:focus {
                        color: black;
                        text-decoration: none;
                        cursor: pointer;
                    }
                </style>

                <script>
                    // Função para abrir o modal
                    function openModalMotoboy() {
                        document.getElementById('motoboyModal').style.display = "block";
                    }

                    // Função para fechar o modal
                    function closeModalMotoboy() {
                        document.getElementById('motoboyModal').style.display = "none";
                    }
                </script>
            <?php else: ?>

                <h2>Selecionar Motoboy</h2>
                <p><?php echo "Para o pedido " . htmlspecialchars($id_pedido); ?></p>
                <form action="./verpedido.php" method="post" id="motoboyForm">
                    <div>
                        <img src="https://i.pinimg.com/originals/e5/07/d7/e507d704d4b6fdcb17116762fcd99acd.gif" />
                    </div>
                    <div class="form-group">
                        <label for="motoboySelect">Selecione o Motoboy:</label>
                        <select id="motoboySelect" name="motoboy_nome" class="form-control" required>
                            <option value="">Selecione</option>
                            <?php foreach ($motoboys as $motoboy): ?>
                                <option value="<?php echo htmlspecialchars($motoboy['nome']); ?>">
                                    <?php echo htmlspecialchars($motoboy['nome']).' - '. htmlspecialchars($motoboy['codigo_funcionario']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <input type="hidden" name="idpedido" value="<?php echo htmlspecialchars($id_pedido); ?>" />
                        <input type="hidden" name="codigop" value="<?php echo htmlspecialchars($id_pedido); ?>" />
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>

                </form>
            <?php endif; ?>
        </div>
    </div>
<?php
    return ob_get_clean(); // Retorna a captura da saída
}

// Conexão com o banco de dados (Exemplo)
// $connect = new PDO('mysql:host=localhost;dbname=seubanco', 'usuario', 'senha');

// Obtenha o ID do pedido
$id_pedido = $pedido->idpedido ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['motoboy_nome'])) {
    // Atualiza o pedido com o nome do motoboy selecionado
    $motoboy_nome = $_POST['motoboy_nome'];
    $codigop = $_POST['codigop'];

    $update_query = "UPDATE pedidos SET motoboy = :motoboy WHERE idpedido = :idpedido";
    $stmt = $connect->prepare($update_query);
    $stmt->bindParam(':motoboy', $motoboy_nome, PDO::PARAM_STR);
    $stmt->bindParam(':idpedido', $id_pedido, PDO::PARAM_STR);
    $stmt->execute();

    // Cria um formulário oculto em JS para enviar automaticamente
    echo '<form id="autoRedirectForm" action="./verpedido.php" method="post">';
    echo '<input type="hidden" name="codigop" value="' . htmlspecialchars($codigop) . '" />';
    echo '</form>';
    echo '<script type="text/javascript">
            document.getElementById("autoRedirectForm").submit();
          </script>';
    exit;
}

// Consulta para obter motoboys
$id_empresa = $cod_id; // Defina a variável $cod_id adequadamente
$query = "SELECT * FROM motoboy WHERE idu = :idu";
$stmt = $connect->prepare($query);
$stmt->bindParam(':idu', $id_empresa, PDO::PARAM_STR);
$stmt->execute();
$motoboys = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consulta para verificar se o pedido tem motoboy
$query_pedido = "SELECT motoboy FROM pedidos WHERE idpedido = :idpedido";
$stmt_pedido = $connect->prepare($query_pedido);
$stmt_pedido->bindParam(':idpedido', $pedido->idpedido, PDO::PARAM_STR);
$stmt_pedido->execute();
$pedido_motoboy = $stmt_pedido->fetch(PDO::FETCH_ASSOC);

$motoboy_atual = $pedido_motoboy['motoboy'] ?? null;

// Renderizar o bloco de motoboy
$renderizacao = render_bloco_motoboy($motoboys, $motoboy_atual, $id_pedido);

}else{
    echo '<div class="card" style="margin-top: 0.5rem;">
            <div class="card-body">
                <div class="alert alert-danger" role="alert">
                    <strong>Erro:</strong> Opps Você não tá autorizado...<br>
                </div>
            </div>
        </div>';
};
?>