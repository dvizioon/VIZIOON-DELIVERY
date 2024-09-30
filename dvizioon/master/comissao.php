<?php
require_once "topo.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idu = $cod_id;
    $statuso = isset($_POST['statuso']) ? $_POST['statuso'] : 'desabilitado';

    // Recupera o valor da comissão atual, se já existir
    $stmt = $connect->prepare("SELECT comissao FROM comissao WHERE idu = ?");
    $stmt->execute([$idu]);
    $comissaoAtual = $stmt->fetchColumn();

    // Só define o valor da comissão se o status for "habilitado"
    if ($statuso == 'habilitado' && isset($_POST['comissao'])) {
        $comissao = floatval($_POST['comissao']);
    } else {
        $comissao = $comissaoAtual !== false ? $comissaoAtual : 0; // Mantém o valor atual ou define como 0 se não existir
    }

    // Verifica se já existe um registro para este usuário
    $stmt = $connect->prepare("SELECT id FROM comissao WHERE idu = ?");
    $stmt->execute([$idu]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
        // Atualiza o registro existente
        $stmt = $connect->prepare("UPDATE comissao SET statuso = ?, comissao = ? WHERE idu = ?");
        if ($stmt->execute([$statuso, $comissao, $idu])) {
            header("Location: ?ok=1");
        } else {
            header("Location: ?erro=1");
        }
    } else {
        // Cria um novo registro
        $stmt = $connect->prepare("INSERT INTO comissao (idu, statuso, comissao) VALUES (?, ?, ?)");
        if ($stmt->execute([$idu, $statuso, $comissao])) {
            header("Location: ?ok=1");
        } else {
            header("Location: ?erro=1");
        }
    }

    if (isset($_POST['delete_id'])) {
        $delete_id = intval($_POST['delete_id']);
        $stmt = $connect->prepare("DELETE FROM comissao WHERE id = ?");
        if ($stmt->execute([$delete_id])) {
            header("Location: ?ok=1");
        } else {
            header("Location: ?erro=1");
        }
        exit();
    }
    exit();
}

// Recupera os dados atuais do status e comissão para o usuário
$stmt = $connect->prepare("SELECT * FROM comissao WHERE idu = ?");
$stmt->execute([$cod_id]);
$comissaoData = $stmt->fetch(PDO::FETCH_OBJ);
$statusoAtual = $comissaoData ? $comissaoData->statuso : 'desabilitado';
$comissaoAtual = $comissaoData ? $comissaoData->comissao : 0;
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

        <!-- Caixa de Informação -->
        <div class="alert alert-info" role="alert">
            <i class="fa fa-info-circle" aria-hidden="true"></i> <strong>Informação sobre Comissão:</strong>
            <p>Este campo define a comissão percentual que será aplicada a pedidos realizados. Se o status estiver habilitado, você deve definir um valor percentual para a comissão. Se o status estiver desabilitado, a comissão não será aplicada aos pedidos. Por exemplo, você pode definir uma comissão de 5% para pedidos vendidos, o que significa que 5% do valor de cada pedido será considerado como comissão.</p>
        </div>

        <div class="section-wrapper mg-b-20">
            <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Cadastrar Status e Comissão</label>
            <hr>
            <form action="" method="post">
                <div class="form-layout">
                    <div class="row mg-b-25">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Status da Comissão: <span class="tx-danger">*</span></label>
                                <select class="form-control" name="statuso" required id="statuso">
                                    <option value="habilitado" <?php echo $statusoAtual == 'habilitado' ? 'selected' : ''; ?>>Habilitado</option>
                                    <option value="desabilitado" <?php echo $statusoAtual == 'desabilitado' ? 'selected' : ''; ?>>Desabilitado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3" id="comissaoField" style="<?php echo $statusoAtual == 'habilitado' ? '' : 'display:none;'; ?>">
                            <div class="form-group">
                                <label class="form-control-label">Valor da Comissão (%): <span class="tx-danger">*</span></label>
                                <input type="text" step="0.01" class="form-control dinheiro" name="comissao" id="comissaoInput" value="<?php echo $comissaoAtual; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-layout-footer" align="center">
                        <button class="btn btn-primary bd-0">Salvar <i class="fa fa-arrow-right"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Lista de comissões -->
        <div class="section-wrapper">
            <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Lista de Comissões</label>
            <hr>
            <div class="table-wrapper">
                <table id="datatable1" class="table display responsive nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Status</th>
                            <th>Comissão (%)</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $comissoes = $connect->query("SELECT * FROM comissao WHERE idu = '$cod_id' ORDER BY id ASC");
                        while ($dados = $comissoes->fetch(PDO::FETCH_OBJ)) {
                        ?>
                            <tr id="row-<?php echo $dados->id; ?>">
                                <td><?php print $dados->id; ?></td>
                                <td><?php print $dados->statuso; ?></td>
                                <td><?php print $dados->comissao; ?>%</td>
                                <td>
                                    <form data-id="<?php echo $dados->id; ?>" method="post" action="">
                                        <input type="hidden" name="delete_id" value="<?php echo $dados->id; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Deletar</button>
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
    // Mostrar ou esconder o campo de comissão com base na seleção do status
    $('#statuso').change(function() {
        if ($(this).val() == 'habilitado') {
            $('#comissaoField').show();
            $('#comissaoInput').attr('required', true);
        } else {
            $('#comissaoField').hide();
            $('#comissaoInput').attr('required', false);
        }
    }).trigger('change');
</script>

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
<script src="../js/moeda.js"></script>