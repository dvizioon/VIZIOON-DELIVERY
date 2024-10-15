<?php
require_once "topo.php";

// Verifica se o ID do funcionário foi passado na URL
if (isset($_GET['id'])) {
    $id_funcionario = $_GET['id'];

    // Prepare a consulta para buscar os dados do funcionário
    $stmt = $connect->prepare("SELECT * FROM funcionarios WHERE id = :id"); // Supondo que você tenha uma conexão PDO
    $stmt->execute(['id' => $id_funcionario]);

    // Verifica se o funcionário existe
    if ($stmt->rowCount() > 0) {
        // Obtém os dados do funcionário
        $dadosfunc = $stmt->fetch(PDO::FETCH_OBJ);

        // Atribuindo os valores do banco às variáveis
        $nome = $dadosfunc->nome; // Atribua o valor do nome do banco de dados
        $login = $dadosfunc->login; // Atribua o valor do login do banco de dados
        $acesso = $dadosfunc->acesso; // Atribua o valor do acesso do banco de dados

        // Verificando as permissões
        $perm_pdv_checked = $dadosfunc->perm_pdv ? 'checked' : ''; // Atribua 'checked' se a permissão estiver ativada
        $perm_desborad_checked = $dadosfunc->perm_desborad ? 'checked' : ''; // Atribua 'checked' se a permissão estiver ativada
        $perm_balcao_checked = $dadosfunc->perm_balcao ? 'checked' : ''; // Atribua 'checked' se a permissão estiver ativada
        $perm_mesa_checked = $dadosfunc->perm_mesa ? 'checked' : ''; // Atribua 'checked' se a permissão estiver ativada
    } else {
        // Se o funcionário não for encontrado, redireciona
        header("Location: ./funcionarios.php");
        exit();
    }
} else {
    // Se o ID não estiver definido, você pode redirecionar ou mostrar uma mensagem de erro
    header("Location: ./funcionarios.php");
    exit();
}
?>

<div class="slim-mainpanel">
    <div class="container">
        <div class="section-wrapper mg-b-20">
            <label class="section-title">Editar Funcionário</label>
            <hr>
            <form action="" method="post">
                <input type="hidden" name="atualizarFuncionario">
                <input type="hidden" name="id_funcionario" value="<?php echo $id_funcionario; ?>">
                <div class="form-layout">
                    <div class="row mg-b-25">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-control-label">Nome: <span class="tx-danger">*</span></label>
                                <input type="text" class="form-control" name="cad_nome" maxlength="120" value="<?php echo $nome; ?>" required>
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="form-group">
                                <label class="form-control-label">Login: <span class="tx-danger">*</span></label>
                                <input type="text" class="form-control" name="cad_login" maxlength="120" value="<?php echo $login; ?>" required>
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class="form-group">
                                <label class="form-control-label">Acesso: <span class="tx-danger">*</span></label>
                                <select name="cad_acesso" class="form-control" required>
                                    <option value="">Selecione...</option>
                                    <option value="1" <?php echo $acesso == 1 ? 'selected' : ''; ?>>PDV</option>
                                    <option value="2" <?php echo $acesso == 2 ? 'selected' : ''; ?>>Cozinha</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Adicionando os checkboxes de permissões -->
                    <div class="row mg-b-25">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="form-control-label">Permissões:</label><br>
                                <label class="ckbox">
                                    <input type="checkbox" name="perm_pdv" value="1" <?php echo $perm_pdv_checked; ?>><span>Ativar Delivery</span>
                                </label><br>
                                <label class="ckbox">
                                    <input type="checkbox" name="perm_desborad" value="1" <?php echo $perm_desborad_checked; ?>><span>Ativar Dashboard</span>
                                </label><br>
                                <label class="ckbox">
                                    <input type="checkbox" name="perm_balcao" value="1" <?php echo $perm_balcao_checked; ?>><span>Ativar Balcão</span>
                                </label><br>
                                <label class="ckbox">
                                    <input type="checkbox" name="perm_mesa" value="1" <?php echo $perm_mesa_checked; ?>><span>Ativar Mesa</span>
                                </label>
                            </div>
                        </div>
                    </div>



                    <div class="form-layout-footer" align="center">
                        <button class="btn btn-primary bd-0">Atualizar <i class="fa fa-arrow-right"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div><!-- container -->
</div><!-- slim-mainpanel -->