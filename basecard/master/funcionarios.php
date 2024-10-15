<?php
require_once "topo.php";
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
      <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Cadastrar Funcionários</label>
      <hr>
      <form action="" method="post">
        <input type="hidden" name="cadfuncionarios" value="ok">
        <div class="form-layout">

          <div class="row mg-b-25">
            <div class="col-lg-6">
              <div class="form-group">
                <label class="form-control-label">Nome: <span class="tx-danger">*</span></label>
                <input type="text" class="form-control" name="cad_nome" maxlength="120" required>
              </div>
            </div>

            <div class="col-lg-2">
              <div class="form-group">
                <label class="form-control-label">Login: <span class="tx-danger">*</span></label>
                <input type="text" class="form-control" name="cad_login" maxlength="120" required>
              </div>
            </div>

            <div class="col-lg-2">
              <div class="form-group">
                <label class="form-control-label">Senha: <span class="tx-danger">*</span></label>
                <input type="password" class="dinheiro form-control" name="cad_senha" required>
              </div>
            </div>

            <div class="col-lg-2">
              <div class="form-group">
                <label class="form-control-label">Acesso: <span class="tx-danger">*</span></label>
                <select name="cad_acesso" class="form-control" required>
                  <option>Selecione...</option>
                  <option value="1">PDV</option>
                  <option value="2">Cozinha</option>
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
                  <input type="checkbox" name="perm_pdv" value="1"><span>Permissão PDV</span>
                </label><br>
                <label class="ckbox">
                  <input type="checkbox" name="perm_desborad" value="1"><span>Ativar Desborad</span>
                </label><br>
                <label class="ckbox">
                  <input type="checkbox" name="perm_balcao" value="1"><span>Ativar Balcão</span>
                </label><br>
                <label class="ckbox">
                  <input type="checkbox" name="perm_mesa" value="1"><span>Ativar Mesa</span>
                </label>
              </div>
            </div>
          </div>

          <div class="form-layout-footer" align="center">
            <button class="btn btn-primary bd-0">Salvar <i class="fa fa-arrow-right"></i></button>
          </div>
        </div>
      </form>
    </div>

    <div class="section-wrapper">
      <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Lista </label>
      <hr>
      <div class="table-wrapper">
        <table id="datatable1" class="table display responsive nowrap" width="100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Nome</th>
              <th>Acesso</th>
              <th>Permissões</th>
              <th></th>
              <th></th>
            </tr>
          </thead>
          <tbody>

            <?php
            while ($dadosfunc = $func->fetch(PDO::FETCH_OBJ)) {
              if ($dadosfunc->acesso == 1) {
                $aces = "PDV";
              }
              if ($dadosfunc->acesso == 2) {
                $aces = "Cozinha";
              }

              // Verificar permissões
              $perm_pdv = $dadosfunc->perm_pdv == 'Sim' ? '<span class="badge badge-success p-2 rounded-50">Delivery</span>' : '<span class="badge badge-danger p-2 rounded-50">Delivery</span>';
              $perm_desborad = $dadosfunc->perm_desborad == 'Sim' ? '<span class="badge badge-success p-2 rounded-50">Dashboard</span>' : '<span class="badge badge-danger p-2 rounded-50">Dashboard</span>';
              $perm_balcao = $dadosfunc->perm_balcao == 'Sim' ? '<span class="badge badge-success p-2 rounded-50">Balcão</span>' : '<span class="badge badge-danger p-2 rounded-50">Balcão</span>';
              $perm_mesa = $dadosfunc->perm_mesa == 'Sim' ? '<span class="badge badge-success p-2 rounded-50">Mesa</span>' : '<span class="badge badge-danger p-2 rounded-50">Mesa</span>';

              // Combinar permissões
              $permissoes = implode(' ', array_filter([$perm_pdv, $perm_desborad, $perm_balcao, $perm_mesa]));
            ?>
              <tr>
                <td><?php print $dadosfunc->id; ?></td>
                <td><?php print $dadosfunc->nome; ?></td>
                <td><?php print $aces; ?></td>
                <td><?php print $permissoes; ?></td>
                <td align="center">
                  <form action="" method="post">
                    <input type="hidden" name="delfun" value="<?php print $dadosfunc->id; ?>" />
                    <button type="submit" class="btn btn-danger btn-sm"><i class="icon fa fa-times"></i></button>
                  </form>
                </td>
                <td>
                  <a href="editarFuncionario.php?id=<?php print $dadosfunc->id; ?>" class="btn btn-info btn-sm">
                    <i class="icon fa fa-pencil"></i>
                  </a>
                </td>

              </tr>
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
</body>

</html>