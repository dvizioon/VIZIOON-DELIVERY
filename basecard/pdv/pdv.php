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

// Declare variáveis globais para as permissões
global $perm_pdv, $perm_desborad, $perm_balcao, $perm_mesa;

// Nova consulta para obter as permissões do funcionário
$queryPermissoes = $connect->prepare("SELECT perm_pdv, perm_desborad, perm_balcao, perm_mesa FROM funcionarios WHERE idu = :idu AND id = :id");
$queryPermissoes->bindParam(':idu', $cod_id);
$queryPermissoes->bindParam(':id', $_SESSION["id_funcionario"]); // Bind da variável de sessão
$queryPermissoes->execute();

if ($queryPermissoes->rowCount() > 0) {
  $permissoes = $queryPermissoes->fetch(PDO::FETCH_ASSOC);

  // Atribuindo as permissões às variáveis globais com 'Sim' ou 'Nao'
  $perm_pdv = isset($permissoes['perm_pdv']) && $permissoes['perm_pdv'] === 'Sim' ? 'Sim' : 'Nao';
  $perm_desborad = isset($permissoes['perm_desborad']) && $permissoes['perm_desborad'] === 'Sim' ? 'Sim' : 'Nao';
  $perm_balcao = isset($permissoes['perm_balcao']) && $permissoes['perm_balcao'] === 'Sim' ? 'Sim' : 'Nao';
  $perm_mesa = isset($permissoes['perm_mesa']) && $permissoes['perm_mesa'] === 'Sim' ? 'Sim' : 'Nao';
} else {
  // Se não houver permissões, redirecione ou mostre uma mensagem de erro
  header("location: sair.php");
  exit;
}

// Variáveis globais para permissões adicionais
global $permissao_delivery, $permissao_desboard, $permissao_balcao, $permissao_mesa;

$permissao_delivery = $perm_pdv;
$permissao_desboard = $perm_desborad;
$permissao_balcao = $perm_balcao;
$permissao_mesa = $perm_mesa;

// Exibir as permissões
// echo "Permissão Delivery: " . $permissao_delivery . "<br>";
// echo "Permissão Dashboard: " . $permissao_desboard . "<br>";
// echo "Permissão Balcão: " . $permissao_balcao . "<br>";
// echo "Permissão Mesa: " . $permissao_mesa . "<br>";


