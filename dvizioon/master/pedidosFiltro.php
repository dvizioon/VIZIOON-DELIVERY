<?php
require_once "topo.php";

// Verifica se é para deletar um pedido
// if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delpedido'])) {
//     $id = intval($_POST['delpedido']); // Sanitiza o ID para evitar SQL Injection

//     $stmt = $connect->prepare("DELETE FROM pedidos WHERE id = ?");
//     if ($stmt->execute([$id])) {
//         header("Location: pedidosFiltro.php?ok=1");
//     } else {
//         header("Location: pedidosFiltro.php?erro=1");
//     }
//     exit();
// }

// Filtros
$filtro_data_inicial = isset($_GET['data_inicial']) ? $_GET['data_inicial'] : '';
$filtro_data_final = isset($_GET['data_final']) ? $_GET['data_final'] : '';
$filtro_produto = isset($_GET['produto']) ? $_GET['produto'] : '';
$filtro_tipo_pedido = isset($_GET['tipo_pedido']) ? $_GET['tipo_pedido'] : '';
$filtro_bairro = isset($_GET['bairro']) ? $_GET['bairro'] : '';
$filtro_motoboy = isset($_GET['motoboy']) ? $_GET['motoboy'] : '';
$filtro_usuario = isset($_GET['usuario']) ? $_GET['usuario'] : '';





// ;;;;;;;;;;;;;;; Filtros Dinamicos Logs


// Formatando data inicial ------------------------------
$timestamp_data_inicial = strtotime($filtro_data_inicial);
$filtro_data_inicial_formatada = date("d-m-Y", $timestamp_data_inicial);
// Fechando data inicial ------------------------------

// Formatando data inicial ------------------------------
$timestamp_data_final = strtotime($filtro_data_final);
$filtro_data_final_formatada = date("d-m-Y", $timestamp_data_final);
// Fechando data inicial ------------------------------

$filtros_logs = array(
    "data_inicial" => $filtro_data_inicial_formatada,
    "data_final" => $filtro_data_final_formatada
);

// Exibe os logs de filtros
// var_dump($filtros_logs);

// ;;;;;;;;;;;;;;; Filtros Dinamicos Logs


$query = "SELECT * FROM pedidos WHERE idu = ?";
$params = [$cod_id];


// Pegar data inicial Ex: 17-08-2024 até 18-08-2024
if ($filtro_data_inicial_formatada && $filtro_data_final_formatada) {
    $query .= " AND data BETWEEN ? AND ?";
    $params[] = $filtro_data_inicial_formatada;
    $params[] = $filtro_data_final_formatada;
} elseif ($filtro_data_inicial_formatada) {
    $query .= " AND data >= ?";
    $params[] = $filtro_data_inicial_formatada;
} elseif ($filtro_data_final_formatada) {
    $query .= " AND data <= ?";
    $params[] = $filtro_data_final_formatada;
}

if ($filtro_produto) {
    $query .= " AND obs LIKE ?";
    $params[] = "%" . htmlspecialchars($filtro_produto, ENT_QUOTES, 'UTF-8') . "%";
}

if ($filtro_tipo_pedido) {
    $query .= " AND fpagamento = ?";
    $params[] = $filtro_tipo_pedido;
}

if ($filtro_bairro) {
    $query .= " AND bairro LIKE ?";
    $params[] = "%" . htmlspecialchars($filtro_bairro, ENT_QUOTES, 'UTF-8') . "%";
}

if ($filtro_motoboy) {
    $query .= " AND motoboy = ?";
    $params[] = $filtro_motoboy;
}

if ($filtro_usuario) {
    $query .= " AND atendente = ?";
    $params[] = $filtro_usuario;
}

$query .= " ORDER BY id ASC";
$stmt = $connect->prepare($query);
$stmt->execute($params);
$pedidos = $stmt->fetchAll(PDO::FETCH_OBJ);


// var_dump($pedidos);

// if (empty($pedidos)) {
//     header("Location: pedidosFiltro.php");
//     exit();
// }
?>

<link rel="stylesheet" href="https://unpkg.com/x-data-spreadsheet@1.1.5/dist/xspreadsheet.css">
<script src="https://unpkg.com/x-data-spreadsheet@1.1.5/dist/xspreadsheet.js"></script>



