<?php

if (isset($_COOKIE['pdvx'])) {
  $cod_id = $_COOKIE['pdvx'];
} else {
  header("location: sair.php");
}

session_start();
date_default_timezone_set('America/Sao_Paulo');
include_once('../../funcoes/Conexao.php');
include_once('../../funcoes/Key.php');


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="windows-1252">

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Sistema de PDV.">
  <meta name="author" content="MDINELLY">
  <title>RECEBIMENTO DE PEDIDOS</title>
  <link href="../lib/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="../lib/Ionicons/css/ionicons.css" rel="stylesheet">
  <link href="../lib/datatables/css/jquery.dataTables.css" rel="stylesheet">
  <link href="../lib/select2/css/select2.min.css" rel="stylesheet">
  <link href="../lib/SpinKit/css/spinkit.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/slim.css">

  <script>
    function activeTab(tabName) {
      // Armazena a aba ativa no localStorage
      localStorage.setItem('activeTab', tabName);
      window.location.reload()
    }

    document.addEventListener("DOMContentLoaded", function() {
      // Se há uma aba ativa no localStorage, selecione-a
      var activeTab = localStorage.getItem('activeTab');
      if (activeTab) {
        var tabElement = document.querySelector('#pedidoTabs a[href="#' + activeTab + '"]');
        if (tabElement) {

          // Ativa a aba
          new bootstrap.Tab(tabElement).show();

          // Ativa o conteúdo da aba
          var tabPane = document.querySelector('#' + activeTab);
          if (tabPane) {
            tabPane.classList.add('show', 'active');
          }
        }
      } else {
        // Se não houver aba armazenada, mostrar a aba padrão (Mesa)
        var defaultTabElement = document.querySelector('#pedidoTabs a[href="#mesa"]');
        if (defaultTabElement) {
          // Ativa a aba padrão
          new bootstrap.Tab(defaultTabElement).show();

          // Ativa o conteúdo da aba padrão
          var defaultTabPane = document.querySelector('#mesa');
          if (defaultTabPane) {
            defaultTabPane.classList.add('show', 'active');
          }
        }
      }

      // Armazena a aba ativa no localStorage ao clicar
      var tabs = document.querySelectorAll('#pedidoTabs a');
      tabs.forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function(e) {

          localStorage.setItem('activeTab', e.target.getAttribute('href').substring(1));
        });
      });
    });
  </script>
</head>

