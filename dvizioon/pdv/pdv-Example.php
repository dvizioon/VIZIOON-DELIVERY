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


      <div class="section-wrapper mg-t-20">
        <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> PEDIDOS RECEBIDOS || <a href="pdvpedido.php?idpedido=<?= $id_pedido = rand(100000, 999999); ?>" class="btn btn-success btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> Pedido Manual</a> </label>
        <hr>
        <div class="table-wrapper">
          <table id="datatable1" class="table display responsive nowrap" width="100%">
            <thead>
              <tr>
                <th class="d-table-cell">#</th> <!-- Sempre visível -->
                <th class="d-table-cell">Comanda</th> <!-- Sempre visível -->
                <th class="d-none d-sm-table-cell">Data</th>
                <th class="d-none d-sm-table-cell">Tipo</th>
                <th class="d-none d-sm-table-cell">Cliente</th>
                <th class="d-table-cell">Mesa</th> <!-- Sempre visível -->
                <th class="d-none d-sm-table-cell">WhatsApp</th>
                <th class="d-none d-sm-table-cell">Total</th>
                <th class="d-table-cell">Status</th> <!-- Sempre visível -->
                <th class="d-none d-sm-table-cell">Pago</th>
                <th class=""></th>
                <th class=""></th>
              </tr>
            </thead>
            <tbody>

              <?php
              $dia    = date("d-m-Y");
              $pedidoss = $connect->query("SELECT * FROM pedidos WHERE idu='" . $cod_id . "' ORDER BY id DESC LIMIT 200");
              $status_verificar = "";

              while ($pedidossx = $pedidoss->fetch(PDO::FETCH_OBJ)) {
                if ($pedidossx->status == 1) {
                  $status = "<button class=\"btn btn-warning btn-sm\">Novo Pedido</button>";
                  echo "
                      <script>
                      var audio = new Audio('./sounds/old-style.mp3');
                      audio.addEventListener('canplaythrough', function() {
                      audio.play();
                      });
                      </script>
                      ";
                }

                if ($pedidossx->status == 2) {
                  $status_verificar = "Pedido Aceito";
                  $status = "<button class=\"btn btn-info w-100  h-100 btn-sm\">Pedido Aceito</button>";
                }
                if ($pedidossx->status == 3) {
                  $status_verificar = "Saiu para entrega";
                  $status = "<button class=\"btn btn-warning w-100  h-100 btn-sm\">Saiu para entrega</button>";
                }
                if ($pedidossx->status == 4) {
                  $status_verificar = "Disponivel para retirada";
                  $status = "<button class=\"btn btn-purple w-100  h-100 btn-sm\">Disponivel para retirada</button>";
                }
                if ($pedidossx->status == 5) {
                  $status_verificar = "Finalizado";
                  $status = "<button class=\"btn btn-success w-100  h-100 btn-sm\">Finalizado</button>";
                }
                if ($pedidossx->status == 6) {
                  $status_verificar = "Cancelado";
                  $status = "<button class=\"btn btn-danger w-100  h-100 btn-sm\">Cancelado</button>";
                }
                if ($pedidossx->status == 7) {
                  $status_verificar = "Confirmado Cozinha";
                  $status = "<button class=\"btn btn-purple btn-sm\">Confirmado Cozinha</button>";
                }

                if ($pedidossx->fpagamento == "DINHEIRO") {
                  $delivery = "<span style=\"color:#FF0000\">DELIVERY</span>";
                }
                if ($pedidossx->fpagamento == "CARTAO" || $pedidossx->fpagamento == "CARTÃO") {
                  $delivery = "<span style=\"color:#FF0000\">DELIVERY</span>";
                }
                if ($pedidossx->fpagamento == "MESA") {
                  $delivery = "MESA";
                }
                if ($pedidossx->fpagamento == "BALCAO") {
                  $delivery = "BALCÃO";
                }

                // echo $delivery;
              ?>

                <?php
                if (!function_exists('truncarTexto')) {
                  /**
                   * Trunca o texto após o primeiro espaço e adiciona '...' se o texto for maior que o comprimento mínimo.
                   *
                   * @param string $texto O texto a ser truncado.
                   * @param int $tamanho Máximo comprimento do texto antes do truncamento.
                   * @param int $comprimento_minimo Comprimento mínimo para truncar o texto.
                   * @return string Texto truncado com '...' adicionado após o primeiro espaço.
                   */
                  function truncarTexto($texto, $tamanho = 50, $comprimento_minimo = 5)
                  {
                    if (strlen($texto) > $comprimento_minimo) {
                      if (strlen($texto) > $tamanho) {
                        $pos_espaco = strpos($texto, ' ', $tamanho);
                        if ($pos_espaco !== false) {
                          $texto = substr($texto, 0, $pos_espaco) . '...';
                        } else {
                          $texto = substr($texto, 0, $tamanho) . '...';
                        }
                      }
                    }
                    return $texto;
                  }
                }


                $nome = $pedidossx->nome;
                $texto_truncado = truncarTexto($nome, 9, 5);

                $data = $pedidossx->data;
                $hora = $pedidossx->hora;

                $texto_truncado_data = truncarTexto($data, 5, 5);
                $texto_truncado_hora = truncarTexto($hora, 9, 5);

                ?>

                <tr>
                  <!-- d-none d-sm-table-cell
                  d-table-cell -->

                  <td class="d-table-cell d-flex" style="align-items:center;gap:0.5rem"><?php print $pedidossx->id; ?> <i class="fa fa-reorder d-block d-md-none" style="font-size: 1.5rem;" aria-hidden="true"></i>
                  </td> <!-- id do Pedido -->
                  <td class="d-table-cell"><?php print $pedidossx->idpedido; ?></td> <!-- codigo do Pedido -->
                  <td class="d-none d-sm-table-cell" title="<?= $data . ' ' . $hora ?>"><?php print $texto_truncado_data; ?></td> <!-- data e hora do Pedido -->
                  <td class="d-none d-sm-table-cell"><?php print $delivery; ?></td> <!-- tipo do Pedido -->
                  <td class="d-none d-sm-table-cell" title="<?= $nome ?>"><?php print htmlspecialchars($texto_truncado); ?></td> <!-- nome do Pedido -->
                  <!-- Adicionando Mesa -->
                  <?php if ($pedidossx->mesa == 0) { ?> <!-- mesa liberada -->
                    <td class="bg-success text-light text-center d-table-cell" style="font-size: 1.0rem;margin:0 auto;">0</td>
                  <?php } else { ?>
                    <td class="bg-danger text-light text-center d-table-cell" style="font-size: 1.0rem;margin:0 auto;"><?php echo $pedidossx->mesa; ?></td> <!-- mesa oculpada -->
                  <?php } ?>


                  <td class="d-none d-sm-table-cell"><a href="https://api.whatsapp.com/send?phone=55<?= $pedidossx->celular; ?>&text=Olá" target="_blank"><img src="../img/wp.png" style="width:15px" /> <?php print $pedidossx->celular; ?></a></td> <!-- numero de celular do pedido -->
                  <td class="d-none d-sm-table-cell">R$ <?php print formatMoedaBr(formatCurrency($pedidossx->vtotal)); ?></td> <!-- Total do Pedido -->

                  <td class="d-table-cell"><?php print $status; ?></td> <!-- Status do Pedido -->

                  <?php

                  $pegadadosgerais   = $connect->query("SELECT * FROM config WHERE id='$cod_id'");
                  $dadosgerais    = $pegadadosgerais->fetch(PDO::FETCH_OBJ);
                  $nomeempresa     = $dadosgerais->nomeempresa;

                  date_default_timezone_set('' . $dadosgerais->fuso . '');

                  $codigop  = $pedidossx->idpedido;

                  $pedido    = $connect->query("SELECT * FROM pedidos WHERE idpedido='$codigop'");
                  $pedido    = $pedido->fetch(PDO::FETCH_OBJ);
                  $celcli    = $pedido->celular;

                  // var_dump($pedido);

                  $produtoscay   = $connect->query("SELECT * FROM store WHERE idsecao = '$codigop' ORDER BY id DESC");

                  $produtoscaxy   = $connect->query("SELECT * FROM store WHERE idsecao = '$codigop' ORDER BY id DESC");

                  $produtosca   = $connect->query("SELECT * FROM store WHERE idsecao = '$codigop' ORDER BY id DESC");
                  $produtoscx   = $produtosca->rowCount();


                  $item      = $connect->query("SELECT * FROM store WHERE idsecao='$codigop'");
                  $opcionais = $connect->query("SELECT * FROM store_o WHERE ids='$codigop'");

                  // Define a consulta
                  $query = "SELECT * FROM `registrospagamentos` WHERE `idpedido` = :idpedido";

                  // Prepara a consulta
                  $stmt = $connect->prepare($query);
                  $stmt->bindParam(':idpedido', $pedidossx->idpedido, PDO::PARAM_INT);

                  // Executa a consulta
                  $stmt->execute();

                  // Obtém os resultados
                  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                  ?>

                  <?php if ($status_verificar == "Finalizado") { ?>
                    <td class="d-none d-sm-table-cell">

                      <form action="comprovante.php" method="post">
                        <input type="hidden" name="codigop" value="<?php print $pedidossx->idpedido; ?>" />
                        <button style="cursor: pointer;border:none;background:none;" onclick="PrintMe('modal-paid')" class=" text-center"><i class="fa fa-money text-success" style="font-size: 1.5rem;" aria-hidden="true"></i></button>
                      </form>


                    </td>
                  <?php } else { ?>
                    <td class="text-light text-center d-none d-sm-table-cell"><i class="fa fa-money text-danger " style="font-size: 1.5rem;" aria-hidden="true"></i></td>
                    <!-- <td class="text-light text-center d-none d-sm-table-cell"></td> -->
                  <?php } ?>

                  <td align="center">
                    <div class="card p-3 mb-3 d-block d-md-none">
                      <h5 class="card-title">Pedido</h5>
                      <p><strong>ID do Pedido:</strong> <?php print $pedidossx->idpedido; ?></p>
                      <p><strong>Data:</strong> <?php print $data . ' às ' . $hora; ?></p>
                      <p><strong>Tipo:</strong> <?php print $delivery; ?></p>
                      <p><strong>Cliente:</strong> <?php print htmlspecialchars($nome); ?></p>
                      <p><strong>Mesa:</strong>
                        <?php if ($pedidossx->mesa == 0) { ?>
                          <span style="font-size: 1.0rem;">0</span>
                        <?php } else { ?>
                          <span style="font-size: 1.0rem;"><?php echo $pedidossx->mesa; ?></span>
                        <?php } ?>
                      </p>
                      <p><strong>WhatsApp:</strong>
                        <a href="https://api.whatsapp.com/send?phone=55<?= $pedidossx->celular; ?>&text=Olá" target="_blank">
                          <img src="../img/wp.png" style="width:15px" /> <?php print $pedidossx->celular; ?>
                        </a>
                      </p>
                      <p><strong>Total:</strong> R$ <?php print formatMoedaBr(formatCurrency($pedidossx->vtotal)); ?></p>
                      <p style="width: 100%; overflow:hidden;"><strong>Pedigo:</strong> <?php print $status; ?></p>
                    </div>
                    <form action="verpedido.php" method="post">
                      <input type="hidden" name="codigop" value="<?php print $pedidossx->idpedido; ?>" />
                      <button style="cursor: pointer;" type="submit" class="btn btn-purple btn-sm w-100"><i class="fa fa-eye" aria-hidden="true"></i></button>
                    </form>

                    <div class="w-100 p-1"></div>
                  </td>

                  <td>
                    <?php if ($status_verificar == "Finalizado") { ?>

                    <?php } else { ?>

                      <a href="pdvpedidoeditar.php?idpedido=<?php print $pedidossx->idpedido; ?>"><button class="btn btn-warning btn-sm w-100"><i class="fa fa-pencil" aria-hidden="true"></i></button> </a>
                    <?php } ?>
                  </td>

                </tr>
              <?php } ?>

            </tbody>
          </table>
        </div>
      </div>

      <br>
      <br>

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