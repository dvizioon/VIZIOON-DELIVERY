<?php
require_once "topo.php";

// Função para gerar um código de funcionário único
function gerarCodigoFuncionario()
{
    return '#motoboy_' . bin2hex(random_bytes(8)); // Gera um código aleatório
}

// Processa o cadastro ou atualização de motoboy
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_id'])) {
        // Exclusão de motoboy
        $delete_id = intval($_POST['delete_id']);
        $stmt = $connect->prepare("DELETE FROM motoboy WHERE id = ?");
        if ($stmt->execute([$delete_id])) {
            header("Location: ?ok=1");
        } else {
            header("Location: ?erro=1");
        }
        exit();
    } else {
        // Cadastro ou atualização de motoboy
        $idu = $cod_id;
        $nome = $_POST['nome'];
        $codigo_funcionario = gerarCodigoFuncionario();
        $telefone = isset($_POST['telefone']) ? $_POST['telefone'] : '';
        $endereco = isset($_POST['endereco']) ? $_POST['endereco'] : '';
        $veiculo = isset($_POST['veiculo']) ? $_POST['veiculo'] : '';
        $placa_veiculo = isset($_POST['placa_veiculo']) ? $_POST['placa_veiculo'] : '';
        $data_contratacao = $_POST['data_contratacao'];
        $tipo_motoboy = isset($_POST['tipo_motoboy']) ? $_POST['tipo_motoboy'] : 'contratado';

        // Verifica se já existe um motoboy com este nome para o usuário
        $stmt = $connect->prepare("SELECT id FROM motoboy WHERE idu = ? AND nome = ?");
        $stmt->execute([$idu, $nome]);
        $exists = $stmt->fetchColumn();

        if ($exists) {
            // Atualiza o registro existente
            $stmt = $connect->prepare("UPDATE motoboy SET telefone = ?, endereco = ?, veiculo = ?, placa_veiculo = ?, data_contratacao = ?, tipo_motoboy = ? WHERE idu = ? AND nome = ?");
            if ($stmt->execute([$telefone, $endereco, $veiculo, $placa_veiculo, $data_contratacao, $tipo_motoboy, $idu, $nome])) {
                header("Location: ?ok=1");
            } else {
                header("Location: ?erro=1");
            }
        } else {
            // Cria um novo registro
            $stmt = $connect->prepare("INSERT INTO motoboy (idu, nome, codigo_funcionario, telefone, endereco, veiculo, placa_veiculo, data_contratacao, tipo_motoboy) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$idu, $nome, $codigo_funcionario, $telefone, $endereco, $veiculo, $placa_veiculo, $data_contratacao, $tipo_motoboy])) {
                header("Location: ?ok=1");
            } else {
                header("Location: ?erro=1");
            }
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

        <div class="section-wrapper mg-b-20">
            <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Cadastrar Motoboy</label>
            <hr>
            <form action="" method="post">
                <div class="form-layout">
                    <div class="row mg-b-25">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">Nome: <span class="tx-danger">*</span></label>
                                <input type="text" class="form-control" name="nome" required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">Telefone:</label>
                                <input type="text" class="form-control" name="telefone">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">Endereço:</label>
                                <input type="text" class="form-control" name="endereco">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">Veículo:</label>
                                <input type="text" class="form-control" name="veiculo">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">Placa do Veículo:</label>
                                <input type="text" class="form-control" name="placa_veiculo">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">Data de Contratação: <span class="tx-danger">*</span></label>
                                <input type="date" class="form-control" name="data_contratacao" required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">Tipo de Motoboy: <span class="tx-danger">*</span></label>
                                <select class="form-control" name="tipo_motoboy" required>
                                    <option value="avulso">Avulso</option>
                                    <option value="contratado" selected>Contratado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-layout-footer" align="center">
                        <button class="btn btn-primary bd-0">Salvar <i class="fa fa-arrow-right"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Lista de motoboys -->
        <div class="section-wrapper">
            <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Lista de Motoboys</label>
            <hr>
            <div>
                <table id="datatable1" class="table display responsive nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Código do Funcionário</th>
                            <th>Telefone</th>
                            <th>Veículo</th>
                            <th>Placa do Veículo</th>
                            <th style="text-align: center;">Ação</th>

                        </tr>
                    </thead>
                    <tbody class="overflow-hidden">
                        <?php
                        // Limite de caracteres para o nome
                        $maxLength = 5;

                        $motoboys = $connect->query("SELECT * FROM motoboy WHERE idu = '$cod_id' ORDER BY id ASC");
                        while ($dados = $motoboys->fetch(PDO::FETCH_OBJ)) {
                            $nome = strlen($dados->nome) > $maxLength ? substr($dados->nome, 0, $maxLength) . '...' : $dados->nome;
                        ?>
                            <tr id="row-<?php echo $dados->id; ?>">
                                <td><?php print $dados->id; ?></td>
                                <td><?php print $nome; ?></td>
                                <td><?php print $dados->codigo_funcionario; ?></td>
                                <td><?php print $dados->telefone; ?></td>
                                <td><?php print $dados->veiculo; ?></td>
                                <td><?php print $dados->placa_veiculo; ?></td>
                                <td class="d-flex justify-content-center align-items-center" style="gap:0.5rem;">
                                    <form data-id="<?php echo $dados->id; ?>" method="post" action="">
                                        <input type="hidden" name="delete_id" value="<?php echo $dados->id; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Deletar</button>
                                    </form>
                                    <button onclick="showModal(<?php echo $dados->id; ?>)" class="btn btn-info btn-sm">Dados</button>
                                </td>
                            </tr>

                            <!-- Modal -->
                            <div id="modal-<?php echo $dados->id; ?>" class="custom-modal">
                                <div class="modal-content">
                                    <span class="close" onclick="closeModal(<?php echo $dados->id; ?>)">&times;</span>
                                    <h2>Dados do Motoboy</h2>
                                    <p><strong>Nome:</strong> <?php echo $dados->nome; ?></p>
                                    <p><strong>Código do Funcionário:</strong> <?php echo $dados->codigo_funcionario; ?></p>
                                    <p><strong>Telefone:</strong> <?php echo $dados->telefone; ?></p>
                                    <p><strong>Endereço:</strong> <?php echo $dados->endereco; ?></p>
                                    <p><strong>Veículo:</strong> <?php echo $dados->veiculo; ?></p>
                                    <p><strong>Placa do Veículo:</strong> <?php echo $dados->placa_veiculo; ?></p>
                                    <p><strong>Data de Contratação:</strong> <?php echo $dados->data_contratacao; ?></p>
                                    <p><strong>Tipo de Motoboy:</strong> <?php echo $dados->tipo_motoboy; ?></p>
                                </div>
                            </div>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Estilo para o Modal -->
<style>
    /* Modal Container */
    .custom-modal {
        display: none;
        /* Oculto por padrão */
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        align-items: center;
        justify-content: center;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        /* Fundo semitransparente */
    }

    /* Conteúdo do Modal */
    .modal-content {
        background-color: #fefefe;

        /* Centraliza verticalmente */
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        /* Largura do modal */
        max-width: 500px;
        border-radius: 10px;
    }

    /* Botão de Fechar */
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

<!-- Script para controle do Modal -->
<script>
    function showModal(id) {
        document.getElementById('modal-' + id).style.display = "flex";
    }

    function closeModal(id) {
        document.getElementById('modal-' + id).style.display = "none";
    }

    // Fecha o modal ao clicar fora dele
    window.onclick = function(event) {
        const modals = document.getElementsByClassName('custom-modal');
        for (let i = 0; i < modals.length; i++) {
            if (event.target == modals[i]) {
                modals[i].style.display = "none";
            }
        }
    }
</script>

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