// Agora você pode usar essas variáveis em todo o seu código



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


      <!-- Se tiver permissão para ver o Dashboard mostrar -->

      <div class="card card-dash-one mg-t-20" style="<?php echo $permissao_desboard == "Sim" ? "border: 1px solid #ced4da" : "border:none !important; " ?>">
        <div class="row no-gutters">

          <?php
          $dia    = date("d-m-Y");
          $todia = $connect->query("SELECT vtotal, SUM(vtotal) AS soma1 FROM pedidos WHERE idu='" . $cod_id . "' AND status='5' AND data = '" . $dia . "'");
          $todia = $todia->fetch(PDO::FETCH_OBJ);
          ?>

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

          <?php
          if ($permissao_desboard == "Sim"):
          ?>
            <div class="col-lg-4">
              <i class="icon ion-ios-pie-outline"></i>
              <div class="dash-content">
                <label class="tx-success">Finalizado em <?= $dia ?></label>
                <h2>R$: <?php echo number_format($todia->soma1, 2, '.', '.'); ?></h2>
              </div><!-- dash-content -->
            </div><!-- col-3 -->

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

          <?php
          endif;
          ?>

        </div><!-- row -->



      </div><!-- card -->


      <?php
      if (isset($_SESSION['ativar_script_audio'])) {
        echo $_SESSION['ativar_script_audio'];
        unset($_SESSION['ativar_script_audio']);
      };
      ?>

      <?php


      $today = date('d-m-Y');

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
      $query_all = $connect->prepare("SELECT fpagamento FROM pedidos WHERE status NOT IN (5, 6) AND data = :today");
      $query_all->bindParam(':today', $today);
      $query_all->execute();
      $all_pedidos = $query_all->fetchAll();

      foreach ($all_pedidos as $pedido) {
        $fpagamento = json_decode($pedido['fpagamento']);
        if (is_array($fpagamento) && isset($fpagamento[0]) && $fpagamento[0] === 'DELIVERY') {
          $total_delivery++;
        }
      }


      $query_mesa = $connect->prepare("SELECT COUNT(*) as total FROM pedidos WHERE status NOT IN (5, 6) AND  fpagamento = 'MESA' AND data = :today");
      $query_mesa->bindParam(':today', $today);
      $query_mesa->execute();
      $total_mesa = $query_mesa->fetch(PDO::FETCH_OBJ)->total;

      $query_balcao = $connect->prepare("SELECT COUNT(*) as total FROM pedidos WHERE status NOT IN (5, 6) AND fpagamento = 'BALCAO' AND data = :today");
      $query_balcao->bindParam(':today', $today);
      $query_balcao->execute();
      $total_balcao = $query_balcao->fetch(PDO::FETCH_OBJ)->total;

      // Contagem de todos os pedidos finalizados (status 5), sem considerar a data
      $query_finalizados = $connect->prepare("SELECT COUNT(*) as total FROM pedidos WHERE status = 5");
      $query_finalizados->execute();
      $total_pedidos_finalizados = $query_finalizados->fetch(PDO::FETCH_OBJ)->total;

      // Contagem de todos os pedidos cancelados (status 6), sem considerar a data
      $query_cancelados = $connect->prepare("SELECT COUNT(*) as total FROM pedidos WHERE status = 6");
      $query_cancelados->execute();
      $total_pedidos_cancelados = $query_cancelados->fetch(PDO::FETCH_OBJ)->total;


      ?>

      <div class="section-wrapper mg-t-20">
        <div style="display:flex;align-items:center;justify-content:space-between;">
          <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> PEDIDOS RECEBIDOS || <a href="pdvpedido.php?idpedido=<?= $id_pedido = rand(100000, 999999); ?>" class="btn btn-success btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> Pedido Manual</a> </label>
          <h3><?php echo isset($_SESSION['nome_funcionario']) ? "Painel Funcionario ⇢ " . $_SESSION['nome_funcionario'] : "" ?></h3>
        </div>
        <hr>

        <?php

        require("./mostrarAlertCozinha.php");

        ?>

        <div class="container mt-5">


          <!-- Nav tabs -->
          <ul class="nav nav-tabs" id="pedidoTabs" role="tablist">

            <!-- se tiver permissão para delivery mostrar -->
            <?php
            if ($permissao_delivery == "Sim"):
            ?>
              <!-- Aba de Pedidos Delivery -->
              <li class="nav-item" role="presentation">
                <a class="nav-link" id="delivery-tab" onclick="activeTab('delivery')" data-toggle="tab" href="#delivery" role="tab" aria-controls="delivery" aria-selected="true">
                  Pedido Delivery (<?= $total_delivery ?>)
                  <?php if ($total_novos_delivery > 0) : ?>
                    <span class="badge bg-warning text-dark"><?= $total_novos_delivery ?></span>
                  <?php endif; ?>
                </a>
              </li>

            <?php
            endif;
            ?>

            <?php
            if ($permissao_mesa == "Sim"):
            ?>
              <!-- Aba de Pedidos de Mesa -->
              <li class="nav-item" role="presentation">
                <a class="nav-link" id="mesa-tab" onclick="activeTab('mesa')" data-toggle="tab" href="#mesa" role="tab" aria-controls="mesa" aria-selected="false">
                  Pedido de Mesa (<?= $total_mesa ?>)
                  <?php if ($total_novos_mesa > 0) : ?>
                    <span class="badge bg-warning text-dark"><?= $total_novos_mesa ?></span>
                  <?php endif; ?>
                </a>
              </li>

            <?php
            endif;
            ?>


            <?php
            if ($permissao_balcao == "Sim"):
            ?>
              <!-- Aba de Pedidos Balcão -->
              <li class="nav-item" role="presentation">
                <a class="nav-link" id="balcao-tab" onclick="activeTab('balcao')" data-toggle="tab" href="#balcao" role="tab" aria-controls="balcao" aria-selected="false">
                  Pedido Balcão (<?= $total_balcao ?>)
                  <?php if ($total_novos_balcao > 0) : ?>
                    <span class="badge bg-warning text-dark"><?= $total_novos_balcao ?></span>
                  <?php endif; ?>
                </a>
              </li>

            <?php
            endif;
            ?>

            <!-- Nova Aba de Pedidos Finalizados -->
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="finalizados-tab" onclick="activeTab('finalizados')" data-toggle="tab" href="#finalizados" role="tab" aria-controls="finalizados" aria-selected="false">
                Pedidos Finalizados <span class="badge badge-success total-finalizados"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-check" viewBox="0 0 16 16">
                    <path d="M11.354 6.354a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0z" />
                    <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                  </svg><?= $total_pedidos_finalizados ?></span> <span class="badge badge-danger total-cancelados"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-x" viewBox="0 0 16 16">
                    <path d="M7.354 5.646a.5.5 0 1 0-.708.708L7.793 7.5 6.646 8.646a.5.5 0 1 0 .708.708L8.5 8.207l1.146 1.147a.5.5 0 0 0 .708-.708L9.207 7.5l1.147-1.146a.5.5 0 0 0-.708-.708L8.5 6.793z" />
                    <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                  </svg><?= $total_pedidos_cancelados ?></span>
              </a>
            </li>

          </ul>
          <!-- Nav tabs -->


          <!-- Tab panes -->
          <?php

          $today = date('d-m-Y');

          // Consulta SQL para obter pedidos de hoje, com status diferente de 5 e 6, ordenados por data
          $pedidoss = $connect->query("
              SELECT * FROM pedidos 
              WHERE idu = '" . $cod_id . "' 
              AND status NOT IN (5, 6) 
              ORDER BY id DESC 
              LIMIT 200
          ");

          $pedidos_mesa = [];
          $pedidos_balcao = [];
          $pedidos_delivery = [];

          // Armazenando pedidos abertos por tipo de pagamento
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

          // Nova consulta para obter pedidos finalizados (status 5) e cancelados (status 6)
          $pedidos_finalizados_query = $connect->query("
              SELECT * FROM pedidos 
              WHERE idu = '" . $cod_id . "' 
              AND status IN (5, 6) 
              ORDER BY id DESC 
              LIMIT 200
          ");

          $pedidos_finalizados = [];

          // Armazenando pedidos finalizados e cancelados
          while ($pedido_finalizado = $pedidos_finalizados_query->fetch(PDO::FETCH_OBJ)) {
            $pedidos_finalizados[] = $pedido_finalizado;
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

            <!-- Conteúdo da aba Pedidos Finalizados -->
            <div class="tab-pane fade" id="finalizados" role="tabpanel" aria-labelledby="finalizados-tab">
              <!-- Conteúdo da aba Pedidos Finalizados -->
              <!-- Filtros de Período -->
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="dateStart" class="form-label">Período Inicial:</label>
                  <input type="date" id="dateStart" class="form-control" placeholder="Data Início">
                </div>
                <div class="col-md-6">
                  <label for="dateEnd" class="form-label">Período Final:</label>
                  <input type="date" id="dateEnd" class="form-control" placeholder="Data Final">
                </div>
              </div>

              <div class="table-wrapper">
                <table id="datatable4" class="table display responsive nowrap" width="100%">
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
                    <?php renderTable($pedidos_finalizados); ?>
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

  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
  <script src="./libs/bootstrap.bundle.min.js"></script>
  <!-- JavaScript para manter a aba ativa após a atualização da página -->
  <!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> -->
  <script src="./libs/jquery.min.js"></script>
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script> -->
  <script src="./libs/bootstrap.min.js"></script>

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


      // $('#datatable4').DataTable({
      //   "order": [
      //     [0, "desc"]
      //   ],
      //   responsive: true,
      //   language: {
      //     searchPlaceholder: 'Buscar...',
      //     sSearch: '',
      //     lengthMenu: '_MENU_ ítens',
      //   }
      // });


      // Select2
      $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity
      });

    });
  </script>

  <!-- <script>
    $(function() {
      'use strict';

      // Inicializa o DataTable
      var table4 = $('#datatable4').DataTable({
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

      // Função para converter data do formato yyyy-mm-dd (input) para d-m-Y
      function formatarData(data) {
        if (data && data.includes('-')) {
          let partes = data.split('-');
          return `${partes[2]}-${partes[1]}-${partes[0]}`; // Converte de yyyy-mm-dd para d-m-Y
        }
        return data; // Retorna a data sem alteração se não estiver no formato esperado
      }

      // Função para comparar as datas
      function compararDatas(dataTabela, dataInicio, dataFim) {
        if (dataInicio && !dataFim) {
          return dataTabela === dataInicio; // Quando só a data inicial é fornecida
        } else if (dataInicio && dataFim) {
          return dataTabela >= dataInicio && dataTabela <= dataFim;
        }
        return true; // Sem datas fornecidas, retorna todos os registros
      }

      // Função para filtrar os dados pelo intervalo de datas ou apenas data inicial
      $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
          if (settings.nTable.id !== 'datatable4') {
            return true; // Aplica o filtro apenas à tabela 4
          }

          var dataTabela = data[2]; // Supondo que a data está na 3ª coluna (índice 2) no formato d-m-Y
          var dataInicio = $('#dateStart').val(); // Data inicial (formato yyyy-mm-dd)
          var dataFim = $('#dateEnd').val(); // Data final (formato yyyy-mm-dd), pode ser opcional

          // Se não houver data na tabela, não aplica filtro
          if (!dataTabela) {
            return true;
          }

          // Converte as datas de início e fim para o formato d-m-Y
          var dataInicioFormatada = dataInicio ? formatarData(dataInicio) : null;
          var dataFimFormatada = dataFim ? formatarData(dataFim) : null;

          // Lógica do filtro: compara diretamente as strings de data no formato d-m-Y
          if (compararDatas(dataTabela, dataInicioFormatada, dataFimFormatada)) {
            return true;
          }
          return false; // Esconde as datas fora do intervalo
        }
      );

      // Função para fazer o POST e recuperar os valores usando $.ajax
      function filtrarPedidos() {
        var dataInicio = $('#dateStart').val(); // Data inicial (formato yyyy-mm-dd)
        var dataFim = $('#dateEnd').val(); // Data final (formato yyyy-mm-dd), pode ser opcional

        var dataInicioFormatada = dataInicio ? formatarData(dataInicio) : null;
        var dataFimFormatada = dataFim ? formatarData(dataFim) : null;

        // Requisição AJAX para filtroPedido.php
        $.ajax({
          url: './relatorios/filtrosPedidos.php', // Caminho para o arquivo PHP
          method: 'POST',
          data: {
            action: 'filtrarPedidos',
            dateStart: dataInicioFormatada, // Passa a data formatada
            dateEnd: dataFimFormatada // Passa a data final formatada (opcional)
          },
          success: function(response) {
            try {
              // Converte a resposta para JSON
              var data = JSON.parse(response);

              // Verifica se os dados retornados são válidos
              if (data.total_finalizados !== undefined && data.total_cancelados !== undefined) {
                // Atualiza os valores na interface
                $('.total-finalizados').html(`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-check" viewBox="0 0 16 16">
                    <path d="M11.354 6.354a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0z" />
                    <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                  </svg>${data.total_finalizados}`); // Atualiza o total de finalizados
                $('.total-cancelados').html(`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-x" viewBox="0 0 16 16">
                    <path d="M7.354 5.646a.5.5 0 1 0-.708.708L7.793 7.5 6.646 8.646a.5.5 0 1 0 .708.708L8.5 8.207l1.146 1.147a.5.5 0 0 0 .708-.708L9.207 7.5l1.147-1.146a.5.5 0 0 0-.708-.708L8.5 6.793z" />
                    <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                  </svg>${data.total_cancelados}`); // Atualiza o total de cancelados
              } else {
                console.error('Os dados retornados não estão completos:', data);
              }

              // console.log('Resposta recebida:', data); // Exibe os dados no console para depuração
            } catch (e) {
              console.error('Erro ao analisar JSON:', e);
              console.log('Resposta recebida:', response);
            }
          },
          error: function(xhr, status, error) {
            console.error('Erro na requisição AJAX:', error);
            console.log('Status:', status);
            console.log('Detalhes:', xhr.responseText);
          }
        });
      }


      // Aplicar o filtro quando o usuário selecionar as datas
      $('#dateStart, #dateEnd').on('change', function() {
        table4.draw(); // Atualiza o DataTable com o filtro de data aplicado
        filtrarPedidos(); // Chama a função para atualizar os valores de finalizados e cancelados
      });

      // Inicializa o Select2 para o seletor de length do DataTable
      $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity
      });

    });
  </script> -->

  <script>
    $(function() {
      'use strict';

      // Inicializa o DataTable
      var table4 = $('#datatable4').DataTable({
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

      // Função para converter data do formato yyyy-mm-dd (input) para d-m-Y
      function formatarData(data) {
        if (data && data.includes('-')) {
          let partes = data.split('-');
          return `${partes[2]}-${partes[1]}-${partes[0]}`; // Converte de yyyy-mm-dd para d-m-Y
        }
        return data; // Retorna a data sem alteração se não estiver no formato esperado
      }

      // Função para comparar as datas
      function compararDatas(dataTabela, dataInicio, dataFim) {
        if (dataInicio && !dataFim) {
          return dataTabela === dataInicio; // Quando só a data inicial é fornecida
        } else if (dataInicio && dataFim) {
          return dataTabela >= dataInicio && dataTabela <= dataFim;
        }
        return true; // Sem datas fornecidas, retorna todos os registros
      }

      // Função para filtrar os dados pelo intervalo de datas ou apenas data inicial
      $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
          if (settings.nTable.id !== 'datatable4') {
            return true; // Aplica o filtro apenas à tabela 4
          }

          var dataTabela = data[2]; // Supondo que a data está na 3ª coluna (índice 2) no formato d-m-Y
          var dataInicio = $('#dateStart').val(); // Data inicial (formato yyyy-mm-dd)
          var dataFim = $('#dateEnd').val(); // Data final (formato yyyy-mm-dd), pode ser opcional

          // Se não houver data na tabela, não aplica filtro
          if (!dataTabela) {
            return true;
          }

          // Converte as datas de início e fim para o formato d-m-Y
          var dataInicioFormatada = dataInicio ? formatarData(dataInicio) : null;
          var dataFimFormatada = dataFim ? formatarData(dataFim) : null;

          // Lógica do filtro: compara diretamente as strings de data no formato d-m-Y
          if (compararDatas(dataTabela, dataInicioFormatada, dataFimFormatada)) {
            return true;
          }
          return false; // Esconde as datas fora do intervalo
        }
      );

      // Função para fazer o POST e recuperar os valores usando $.ajax
      function filtrarPedidos() {
        var dataInicio = $('#dateStart').val(); // Data inicial (formato yyyy-mm-dd)
        var dataFim = $('#dateEnd').val(); // Data final (formato yyyy-mm-dd), pode ser opcional

        var dataInicioFormatada = dataInicio ? formatarData(dataInicio) : null;
        var dataFimFormatada = dataFim ? formatarData(dataFim) : null;

        // Armazena as datas no localStorage
        localStorage.setItem('dataInicio', dataInicio);
        localStorage.setItem('dataFim', dataFim);

        // Requisição AJAX para filtroPedido.php
        $.ajax({
          url: './relatorios/filtrosPedidos.php', // Caminho para o arquivo PHP
          method: 'POST',
          data: {
            action: 'filtrarPedidos',
            dateStart: dataInicioFormatada, // Passa a data formatada
            dateEnd: dataFimFormatada // Passa a data final formatada (opcional)
          },
          success: function(response) {
            try {
              // Converte a resposta para JSON
              var data = JSON.parse(response);

              // Verifica se os dados retornados são válidos
              if (data.total_finalizados !== undefined && data.total_cancelados !== undefined) {
                // Atualiza os valores na interface
                $('.total-finalizados').html(`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-check" viewBox="0 0 16 16">
                <path d="M11.354 6.354a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0z" />
                <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
              </svg>${data.total_finalizados}`); // Atualiza o total de finalizados
                $('.total-cancelados').html(`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-x" viewBox="0 0 16 16">
                <path d="M7.354 5.646a.5.5 0 1 0-.708.708L7.793 7.5 6.646 8.646a.5.5 0 1 0 .708.708L8.5 8.207l1.146 1.147a.5.5 0 0 0 .708-.708L9.207 7.5l1.147-1.146a.5.5 0 0 0-.708-.708L8.5 6.793z" />
                <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
              </svg>${data.total_cancelados}`); // Atualiza o total de cancelados
              } else {
                console.error('Os dados retornados não estão completos:', data);
              }
            } catch (e) {
              console.error('Erro ao analisar JSON:', e);
              console.log('Resposta recebida:', response);
            }
          },
          error: function(xhr, status, error) {
            console.error('Erro na requisição AJAX:', error);
            console.log('Status:', status);
            console.log('Detalhes:', xhr.responseText);
          }
        });
      }

      // Função para carregar os valores do localStorage e aplicar o filtro automaticamente
      function carregarFiltroLocalStorage() {
        var dataInicio = localStorage.getItem('dataInicio');
        var dataFim = localStorage.getItem('dataFim');

        if (dataInicio) {
          $('#dateStart').val(dataInicio);
        }
        if (dataFim) {
          $('#dateEnd').val(dataFim);
        }

        // Se houver dados salvos, aplicar o filtro automaticamente
        if (dataInicio || dataFim) {
          table4.draw();
          filtrarPedidos();
        }
      }

      // Aplicar o filtro quando o usuário selecionar as datas
      $('#dateStart, #dateEnd').on('change', function() {
        table4.draw(); // Atualiza o DataTable com o filtro de data aplicado
        filtrarPedidos(); // Chama a função para atualizar os valores de finalizados e cancelados
      });

      // Carregar o filtro salvo no localStorage quando a página for carregada
      carregarFiltroLocalStorage();

      // Inicializa o Select2 para o seletor de length do DataTable
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