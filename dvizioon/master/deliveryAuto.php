<?php
require_once "topo.php";

// Verifica se a solicitação é para ativar/desativar o módulo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modulo_status'])) {
    $modulo_status = $_POST['modulo_status'];

    // Atualiza o status do módulo no banco de dados
    $sql_update_modulo = "UPDATE config SET auto_dados_delivery = :modulo_status WHERE id = :idu";
    $sql_update = $connect->prepare($sql_update_modulo);
    $sql_update->bindParam(':modulo_status', $modulo_status);
    $sql_update->bindParam(':idu', $cod_id);
    $sql_update->execute();
}

// Nome da consulta
$consulta_modulo_auto_dados = "SELECT auto_dados_delivery FROM config WHERE id = :idu";

// Prepara a consulta SQL
$sql_auto_dados = $connect->prepare($consulta_modulo_auto_dados);
$sql_auto_dados->bindParam(':idu', $cod_id);
$sql_auto_dados->execute();
$sql_auto_dados_result = $sql_auto_dados->fetch(PDO::FETCH_ASSOC);

// Verifica se está salvando um novo registro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cadastroDados'])) {

    $idu = $cod_id;
    $nome = $_POST['nome'];
    $bairro = $_POST['bairro'];
    // $telefone = $_POST['telefone'];
    $telefone = preg_replace('/\D/', '', $_POST['telefone']); 
    $endereco = $_POST['endereco'];
    $complemento = $_POST['complemento'];
    $cep = $_POST['cep'];
    $casa = $_POST['casa'];
    $primeiro_nome = $_POST['primeiro_nome'];
    $data_nascimento = $_POST['data_nascimento'];
   

    if (isset($_POST['edit_id'])) {
        // Edita o registro existente
        $edit_id = $_POST['edit_id'];
        $sql = "UPDATE registroDados SET nome = '$nome', bairro = '$bairro', telefone = '$telefone', endereco = '$endereco',
                complemento = '$complemento', cep = '$cep', casa = '$casa', primeiro_nome = '$primeiro_nome', data_nascimento = '$data_nascimento'
                WHERE id = '$edit_id'";
    } else {
        // Insere um novo registro
        $sql = "INSERT INTO registroDados (idu, nome, bairro, telefone, endereco, complemento, cep, casa, primeiro_nome, data_nascimento)
                VALUES ('$idu', '$nome', '$bairro', '$telefone', '$endereco', '$complemento', '$cep', '$casa', '$primeiro_nome', '$data_nascimento')";
    }

    if ($connect->query($sql)) {
        header("Location: ?ok");
    } else {
        header("Location: ?erro");
    }
}

// Verifica se está excluindo um registro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delregistro'])) {
    $delId = $_POST['delregistro'];
    $connect->query("DELETE FROM registroDados WHERE id = '$delId'");
    header("Location: ?ok");
}

// Verifica se está editando um registro
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $edit_query = $connect->query("SELECT * FROM registroDados WHERE id = '$edit_id'");
    $edit_data = $edit_query->fetch(PDO::FETCH_ASSOC);
}
?>



