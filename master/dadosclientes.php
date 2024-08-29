<?php
ob_start();
session_start();
if ((!isset($_SESSION['cod_id']) == true)) {
    unset($_SESSION['cod_id']);
    header('location: ./');
}
$cod_id = $_SESSION['cod_id'];
require_once "../funcoes/Conexao.php";
require_once "../funcoes/Key.php";
require_once "./addNewEmpresa.php";


$config = $connect->query("SELECT * FROM adm");
$config = $config->fetch(PDO::FETCH_OBJ);

$codigop = $_POST['codigop'];

// Usando prepared statements para maior segurança
$stmt = $connect->prepare("SELECT * FROM config WHERE id = :codigop");
$stmt->bindParam(':codigop', $codigop, PDO::PARAM_INT);
$stmt->execute();
$cliente = $stmt->fetch(PDO::FETCH_OBJ);

$confgadm = $connect->query("SELECT * FROM adm");
$confgadm = $confgadm->fetch(PDO::FETCH_OBJ);
$novocli  = $confgadm->novocliente;

// var_dump($cliente);

$data_inicial = date("Y-m-d");;
$data_final = $cliente->expiracao;;
$diferenca = strtotime($data_final) - strtotime($data_inicial);
$prazo = floor($diferenca / (60 * 60 * 24));


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="windows-1252">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistema de PDV.">
    <meta name="author" content="MDINELLY">
    <title>PAINEL ADMINISTRATIVO</title>
    <link href="../basecard/lib/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../basecard/lib/Ionicons/css/ionicons.css" rel="stylesheet">
    <link href="../basecard/lib/datatables/css/jquery.dataTables.css" rel="stylesheet">
    <link href="../basecard/lib/select2/css/select2.min.css" rel="stylesheet">
    <link href="../basecard/lib/SpinKit/css/spinkit.css" rel="stylesheet">
    <link rel="stylesheet" href="../basecard/css/slim.css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<body>

    <div class="slim-navbar">
        <div class="container">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="icon ion-ios-home-outline"></i>
                        <span>PAINEL ADMINISTRATIVO</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span>
                            <progress value="0" max="120" id="progressBar"></progress>
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
            <?php if (isset($_GET["ok"])) { ?>
                <div class="alert alert-success mg-t-20" role="alert">
                    <i class="fa fa-thumbs-o-up" aria-hidden="true"></i> Alterado com sucesso.
                </div>
            <?php } ?>

            <div class="card card-dash-one mg-t-20">
                <div class="row no-gutters">

                    <?php
                    $dia      = date("d-m-Y");
                    $todia = $connect->query("SELECT vtotal, SUM(vtotal) AS soma1 FROM pedidos WHERE idu='" . $codigop . "' AND status='5'");
                    $todia = $todia->fetch(PDO::FETCH_OBJ);
                    ?>
                    <div class="col-lg-4">
                        <i class="icon ion-ios-pie-outline"></i>
                        <div class="dash-content">
                            <label class="tx-success">Total de Pedidos Finalizados</label>
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


                    $status1 = $connect->query("SELECT vtotal FROM pedidos WHERE idu='" . $codigop . "' AND status='1'");
                    $status2 = $connect->query("SELECT vtotal FROM pedidos WHERE idu='" . $codigop . "' AND status='2'");
                    $status3 = $connect->query("SELECT vtotal, SUM(vtotal) AS soma8 FROM pedidos WHERE idu='" . $codigop . "' AND status='3'");
                    $status4 = $connect->query("SELECT vtotal, SUM(vtotal) AS soma9 FROM pedidos WHERE idu='" . $codigop . "' AND status='4'");

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
                            <label class="tx-warning">Total de Pedidos na Fila</label>
                            <h2>R$: <?php echo number_format($aguar, 2, ',', '.'); ?></h2>
                        </div><!-- dash-content -->
                    </div><!-- col-3 -->

                    <?php
                    $final = $connect->query("SELECT vtotal, SUM(vtotal) AS soma3 FROM pedidos WHERE idu='" . $codigop . "' AND status='6'");
                    $final = $final->fetch(PDO::FETCH_OBJ);
                    ?>
                    <div class="col-lg-4">
                        <i class="icon ion-ios-analytics-outline"></i>
                        <div class="dash-content">
                            <label class="tx-danger">Total de Pedidos Cancelados</label>
                            <h2>R$: <?php echo number_format($final->soma3, 2, '.', '.'); ?></h2>
                        </div><!-- dash-content -->
                    </div><!-- col-3 -->

                </div><!-- row -->
            </div><!-- card -->



            <?php if (isset($cliente)) : ?>
                <div class="section-wrapper mg-t-20">
                    <div class="mt-4">
                        <!-- <form action="./AddEmpresa.php" method="POST"> -->
                        <a href="controle.php" class="btn btn-primary me-2">Voltar</a>
                        <!-- <button type="submit" name="newUser" class="btn btn-success">Criar Novo Usuário</button>
                        </form> -->
                    </div>
                    <div class="card mb-3" style="max-width: 100%; margin-top: 1rem;">
                        <h1 class="card-title m-3 text-success">Dados Encontrados...</h1>
                        <div class="card-body">
                            <h5 class="card-title">Informações do Cliente</h5>

                            <div class="mb-3">

                                <div class="mb-3">
                                    <div class="form-group">
                                        <?php if ($prazo >= 1) { ?>

                                            <i class="bi bi-calendar" style="color:#006699;"></i>
                                            <strong>Dias:</strong> <?= htmlspecialchars($prazo); ?>
                                        <?php } else { ?>
                                            <strong style="background-color:#FF6600; color:#FFFFFF;padding:1rem;">Conta Expirada</strong>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <?php if ($novocli == 1) : ?>
                                <hr>
                                <div class="mb-3">
                                    <i class="bi bi-link" style="color:#006699;"></i>
                                    <strong>Sua URL personalizada:</strong><br>
                                    <span style="color:#006699"><?= htmlspecialchars($urlmaster); ?>/<?= htmlspecialchars($cliente->url); ?></span>
                                </div>
                                <div class="mb-3">
                                    <i class="bi bi-lock" style="color:#006699;"></i>
                                    <strong>Painel Administrativo:</strong><br>
                                    <span style="color:#006699"><?= htmlspecialchars($urlmaster); ?>/<?= htmlspecialchars($cliente->url); ?>/master</span>
                                </div>
                            <?php endif; ?>

                            <hr>


                            <div class="mb-3">
                                <i class="bi bi-person" style="color:#006699;"></i>
                                <strong>Login:</strong> <span style="color:#006699"><?= htmlspecialchars($cliente->cpf); ?></span>
                            </div>
                            <div class="mb-3">
                                <i class="bi bi-shield-lock" style="color:#006699;"></i>
                                <strong>Senha:</strong> <span style="color:#006699">****</span>
                            </div>

                        </div>
                    </div>

                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
                </div>
            <?php else : ?>
                <div class="mb-3 text-danger">Nenhum cliente encontrado.</div>
            <?php endif; ?>



            <br>
            <br>

        </div><!-- container -->
    </div><!-- slim-mainpanel -->



    <script src="../basecard/lib/jquery/js/jquery.js"></script>
    <script src="../basecard/lib/datatables/js/jquery.dataTables.js"></script>
    <script src="../basecard/lib/datatables-responsive/js/dataTables.responsive.js"></script>
    <script src="../basecard/lib/select2/js/select2.min.js"></script>
    <script>
        var timeleft = 120;
        var downloadTimer = setInterval(function() {
            document.getElementById("progressBar").value = 120 - timeleft;
            timeleft -= 1;
            if (timeleft <= 0) {
                clearInterval(downloadTimer);
            }
        }, 1000);
    </script>

</body>

</html>