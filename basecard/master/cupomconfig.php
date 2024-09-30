<?php
require_once "topo.php";

// Função de Excluir
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt_delete = $connect->prepare("DELETE FROM cumpomConfig WHERE id = :id");
    $stmt_delete->bindParam(':id', $delete_id);
    $stmt_delete->execute();
    header("Location: ?ok");
    exit();
}

// Verifica se a solicitação é um POST para salvar as configurações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modulo_status = $_POST['modulo_status'] ?? 'nao'; // Status de ativação do cupom, com valor padrão
    $valor_porcentagem = $_POST['valor_porcentagem'] ?? null; // Valor de porcentagem do cupom
    $quantidade_compras = $_POST['quantidade_compras'] ?? null; // Quantidade de vezes que o cupom pode ser usado
    $padrao = $_POST['padrao'] ?? 'nao'; // Define se o cupom é padrão

    // Cria um array com os dias da semana a partir dos checkboxes
    $dias_da_semana = json_encode([
        'segunda' => isset($_POST['segunda']) ? 'sim' : 'nao',
        'terca' => isset($_POST['terca']) ? 'sim' : 'nao',
        'quarta' => isset($_POST['quarta']) ? 'sim' : 'nao',
        'quinta' => isset($_POST['quinta']) ? 'sim' : 'nao',
        'sexta' => isset($_POST['sexta']) ? 'sim' : 'nao',
        'sabado' => isset($_POST['sabado']) ? 'sim' : 'nao',
        'domingo' => isset($_POST['domingo']) ? 'sim' : 'nao'
    ]);

    // Se o cupom é marcado como "padrão", desativa todos os outros
    if ($padrao == 'sim') {
        $sql_desativar_outros = "UPDATE cumpomConfig SET padrao = 'nao' WHERE idu = :idu";
        $stmt_desativar = $connect->prepare($sql_desativar_outros);
        $stmt_desativar->bindParam(':idu', $cod_id);
        $stmt_desativar->execute();
    }

    // Verifica se estamos editando um cupom existente
    if (isset($_POST['edit_id'])) {
        $sql_update_modulo = "UPDATE cumpomConfig SET 
                              ativo = :modulo_status, 
                              dias_da_semana = :dias_da_semana, 
                              quantidade_compras = :quantidade_compras, 
                              valor_porcentagem = :valor_porcentagem, 
                              padrao = :padrao 
                              WHERE id = :id";
        $stmt_update = $connect->prepare($sql_update_modulo);
        $stmt_update->bindParam(':modulo_status', $modulo_status);
        $stmt_update->bindParam(':dias_da_semana', $dias_da_semana);
        $stmt_update->bindParam(':quantidade_compras', $quantidade_compras);
        $stmt_update->bindParam(':valor_porcentagem', $valor_porcentagem);
        $stmt_update->bindParam(':padrao', $padrao);
        $stmt_update->bindParam(':id', $_POST['edit_id']);
        $stmt_update->execute();
        header("Location: ?ok");
        exit();
    } else {
        // Inserir uma nova configuração
        $sql_insert = "INSERT INTO cumpomConfig (idu, ativo, dias_da_semana, quantidade_compras, valor_porcentagem, padrao) 
                       VALUES (:idu, :modulo_status, :dias_da_semana, :quantidade_compras, :valor_porcentagem, :padrao)";
        $stmt_insert = $connect->prepare($sql_insert);
        $stmt_insert->bindParam(':idu', $cod_id);
        $stmt_insert->bindParam(':modulo_status', $modulo_status);
        $stmt_insert->bindParam(':dias_da_semana', $dias_da_semana);
        $stmt_insert->bindParam(':quantidade_compras', $quantidade_compras);
        $stmt_insert->bindParam(':valor_porcentagem', $valor_porcentagem);
        $stmt_insert->bindParam(':padrao', $padrao);
        $stmt_insert->execute();
        header("Location: ?ok");
        exit();
    }
}

// Verifica se estamos editando um cupom existente
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $sql_edit = "SELECT * FROM cumpomConfig WHERE id = :id LIMIT 1";
    $stmt_edit = $connect->prepare($sql_edit);
    $stmt_edit->bindParam(':id', $edit_id);
    $stmt_edit->execute();
    $edit_data = $stmt_edit->fetch(PDO::FETCH_ASSOC);
    if ($edit_data) {
        $dias_da_semana = json_decode($edit_data['dias_da_semana'], true);
    }
}

// Consulta para exibir a tabela de cupons
$sql_consulta_cupons = "SELECT * FROM cumpomConfig";
$stmt_consulta_cupons = $connect->prepare($sql_consulta_cupons);
$stmt_consulta_cupons->execute();
$cupom_configs = $stmt_consulta_cupons->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração de Cupom</title>
</head>