<div class="slim-mainpanel">
    <div class="container">

        <!-- Formulário para ativar/desativar o módulo -->
        <div class="section-wrapper mg-b-20">
            <label class="section-title">Ativar Auto - Preenchimento</label>
            <p>Este módulo permite preencher automaticamente alguns dados com base em informações processadas pelo delivery</p>
            <form method="post" action="">
                <div class="form-group">
                    <select name="modulo_status" class="form-control" id="modulo_status">
                        <option value="sim" <?php echo ($sql_auto_dados_result['auto_dados_delivery'] == 'sim') ? 'selected' : ''; ?>>Ativado</option>
                        <option value="nao" <?php echo ($sql_auto_dados_result['auto_dados_delivery'] == 'nao') ? 'selected' : ''; ?>>Desativado</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </form>
        </div>

        <!-- Verifica se o módulo está ativo -->
        <?php if (isset($sql_auto_dados_result['auto_dados_delivery']) && $sql_auto_dados_result['auto_dados_delivery'] == "sim"): ?>

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

            <div class="section-wrapper mg-b-20">
                <label class="section-title"><?php echo isset($edit_data) ? 'Editar Dados Cliente' : 'Cadastrar Dados Cliente'; ?></label>
                <hr>
                <form action="" method="post">
                    <input type="hidden" name="cadastroDados" value="ok">
                    <?php if (isset($edit_data)) { ?>
                        <input type="hidden" name="edit_id" value="<?php echo $edit_data['id']; ?>">
                    <?php } ?>
                    <div class="form-layout">
                        <div class="row mg-b-25">
                            <!-- Formulário de cadastro de dados -->
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Nome Completo: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="nome" value="<?php echo $edit_data['nome'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Bairro: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="bairro" value="<?php echo $edit_data['bairro'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Telefone: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo $edit_data['telefone'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Endereço: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="endereco" value="<?php echo $edit_data['endereco'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Complemento: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="complemento" value="<?php echo $edit_data['complemento'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">CEP: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="cep" value="<?php echo $edit_data['cep'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Casa: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="casa" value="<?php echo $edit_data['casa'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Primeiro Nome: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="primeiro_nome" value="<?php echo $edit_data['primeiro_nome'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Data de Nascimento: <span class="tx-danger">*</span></label>
                                    <input type="date" class="form-control" name="data_nascimento" value="<?php echo $edit_data['data_nascimento'] ?? ''; ?>" required>
                                </div>
                            </div>
                        </div><!-- row -->

                        <div class="form-layout-footer" align="center">
                            <button class="btn btn-primary bd-0"><?php echo isset($edit_data) ? 'Atualizar' : 'Salvar'; ?> <i class="fa fa-arrow-right"></i></button>
                        </div><!-- form-layout-footer -->
                    </div><!-- form-layout -->
                </form>
            </div><!-- section-wrapper -->

            <div class="section-wrapper">
                <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Lista de Registros</label>
                <hr>
                <div class="table-wrapper">
                    <table id="datatable1" class="table display responsive nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID Usuário</th>
                                <th>Nome</th>
                                <th>Bairro</th>
                                <th>Telefone</th>
                                <th>Endereço</th>
                                <th>CEP</th>
                                <th>Data de Nascimento</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $dados = $connect->query("SELECT * FROM registroDados ORDER BY nome ASC");
                            while ($dado = $dados->fetch(PDO::FETCH_OBJ)) {

                                $date = new DateTimeImmutable($dado->data_nascimento);
                            ?>
                                <tr>

                                    <td><?php echo $dado->id; ?></td>
                                    <td><?php echo $dado->idu; ?></td>
                                    <td><?php echo $dado->nome; ?></td>
                                    <td><?php echo $dado->bairro; ?></td>
                                    <td><?php echo $dado->telefone; ?></td>
                                    <td><?php echo $dado->endereco; ?></td>
                                    <td><?php echo $dado->cep; ?></td>
                                    <td><?php echo $date->format("d/m/Y"); ?></td>
                                    <td align="center">
                                        <a href="?edit_id=<?php echo $dado->id; ?>" class="btn btn-warning btn-sm"><i class="icon fa fa-pencil"></i></a>
                                        <form action="" method="post" style="display:inline-block;">
                                            <input type="hidden" name="delregistro" value="<?php echo $dado->id; ?>" />
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="icon fa fa-times"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-info">
                <i class="fa fa-info-circle" aria-hidden="true"></i> O módulo de auto-preenchimento está desativado. Ative-o para poder cadastrar ou editar registros automaticamente.
            </div>
        <?php endif; ?>

    </div><!-- container -->
</div><!-- slim-mainpanel -->
<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../lib/datatables/js/jquery.dataTables.js"></script>
<script src="../lib/datatables-responsive/js/dataTables.responsive.js"></script>
<script src="./scorpe/jquery.mask.min.js"></script>
<script>
    $(function() {
        'use strict';

        $('#datatable1').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: 'Buscar...',
                sSearch: '',
                lengthMenu: '_MENU_ ítens',
            }
        });

        // Select2
        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity
        });

    });
</script>
<script src="../js/slim.js"></script>


<script>
    $(function() {
        // Aplica a máscara no campo de telefone
        $('#telefone').mask('(00) 00000-0000', {
            placeholder: "(99) 99999-9999"
        });
    });
</script>



</body>

</html>