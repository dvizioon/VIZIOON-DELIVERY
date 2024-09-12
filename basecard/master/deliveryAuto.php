<?php
require_once "topo.php";

// Verifica se está salvando um novo registro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cadastroDados'])) {
    $idu = $cod_id;
    $nome = $_POST['nome'];
    $bairro = $_POST['bairro'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $complemento = $_POST['complemento'];
    $cep = $_POST['cep'];
    $casa = $_POST['casa'];
    $primeiro_nome = $_POST['primeiro_nome'];

    $sql = "INSERT INTO registroDados (idu, nome, bairro, telefone, endereco, complemento, cep, casa, primeiro_nome)
            VALUES ('$idu', '$nome', '$bairro', '$telefone', '$endereco', '$complemento', '$cep', '$casa', '$primeiro_nome')";

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
?>

<div class="slim-mainpanel">
    <div class="container">

        <?php if (isset($_GET["erro"])) { ?>
            <div class="alert alert-warning" role="alert">
                <i class="fa fa-asterisk" aria-hidden="true"></i> Erro.
            </div>
        <?php } ?>
        <?php if (isset($_GET["ok"])) { ?>
            <div class="alert alert-success" role="alert">
                <i class="fa fa-thumbs-o-up" aria-hidden="true"></i> Sucesso.
            </div>
        <?php } ?>

        <div class="section-wrapper mg-b-20">
            <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Cadastrar Dados</label>
            <hr>
            <form action="" method="post">
                <input type="hidden" name="cadastroDados" value="ok">
                <div class="form-layout">
                    <div class="row mg-b-25">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Nome Completo: <span class="tx-danger">*</span></label>
                                <input type="text" class="form-control" name="nome" required>
                            </div>
                        </div><!-- col-4 -->
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Bairro: <span class="tx-danger">*</span></label>
                                <input type="text" class="form-control" name="bairro" required>
                            </div>
                        </div><!-- col-4 -->
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Telefone: <span class="tx-danger">*</span></label>
                                <input type="text" class="form-control" name="telefone" required>
                            </div>
                        </div><!-- col-4 -->
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Endereço: <span class="tx-danger">*</span></label>
                                <input type="text" class="form-control" name="endereco" required>
                            </div>
                        </div><!-- col-4 -->
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Complemento: <span class="tx-danger">*</span></label>
                                <input type="text" class="form-control" name="complemento" required>
                            </div>
                        </div><!-- col-4 -->
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">CEP: <span class="tx-danger">*</span></label>
                                <input type="text" class="form-control" name="cep" required>
                            </div>
                        </div><!-- col-4 -->
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Casa: <span class="tx-danger">*</span></label>
                                <input type="text" class="form-control" name="casa" required>
                            </div>
                        </div><!-- col-4 -->
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Primeiro Nome: <span class="tx-danger">*</span></label>
                                <input type="text" class="form-control" name="primeiro_nome" required>
                            </div>
                        </div><!-- col-4 -->
                    </div><!-- row -->

                    <div class="form-layout-footer" align="center">
                        <button class="btn btn-primary bd-0">Salvar <i class="fa fa-arrow-right"></i></button>
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
                            <!-- <th>Casa</th> -->
                            <!-- <th>Primeiro Nome</th> -->
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dados = $connect->query("SELECT * FROM registroDados ORDER BY nome ASC");
                        while ($dado = $dados->fetch(PDO::FETCH_OBJ)) {
                        ?>
                            <tr>
                                <td><?php echo $dado->id; ?></td>
                                <td><?php echo $dado->idu; ?></td>
                                <td><?php echo $dado->nome; ?></td>
                                <td><?php echo $dado->bairro; ?></td>
                                <td><?php echo $dado->telefone; ?></td>
                                <td><?php echo $dado->endereco; ?></td>
                                <td><?php echo $dado->cep; ?></td>
                                <!-- <td><?php echo $dado->casa; ?></td> -->
                                <!-- <td><?php echo $dado->primeiro_nome; ?></td> -->
                                <td align="center">
                                    <form action="" method="post">
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

    </div><!-- container -->
</div><!-- slim-mainpanel -->

<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/datatables/js/jquery.dataTables.js"></script>
<script src="../lib/datatables-responsive/js/dataTables.responsive.js"></script>
<script src="../lib/select2/js/select2.min.js"></script>

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
</body>

</html>