<body>
    <div class="container mt-5">
        <?php if (isset($_GET["erro"])) { ?>
            <div class="alert alert-warning" role="alert">
                <i class="fa fa-asterisk" aria-hidden="true"></i> Erro ao salvar os dados.
            </div>
        <?php } ?>
        <?php if (isset($_GET["ok"])) { ?>
            <div class="alert alert-success" role="alert">
                <i class="fa fa-thumbs-o-up" aria-hidden="true"></i> Dados salvos com sucesso.
            </div>
        <?php } ?>

        <!-- Formulário para ativar/desativar o cupom e selecionar os dias -->
        <div class="section-wrapper mg-b-20">
            <label class="section-title"><?php echo isset($edit_data) ? 'Editar Cupom' : 'Criar Novo Cupom'; ?></label>
            <p>Este módulo permite ativar ou desativar o cupom de desconto para a sua loja.</p>
            <form action="" method="POST">
                <?php if (isset($edit_data)) { ?>
                    <input type="hidden" name="edit_id" value="<?php echo $edit_data['id']; ?>">
                <?php } ?>
                <div class="form-group">
                    <label for="modulo_status">Status do Cupom:</label>
                    <select name="modulo_status" class="form-control" id="modulo_status">
                        <option value="sim" <?php echo (isset($edit_data) && $edit_data['ativo'] == 'sim') ? 'selected' : ''; ?>>Ativado</option>
                        <option value="nao" <?php echo (isset($edit_data) && $edit_data['ativo'] == 'nao') ? 'selected' : ''; ?>>Desativado</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="padrao">Este é o cupom padrão?</label>
                    <select name="padrao" class="form-control" id="padrao">
                        <option value="sim" <?php echo (isset($edit_data) && $edit_data['padrao'] == 'sim') ? 'selected' : ''; ?>>Sim</option>
                        <option value="nao" <?php echo (isset($edit_data) && $edit_data['padrao'] == 'nao') ? 'selected' : ''; ?>>Não</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Valor de Desconto (%):</label>
                    <input type="number" step="0.01" class="form-control" name="valor_porcentagem" value="<?php echo $edit_data['valor_porcentagem'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Limites de Compras:</label>
                    <input type="number" class="form-control" name="quantidade_compras" value="<?php echo $edit_data['quantidade_compras'] ?? ''; ?>" required>
                </div>

                <!-- Seleção de dias da semana -->
                <div class="form-group">
                    <label>Selecionar Dias da Semana:</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="segunda" <?php echo (isset($dias_da_semana['segunda']) && $dias_da_semana['segunda'] == 'sim') ? 'checked' : ''; ?>>
                        <label class="form-check-label">Segunda-feira</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="terca" <?php echo (isset($dias_da_semana['terca']) && $dias_da_semana['terca'] == 'sim') ? 'checked' : ''; ?>>
                        <label class="form-check-label">Terça-feira</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="quarta" <?php echo (isset($dias_da_semana['quarta']) && $dias_da_semana['quarta'] == 'sim') ? 'checked' : ''; ?>>
                        <label class="form-check-label">Quarta-feira</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="quinta" <?php echo (isset($dias_da_semana['quinta']) && $dias_da_semana['quinta'] == 'sim') ? 'checked' : ''; ?>>
                        <label class="form-check-label">Quinta-feira</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="sexta" <?php echo (isset($dias_da_semana['sexta']) && $dias_da_semana['sexta'] == 'sim') ? 'checked' : ''; ?>>
                        <label class="form-check-label">Sexta-feira</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="sabado" <?php echo (isset($dias_da_semana['sabado']) && $dias_da_semana['sabado'] == 'sim') ? 'checked' : ''; ?>>
                        <label class="form-check-label">Sábado</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="domingo" <?php echo (isset($dias_da_semana['domingo']) && $dias_da_semana['domingo'] == 'sim') ? 'checked' : ''; ?>>
                        <label class="form-check-label">Domingo</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><?php echo isset($edit_data) ? 'Atualizar' : 'Criar'; ?></button>
            </form>
        </div>

        <!-- Tabela de Cupons Configurados -->
        <div class="section-wrapper">
            <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Lista de Cupons Configurados</label>
            <hr>
            <div class="table-wrapper">
                <table id="datatable1" class="table display responsive nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Status</th>
                            <th>Padrão</th>
                            <th>Desconto (%)</th>
                            <th>Limites de Compras</th>
                            <th>Dias da Semana</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cupom_configs as $cupom) {
                            $dias = json_decode($cupom['dias_da_semana'], true);
                        ?>
                            <tr>
                                <td><?php echo $cupom['id']; ?></td>
                                <td><?php echo $cupom['ativo']; ?></td>
                                <td><?php echo $cupom['padrao']; ?></td>
                                <td><?php echo $cupom['valor_porcentagem']; ?></td>
                                <td><?php echo $cupom['quantidade_compras']; ?></td>
                                <td>
                                    <?php
                                    foreach ($dias as $dia => $status) {
                                        echo "<div class='border border-warning rounded'><span style='color:" . ($status == "sim" ? "green" : "red") . ";'>" . ucfirst($dia) . ": </span>" . $status . "</div><br>";
                                    }
                                    ?>
                                </td>
                                <td align="center">
                                    <a href="?edit_id=<?php echo $cupom['id']; ?>" class="btn btn-warning btn-sm"><i class="icon fa fa-pencil"></i></a>
                                    <a href="?delete_id=<?php echo $cupom['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este cupom?')"><i class="icon fa fa-times"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>