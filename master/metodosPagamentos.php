<?php
require_once "topo.php";

// Verifica se já existem métodos de pagamento para o id do usuário
$stmt = $connect->prepare("SELECT COUNT(*) FROM metodospagamentos WHERE idu = ?");
$stmt->execute([$cod_id]);
$metodosCount = $stmt->fetchColumn();

if ($metodosCount == 0) {
    $mostrarCardInformativo = true;
} else {
    $mostrarCardInformativo = false;
}

// Cadastrar método de pagamento padrão
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cadmetodopagamento']) && $_POST['cadmetodopagamento'] == 'ok') {
        $metodos_padrao = ["Pix", "Cartão", "Dinheiro"];
        foreach ($metodos_padrao as $metodo) {
            // Verifica se o método já existe antes de inserir
            $stmt_check = $connect->prepare("SELECT COUNT(*) FROM metodospagamentos WHERE idu = ? AND metodopagamento = ?");
            $stmt_check->execute([$cod_id, $metodo]);
            if ($stmt_check->fetchColumn() == 0) {
                $stmt_insert = $connect->prepare("INSERT INTO metodospagamentos (idu, metodopagamento) VALUES (?, ?)");
                $stmt_insert->execute([$cod_id, $metodo]);
            }
        }
        header("Location: ?ok=1");
        exit();
    }

    // Verifica se é para cadastrar um novo método de pagamento
    if (isset($_POST['metodopagamento']) && !empty($_POST['metodopagamento'])) {
        $metodopagamento = $_POST['metodopagamento'];
        $idu = $cod_id;

        $stmt = $connect->prepare("INSERT INTO metodospagamentos (idu, metodopagamento) VALUES (?, ?)");
        if ($stmt->execute([$idu, $metodopagamento])) {
            header("Location: ?ok=1");
        } else {
            header("Location: ?erro=1");
        }
        exit();
    }

    // Verifica se é para deletar um método de pagamento
    if (isset($_POST['delmetodopagamento'])) {
        $id = $_POST['delmetodopagamento'];

        $stmt = $connect->prepare("DELETE FROM metodospagamentos WHERE id = ?");
        if ($stmt->execute([$id])) {
            header("Location: ?ok=1");
        } else {
            header("Location: ?erro=1");
        }
        exit();
    }
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

        <?php if ($mostrarCardInformativo) { ?>
            <div class="alert alert-info" role="alert">
                <i class="fa fa-info-circle" aria-hidden="true"></i> <strong>Nenhum método de pagamento encontrado.</strong>
                <p>Para continuar, você precisa configurar pelo menos um método de pagamento. Clique no botão abaixo para criar os métodos de pagamento padrão.</p>
                <form action="" method="post">
                    <input type="hidden" name="cadmetodopagamento" value="ok">
                    <button class="btn btn-primary bd-0">Criar Métodos Padrão</button>
                </form>
            </div>
        <?php } else { ?>


            <div class="section-wrapper mg-b-20">
                <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Adicionar Método de Pagamento</label>
                <hr>
                <form action="" method="post">
                    <div class="form-layout">
                        <div class="row mg-b-25">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label">Método de Pagamento: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="metodopagamento" maxlength="30" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-layout-footer" align="center">
                            <button class="btn btn-primary bd-0">Adicionar Novo Método <i class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="section-wrapper">
                <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Lista de Métodos de Pagamento</label>
                <hr>
                <div class="table-wrapper">
                    <table id="datatable1" class="table display responsive nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Método de Pagamento</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $metodos = $connect->query("SELECT * FROM metodospagamentos WHERE idu = '$cod_id' ORDER BY id ASC");
                            while ($dadosmetodo = $metodos->fetch(PDO::FETCH_OBJ)) {
                            ?>
                                <tr>
                                    <td><?php print $dadosmetodo->id; ?></td>
                                    <td><?php print $dadosmetodo->metodopagamento; ?></td>
                                    <td align="center">
                                        <form action="" method="post">
                                            <input type="hidden" name="delmetodopagamento" value="<?php print $dadosmetodo->id; ?>" />
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="icon fa fa-times"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
</div>
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

        $('#datatable2').DataTable({
            bLengthChange: false,
            searching: false,
            responsive: true
        });

        // Select2
        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity
        });
    });
</script>
<script src="../js/slim.js"></script>
<?php } ?>
</body>


</html>