<body>

  <div class="slim-navbar">
    <div class="container">
      <ul class="nav">
        <li class="nav-item">
          <a class="nav-link" href="#">
            <i class="icon ion-ios-home-outline"></i>
            <span>RECEBIMENTO DE PEDIDOS</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <span>
              <progress value="0" max="30" id="progressBar"></progress>
            </span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="sair.php">
            <i class="icon ion-ios-analytics-outline"></i>
            <span>SAIR</span>
          </a>
        </li>
      </ul>
    </div>
  </div>

  <div class="slim-mainpanel">
    <div class="container">

      <?php
      if (isset($_SESSION['updateScreen'])) :
        header("location: pdv.php?ok=");
        unset($_SESSION['updateScreen']);
      endif;
      ?>


      <div class="card card-dash-one mg-t-20">
        <div class="row no-gutters">

          <?php
          $dia    = date("d-m-Y");
          $todia = $connect->query("SELECT vtotal, SUM(vtotal) AS soma1 FROM pedidos WHERE idu='" . $cod_id . "' AND status='5' AND data = '" . $dia . "'");
          $todia = $todia->fetch(PDO::FETCH_OBJ);
          ?>
          <div class="col-lg-4">
            <i class="icon ion-ios-pie-outline"></i>
            <div class="dash-content">
              <label class="tx-success">Finalizado em <?= $dia ?></label>
              <h2>R$: <?php echo number_format($todia->soma1, 2, '.', '.'); ?></h2>
            </div><!-- dash-content -->
          </div><!-- col-3 -->
          <?php
          function formatCurrency($num)
          {
            if (preg_match('/' . "," . '/', $num)) {
              return formatValorMoedaDatabase($num);
            } else {
              $num = formatMoedaBr($num);
              return formatValorMoedaDatabase($num);
            }
          }
          function formatValorMoedaDatabase($num)
          {
            return str_replace(',', '.', preg_replace('#[^\d\,]#is', '', $num));
          }

          function formatMoedaBr($num)
          {
            return number_format($num, 2, ',', '.');
          }


          $status1 = $connect->query("SELECT vtotal FROM pedidos WHERE idu='" . $cod_id . "' AND data = '" . $dia . "' AND status='1'");
          $status2 = $connect->query("SELECT vtotal FROM pedidos WHERE idu='" . $cod_id . "' AND data = '" . $dia . "' AND status='2'");
          $status3 = $connect->query("SELECT vtotal, SUM(vtotal) AS soma8 FROM pedidos WHERE idu='" . $cod_id . "' AND data = '" . $dia . "' AND status='3'");
          $status4 = $connect->query("SELECT vtotal, SUM(vtotal) AS soma9 FROM pedidos WHERE idu='" . $cod_id . "' AND data = '" . $dia . "' AND status='4'");

          $aguar = 0;
          while ($status1x = $status1->fetch(PDO::FETCH_OBJ)) {
            $aguar += formatCurrency($status1x->vtotal);
          }
          while ($status2x = $status2->fetch(PDO::FETCH_OBJ)) {
            $aguar += formatCurrency($status2x->vtotal);
          }
          while ($status3x = $status3->fetch(PDO::FETCH_OBJ)) {
            $aguar += formatCurrency($status3x->vtotal);
          }
          while ($status4x = $status4->fetch(PDO::FETCH_OBJ)) {
            $aguar += formatCurrency($status4x->vtotal);
          }

          ?>
          <div class="col-lg-4">
            <i class="icon ion-ios-stopwatch-outline"></i>
            <div class="dash-content">
              <label class="tx-warning">Pedidos da Fila</label>
              <h2>R$: <?php echo number_format($aguar, 2, ',', '.'); ?></h2>
            </div><!-- dash-content -->
          </div><!-- col-3 -->

          <?php
          $final = $connect->query("SELECT vtotal, SUM(vtotal) AS soma3 FROM pedidos WHERE idu='" . $cod_id . "' AND status='6' AND data = '" . $dia . "'");
          $final = $final->fetch(PDO::FETCH_OBJ);
          ?>
          <div class="col-lg-4">
            <i class="icon ion-ios-analytics-outline"></i>
            <div class="dash-content">
              <label class="tx-danger">Cancelados em <?= $dia ?></label>
              <h2>R$: <?php echo number_format($final->soma3, 2, '.', '.'); ?></h2>
            </div><!-- dash-content -->
          </div><!-- col-3 -->

        </div><!-- row -->
      </div><!-- card -->

      <?php
        if(isset($_SESSION['ativar_script_audio'])){
          echo $_SESSION['ativar_script_audio'];
          unset($_SESSION['ativar_script_audio']);
        };
      ?>

      <?php

      // // Contar pedidos novos (status 1) por tipo
      // $query_novos_delivery = $connect->query("SELECT COUNT(*) as total FROM pedidos WHERE status = 1 AND fpagamento IN ('DINHEIRO', 'CARTAO')");
      // $total_novos_delivery = $query_novos_delivery->fetch(PDO::FETCH_OBJ)->total;

      // $query_novos_mesa = $connect->query("SELECT COUNT(*) as total FROM pedidos WHERE status = 1 AND fpagamento = 'MESA'");
      // $total_novos_mesa = $query_novos_mesa->fetch(PDO::FETCH_OBJ)->total;

      // $query_novos_balcao = $connect->query("SELECT COUNT(*) as total FROM pedidos WHERE status = 1 AND fpagamento = 'BALCAO'");
      // $total_novos_balcao = $query_novos_balcao->fetch(PDO::FETCH_OBJ)->total;

      // // Contar pedidos atendidos (status 2)
      // $query_atendidos = $connect->query("SELECT COUNT(*) as total FROM pedidos WHERE status = 2");
      // $total_atendidos = $query_atendidos->fetch(PDO::FETCH_OBJ)->total;

      // // Contar pedidos por tipo
      // $query_delivery = $connect->query("SELECT COUNT(*) as total FROM pedidos WHERE fpagamento IN ('DINHEIRO', 'CARTAO')");
      // $total_delivery = $query_delivery->fetch(PDO::FETCH_OBJ)->total;

      // $query_mesa = $connect->query("SELECT COUNT(*) as total FROM pedidos WHERE fpagamento = 'MESA'");
      // $total_mesa = $query_mesa->fetch(PDO::FETCH_OBJ)->total;

      // $query_balcao = $connect->query("SELECT COUNT(*) as total FROM pedidos WHERE fpagamento = 'BALCAO'");
      // $total_balcao = $query_balcao->fetch(PDO::FETCH_OBJ)->total;

      $today = date('d-m-Y');

      // Contar pedidos novos (status 1) por tipo
      //$query_novos_delivery = $connect->prepare("SELECT COUNT(*) as total FROM pedidos WHERE status = 1 AND fpagamento IN ('DINHEIRO', 'CARTAO') AND data = :today");
      //$query_novos_delivery->bindParam(':today', $today);
      //$query_novos_delivery->execute();
      //$total_novos_delivery = $query_novos_delivery->fetch(PDO::FETCH_OBJ)->total;

      $query_novos_mesa = $connect->prepare("SELECT COUNT(*) as total FROM pedidos WHERE status = 1 AND fpagamento = 'MESA' AND data = :today");
      $query_novos_mesa->bindParam(':today', $today);
      $query_novos_mesa->execute();
      $total_novos_mesa = $query_novos_mesa->fetch(PDO::FETCH_OBJ)->total;

      $query_novos_balcao = $connect->prepare("SELECT COUNT(*) as total FROM pedidos WHERE status = 1 AND fpagamento = 'BALCAO' AND data = :today");
      $query_novos_balcao->bindParam(':today', $today);
      $query_novos_balcao->execute();
      $total_novos_balcao = $query_novos_balcao->fetch(PDO::FETCH_OBJ)->total;

      // Contar pedidos atendidos (status 2) por tipo
      $query_atendidos = $connect->prepare("SELECT COUNT(*) as total FROM pedidos WHERE status = 2 AND data = :today");
      $query_atendidos->bindParam(':today', $today);
      $query_atendidos->execute();
      $total_atendidos = $query_atendidos->fetch(PDO::FETCH_OBJ)->total;

      // Contar pedidos por tipo
      // $query_delivery = $connect->prepare("SELECT COUNT(*) as total FROM pedidos WHERE fpagamento IN ('DELIVERY') AND data = :today");
      // $query_delivery->bindParam(':today', $today);
      // $query_delivery->execute();
      // $total_delivery = $query_delivery->fetch(PDO::FETCH_OBJ)->total;
      // Contar pedidos apenas para "DELIVERY" na primeira posição do array
      // Contar pedidos apenas para "DELIVERY" na primeira posição do array
      //       $query_delivery = $connect->prepare("
      //     SELECT COUNT(*) as total 
      //     FROM pedidos 
      //     WHERE JSON_UNQUOTE(JSON_EXTRACT(fpagamento, '$[0]')) = 'DELIVERY' 
      //     AND data = :today
      // ");
      //       $query_delivery->bindParam(':today', $today);
      //       $query_delivery->execute();
      //       $total_delivery = $query_delivery->fetch()->total;

      // Contar pedidos apenas para "DELIVERY" na primeira posição do array
      // Contar pedidos apenas para "DELIVERY" na primeira posição do array

      // Contar pedidos apenas para "DELIVERY" na primeira posição do array para status 1 (novos)
      $total_novos_delivery = 0;
      $query_all_novos = $connect->prepare("SELECT fpagamento FROM pedidos WHERE status = 1 AND data = :today");
      $query_all_novos->bindParam(':today', $today);
      $query_all_novos->execute();
      $all_novos_pedidos = $query_all_novos->fetchAll();

      foreach ($all_novos_pedidos as $pedido) {
        $fpagamento = json_decode($pedido['fpagamento']);
        if (is_array($fpagamento) && isset($fpagamento[0]) && $fpagamento[0] === 'DELIVERY') {
          $total_novos_delivery++;
        }
      }

      // Contar pedidos apenas para "DELIVERY" na primeira posição do array para todos os pedidos
      $total_delivery = 0;
      $query_all = $connect->prepare("SELECT fpagamento FROM pedidos WHERE data = :today");
      $query_all->bindParam(':today', $today);
      $query_all->execute();
      $all_pedidos = $query_all->fetchAll();

      foreach ($all_pedidos as $pedido) {
        $fpagamento = json_decode($pedido['fpagamento']);
        if (is_array($fpagamento) && isset($fpagamento[0]) && $fpagamento[0] === 'DELIVERY') {
          $total_delivery++;
        }
      }


      $query_mesa = $connect->prepare("SELECT COUNT(*) as total FROM pedidos WHERE fpagamento = 'MESA' AND data = :today");
      $query_mesa->bindParam(':today', $today);
      $query_mesa->execute();
      $total_mesa = $query_mesa->fetch(PDO::FETCH_OBJ)->total;

      $query_balcao = $connect->prepare("SELECT COUNT(*) as total FROM pedidos WHERE fpagamento = 'BALCAO' AND data = :today");
      $query_balcao->bindParam(':today', $today);
      $query_balcao->execute();
      $total_balcao = $query_balcao->fetch(PDO::FETCH_OBJ)->total;
      ?>

      <div class="section-wrapper mg-t-20">
        <div style="display:flex;align-items:center;justify-content:space-between;">
          <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> PEDIDOS RECEBIDOS || <a href="pdvpedido.php?idpedido=<?= $id_pedido = rand(100000, 999999); ?>" class="btn btn-success btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> Pedido Manual</a> </label>
          <h3>Painel Funcionario ⇢ <?php echo $_SESSION['nome_funcionario']; ?></h3>
        </div>
        <hr>

        <?php

          require("./mostrarAlertCozinha.php");

        ?>

        <div class="container mt-5">
          <!-- Nav tabs -->
          <!-- <ul class="nav nav-tabs" id="pedidoTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <a class="nav-link active" id="delivery-tab" data-bs-toggle="tab" href="#delivery" role="tab" aria-controls="delivery" aria-selected="true">Pedido Delivery</a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="mesa-tab" data-bs-toggle="tab" href="#mesa" role="tab" aria-controls="mesa" aria-selected="false">Pedido de Mesa</a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="balcao-tab" data-bs-toggle="tab" href="#balcao" role="tab" aria-controls="balcao" aria-selected="false">Pedido Balcão</a>
            </li>
          </ul> -->

          <!-- Nav tabs -->
          <!-- <ul class="nav nav-tabs" id="pedidoTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <a class="nav-link " id="delivery-tab" data-bs-toggle="tab" href="#delivery" role="tab" aria-controls="delivery" aria-selected="true">
                Pedido Delivery (<?= $total_delivery ?>)
                <?php if ($total_novos_delivery > 0) : ?>
                  <span class="badge bg-warning text-dark"><?= $total_novos_delivery ?></span>
                <?php endif; ?>
              </a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="mesa-tab " data-bs-toggle="tab" href="#mesa" role="tab" aria-controls="mesa" aria-selected="false">
                Pedido de Mesa (<?= $total_mesa ?>)
                <?php if ($total_novos_mesa > 0) : ?>
                  <span class="badge bg-warning text-dark"><?= $total_novos_mesa ?></span>
                <?php endif; ?>
              </a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="balcao-tab" data-bs-toggle="tab" href="#balcao" role="tab" aria-controls="balcao" aria-selected="false">
                Pedido Balcão (<?= $total_balcao ?>)
                <?php if ($total_novos_balcao > 0) : ?>
                  <span class="badge bg-warning text-dark"><?= $total_novos_balcao ?></span>
                <?php endif; ?>
              </a>
            </li>
          </ul> -->

          <!-- Nav tabs -->
          <ul class="nav nav-tabs" id="pedidoTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="delivery-tab" onclick="activeTab('delivery')" data-toggle="tab" href="#delivery" role="tab" aria-controls="delivery" aria-selected="true">
                Pedido Delivery (<?= $total_delivery ?>)
                <?php if ($total_novos_delivery > 0) : ?>
                  <span class="badge bg-warning text-dark"><?= $total_novos_delivery ?></span>
                <?php endif; ?>
              </a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="mesa-tab" onclick="activeTab('mesa')" data-toggle="tab" href="#mesa" role="tab" aria-controls="mesa" aria-selected="false">
                Pedido de Mesa (<?= $total_mesa ?>)
                <?php if ($total_novos_mesa > 0) : ?>
                  <span class="badge bg-warning text-dark"><?= $total_novos_mesa ?></span>
                <?php endif; ?>
              </a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="balcao-tab" onclick="activeTab('balcao')" data-toggle="tab" href="#balcao" role="tab" aria-controls="balcao" aria-selected="false">
                Pedido Balcão (<?= $total_balcao ?>)
                <?php if ($total_novos_balcao > 0) : ?>
                  <span class="badge bg-warning text-dark"><?= $total_novos_balcao ?></span>
                <?php endif; ?>
              </a>
            </li>
          </ul>


          <!-- Tab panes -->
          <?php

          // echo $today;
          // Conecte-se ao banco de dados e obtenha pedidos
          // $pedidoss = $connect->query("SELECT * FROM pedidos WHERE idu='" . $cod_id . "' ORDER BY id DESC LIMIT 200");
          // $pedidoss = $connect->query("SELECT * FROM pedidos WHERE idu = '" . $cod_id . "' AND DATE(data) ='" . $today . "' ORDER BY id DESC LIMIT 200");

          // $today = date('d-m-Y');
          // $yesterday = date('d-m-Y', strtotime('-1 day'));

          // // Consulta SQL para obter pedidos de hoje e ontem, ordenados por data
          // $pedidoss = $connect->query("
          //             SELECT * FROM pedidos 
          //             WHERE idu = '" . $cod_id . "' 
          //             AND (DATE(data) = '" . $today . "' OR DATE(data) = '" . $yesterday . "') 
          //             ORDER BY DATE(data) DESC, id DESC 
          //             LIMIT 200
          //         ");


          //           $today = date('d-m-Y');
          //           $yesterday = date('d-m-Y', strtotime('-1 day'));

          //           // Consulta SQL para obter pedidos de hoje e ontem, ordenados por data
          //           $pedidoss = $connect->query("
          //     SELECT * FROM pedidos 
          //     WHERE idu = '" . $cod_id . "' 
          //     AND (data = '" . $today . "' OR data = '" . $yesterday . "') 
          //     ORDER BY 
          //         CASE 
          //             WHEN data = '" . $today . "' THEN 1 
          //             WHEN data = '" . $yesterday . "' THEN 2 
          //         END, 
          //         id DESC 
          //     LIMIT 200
          // ");

          $today = date('d-m-Y');

          // Consulta SQL para obter pedidos de hoje, ordenados por data
          $pedidoss = $connect->query("
    SELECT * FROM pedidos 
    WHERE idu = '" . $cod_id . "' 
    AND data = '" . $today . "' 
    ORDER BY id DESC 
    LIMIT 200
");



          $pedidos_mesa = [];
          $pedidos_balcao = [];
          $pedidos_delivery = [];

          while ($pedidossx = $pedidoss->fetch(PDO::FETCH_OBJ)) {
            switch ($pedidossx->fpagamento) {
              case "MESA":
                $pedidos_mesa[] = $pedidossx;
                break;
              case "BALCAO":
                $pedidos_balcao[] = $pedidossx;
                break;
              default:
                $pedidos_delivery[] = $pedidossx;
                break;
            }
          }

          // Inclua a função renderTable e outras funções necessárias
          include 'pedido_row.php';
          ?>

          <div class="tab-content" id="pedidoTabsContent">
            <div class="tab-pane fade" id="delivery" role="tabpanel" aria-labelledby="delivery-tab">
              <!-- Conteúdo da aba Pedido Delivery -->
              <div class="table-wrapper">
                <table id="datatable3" class="table display responsive nowrap" width="100%">
                  <thead>
                    <tr>
                      <th class="d-table-cell">#</th>
                      <th class="d-table-cell">Comanda</th>
                      <th class="d-none d-sm-table-cell">Data</th>
                      <th class="d-none d-sm-table-cell">Tipo</th>
                      <th class="d-none d-sm-table-cell">Cliente</th>
                      <th class="d-table-cell">Mesa</th>
                      <th class="d-none d-sm-table-cell">WhatsApp</th>
                      <th class="d-none d-sm-table-cell">Total</th>
                      <th class="d-table-cell">Status</th>
                      <th class="d-none d-sm-table-cell">Pago</th>
                      <th class=""></th>
                      <th class=""></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php renderTable($pedidos_delivery); ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="tab-pane fade" id="mesa" role="tabpanel" aria-labelledby="mesa-tab">
              <!-- Conteúdo da aba Pedido de Mesa -->
              <div class="table-wrapper">
                <table id="datatable1" class="table display responsive nowrap" width="100%">
                  <thead>
                    <tr>
                      <th class="d-table-cell">#</th>
                      <th class="d-table-cell">Comanda</th>
                      <th class="d-none d-sm-table-cell">Data</th>
                      <th class="d-none d-sm-table-cell">Tipo</th>
                      <th class="d-none d-sm-table-cell">Cliente</th>
                      <th class="d-table-cell">Mesa</th>
                      <th class="d-none d-sm-table-cell">WhatsApp</th>
                      <th class="d-none d-sm-table-cell">Total</th>
                      <th class="d-table-cell">Status</th>
                      <th class="d-none d-sm-table-cell">Pago</th>
                      <th class=""></th>
                      <th class=""></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php renderTable($pedidos_mesa); ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="tab-pane fade" id="balcao" role="tabpanel" aria-labelledby="balcao-tab">
              <!-- Conteúdo da aba Pedido Balcão -->
              <div class="table-wrapper">
                <table id="datatable2" class="table display responsive nowrap" width="100%">
                  <thead>
                    <tr>
                      <th class="d-table-cell">#</th>
                      <th class="d-table-cell">Comanda</th>
                      <th class="d-none d-sm-table-cell">Data</th>
                      <th class="d-none d-sm-table-cell">Tipo</th>
                      <th class="d-none d-sm-table-cell">Cliente</th>
                      <th class="d-table-cell">Mesa</th>
                      <th class="d-none d-sm-table-cell">WhatsApp</th>
                      <th class="d-none d-sm-table-cell">Total</th>
                      <th class="d-table-cell">Status</th>
                      <th class="d-none d-sm-table-cell">Pago</th>
                      <th class=""></th>
                      <th class=""></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php renderTable($pedidos_balcao); ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>

      <br>
      <br>

    </div><!-- container -->
  </div><!-- slim-mainpanel -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- JavaScript para manter a aba ativa após a atualização da página -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>


  <script src="../lib/jquery/js/jquery.js"></script>
  <script src="../lib/datatables/js/jquery.dataTables.js"></script>
  <script src="../lib/datatables-responsive/js/dataTables.responsive.js"></script>
  <script src="../lib/select2/js/select2.min.js"></script>

  <script>
    $(function() {
      'use strict';

      $('#datatable1').DataTable({
        "order": [
          [0, "desc"]
        ],
        responsive: true,
        language: {
          searchPlaceholder: 'Buscar...',
          sSearch: '',
          lengthMenu: '_MENU_ ítens',
        }
      });

      $('#datatable2').DataTable({
        "order": [
          [0, "desc"]
        ],
        responsive: true,
        language: {
          searchPlaceholder: 'Buscar...',
          sSearch: '',
          lengthMenu: '_MENU_ ítens',
        }
      });

      $('#datatable3').DataTable({
        "order": [
          [0, "desc"]
        ],
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

  <script type="text/javascript">
    setTimeout(function() {
      window.location.reload(1);
    }, 30000);
  </script>

  <script>
    var timeleft = 30;
    var downloadTimer = setInterval(function() {
      document.getElementById("progressBar").value = 30 - timeleft;
      timeleft -= 1;
      if (timeleft <= 0) {
        clearInterval(downloadTimer);
      }
    }, 1000);
  </script>

</body>

</html>