<div class="slim-mainpanel">
    <!-- <div id="xspreadsheet"></div> -->
    <div class="container">
        <?php if (isset($_GET["erro"])) { ?>
            <div class="alert alert-warning" role="alert">
                <i class="fa fa-asterisk" aria-hidden="true"></i> Erro ao deletar pedido.
            </div>
        <?php } ?>
        <?php if (isset($_GET["ok"])) { ?>
            <div class="alert alert-success" role="alert">
                <i class="fa fa-thumbs-o-up" aria-hidden="true"></i> Pedido deletado com sucesso.
            </div>
        <?php } ?>

        <div class="section-wrapper mg-b-20">
            <label class="section-title"><i class="fa fa-list-alt" aria-hidden="true"></i> Filtros</label>
            <hr>
            <form method="get" action="pedidosFiltro.php">
                <div class="form-layout">
                    <div class="row mg-b-25">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Data Inicial: </label>
                                <input type="date" class="form-control" name="data_inicial" value="<?php echo htmlspecialchars($filtro_data_inicial); ?>">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Data Final: </label>
                                <input type="date" class="form-control" name="data_final" value="<?php echo htmlspecialchars($filtro_data_final); ?>">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Produto: </label>
                                <input type="text" class="form-control" name="produto" value="<?php echo htmlspecialchars($filtro_produto); ?>">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Tipo de Pedido: </label>
                                <select class="form-control" name="tipo_pedido">
                                    <option value="">Todos</option>
                                    <option value="DELIVERY" <?php if ($filtro_tipo_pedido == 'DELIVERY') echo 'selected'; ?>>DELIVERY</option>
                                    <option value="MESA" <?php if ($filtro_tipo_pedido == 'MESA') echo 'selected'; ?>>MESA</option>
                                    <!-- Adicione outras opções conforme necessário -->
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Bairro: </label>
                                <input type="text" class="form-control" name="bairro" value="<?php echo htmlspecialchars($filtro_bairro); ?>">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Motoboy: </label>
                                <select class="form-control" name="motoboy">
                                    <option value="">Todos</option>
                                    <!-- Preencher com opções de motoboys do banco -->
                                    <option value="Motoboy 1" <?php if ($filtro_motoboy == 'Motoboy 1') echo 'selected'; ?>>Motoboy 1</option>
                                    <option value="Motoboy 2" <?php if ($filtro_motoboy == 'Motoboy 2') echo 'selected'; ?>>Motoboy 2</option>
                                    <!-- Adicione outras opções conforme necessário -->
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Usuário/Garçom: </label>
                                <select class="form-control" name="usuario">
                                    <option value="">Todos</option>
                                    <!-- Preencher com opções de usuários/garçons do banco -->
                                    <option value="Usuario 1" <?php if ($filtro_usuario == 'Usuario 1') echo 'selected'; ?>>Usuario 1</option>
                                    <option value="Usuario 2" <?php if ($filtro_usuario == 'Usuario 2') echo 'selected'; ?>>Usuario 2</option>
                                    <!-- Adicione outras opções conforme necessário -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-layout-footer" align="center">
                        <button class="btn btn-primary bd-0">Filtrar <i class="fa fa-filter"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <div class="section-wrapper">
            <label class="section-title"><i class="fa fa-list-alt" aria-hidden="true"></i> Lista de Pedidos</label>
            <hr>
            <div class="table-wrapper">
                <table id="datatable1" class="table display responsive nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Tipo de Pagamento</th>
                            <th>Nome</th>
                            <th>Celular</th>
                            <th>Status</th>
                            <th>Valor Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pedido->id); ?></td>
                                <td><?php echo htmlspecialchars($pedido->data); ?></td>
                                <td><?php echo htmlspecialchars($pedido->hora); ?></td>
                                <td><?php echo htmlspecialchars($pedido->fpagamento); ?></td>
                                <td><?php echo htmlspecialchars($pedido->nome); ?></td>
                                <td><?php echo htmlspecialchars($pedido->celular); ?></td>
                                <td><?php echo htmlspecialchars($pedido->status); ?></td>
                                <td><?php echo htmlspecialchars($pedido->vtotal); ?></td>
                                
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    x_spreadsheet('#xspreadsheet');
</script>