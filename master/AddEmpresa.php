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

$conversao = array(
    'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'é' => 'e',
    'ê' => 'e', 'í' => 'i', 'ï' => 'i', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', "ö" => "o",
    'ú' => 'u', 'ü' => 'u', 'ç' => 'c', 'ñ' => 'n', 'Á' => 'A', 'À' => 'A', 'Ã' => 'A',
    'Â' => 'A', 'É' => 'E', 'Ê' => 'E', 'Í' => 'I', 'Ï' => 'I', "Ö" => "O", 'Ó' => 'O',
    'Ô' => 'O', 'Õ' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ç' => 'C', 'N' => 'Ñ'
);


if (isset($_POST['submitURL'])) {

    // verificar se a url passada existe ou é vazia
    if (!$_POST['urlNewEmpresa'] || empty($_POST['urlNewEmpresa'])) {
        return;
    };

    if (
        !$_POST['modelo'] || empty($_POST['modelo'])
    ) {
        return;
    };

    $modelo = 1;

    $urlnewEmpresa = $_POST['urlNewEmpresa'];
    $urlnewEmpresa = str_replace(' ', '', $urlnewEmpresa);
    $urlnewEmpresa = strtr($urlnewEmpresa, $conversao);
    $urlnewEmpresa = strtolower($urlnewEmpresa);
    // echo $urlnewEmpresa;
    // echo $_POST['urlNewEmpresa'];
    $buscaurl   = $connect->query("SELECT id FROM config WHERE url='$urlnewEmpresa'");
    $count_url  = $buscaurl->rowCount();

    $_SESSION['urlnewEmpresa'] = $urlnewEmpresa;

    if ($count_url >= 1) {

        // echo "Sem Disponibilidade";
        $_SESSION["url_indisponivel"] = "URL está indiponivel pois já está em uso " . $urlnewEmpresa;
    } else {
        // echo "Com Disponibilidade";
        $_SESSION["url_disponivel"] = "URL está disponivel para uso " . $urlnewEmpresa;
    }


    // if (isset($_POST["urlNewEmpresa"])) {
    //     $modelo = $_POST['modelo'];
    //     $url = $_POST['url'];
    //     $url = str_replace(' ', '', $url);
    //     $url = strtr($url, $conversao);
    //     $url = strtolower($url);
    //     //$url = "delicius' AND url='kjhkjhkjhkj";

    //     $buscaurl   = $connect->query("SELECT id FROM config WHERE url='$url'");
    //     $count_url  = $buscaurl->rowCount();

    //     if ($count_url >= 1) {

    //         header("location: ./?cod=existente");
    //         exit;
    //     } else {
    //         $_SESSION["modelo"] = $modelo;
    //         $_SESSION["url"] = $url;
    //         header("location: ./cadastrar_empresa");
    //         exit;
    //     }
    // }
}


if (isset($_POST['newUser'])) {
    unset($_SESSION['urlnewEmpresa']);
    unset($_SESSION['card_disponivel']);
    unset($_SESSION['url_indisponivel']);
    unset($_SESSION['url_disponivel']);
    header("location: ./AddEmpresa.php");
}

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

    <style>
        .icon_master {
            display: flex;
            align-items: first baseline;
            width: 100%;
            gap: 0.5rem;
        }
    </style>
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

            <div class="card card-dash-one mg-t-20">
                <div class="row no-gutters">

                    <?php
                    $todia = $connect->query("SELECT id FROM config");
                    ?>
                    <div class="col-lg-3">
                        <i class="icon ion-ios-pie-outline"></i>
                        <div class="dash-content">
                            <label class="tx-success">Empresas Cadastradas</label>
                            <h2><?= $todia   = $todia->rowCount(); ?></h2>
                        </div><!-- dash-content -->
                    </div><!-- col-3 -->

                    <?php
                    $dtat = date("Y-m-d");
                    $todiav = $connect->query("SELECT id FROM config WHERE expiracao > '$dtat' AND status ='1'");
                    ?>
                    <div class="col-lg-3">
                        <i class="icon ion-ios-stopwatch-outline"></i>
                        <div class="dash-content">
                            <label class="tx-purple">Empresas Ativas</label>
                            <h2><?= $todiav   = $todiav->rowCount(); ?></h2>
                        </div><!-- dash-content -->
                    </div><!-- col-3 -->

                    <?php
                    $dtat = date("Y-m-d");
                    $todiav = $connect->query("SELECT id FROM config WHERE expiracao < '$dtat'");
                    ?>
                    <div class="col-lg-3">
                        <i class="icon ion-ios-stopwatch-outline"></i>
                        <div class="dash-content">
                            <label class="tx-warning">Empresas Vencidas</label>
                            <h2><?= $todiav   = $todiav->rowCount(); ?></h2>
                        </div><!-- dash-content -->
                    </div><!-- col-3 -->

                    <?php
                    $todiab = $connect->query("SELECT id FROM config WHERE status='3'");
                    ?>
                    <div class="col-lg-3">
                        <i class="icon ion-ios-analytics-outline"></i>
                        <div class="dash-content">
                            <label class="tx-danger">Empresas Bloqueadas</label>
                            <h2><?= $todiab   = $todiab->rowCount(); ?></h2>
                        </div><!-- dash-content -->
                    </div><!-- col-3 -->

                </div><!-- row -->
            </div><!-- card -->


            <?php

            if (isset($_SESSION['card_disponivel'])) {
            ?>
                <?php

                $url = $_SESSION['urlnewEmpresa'];

                $user  = $connect->query("SELECT * FROM config WHERE url='$url'");
                $userx = $user->fetch(PDO::FETCH_OBJ);

                $confgadm      = $connect->query("SELECT * FROM adm");
                $confgadm      = $confgadm->fetch(PDO::FETCH_OBJ);
                $novocli    = $confgadm->novocliente;

                ?>
                <div class="card mb-3" style="max-width: 100%; margin-top: 1rem;">
                    <h1 class="card-title m-3 text-success">Cliente Cadastrado com Sucesso...</h1>
                    <div class="card-body">
                        <h5 class="card-title">Informações do Cliente</h5>

                        <div class="mb-3">
                            <i class="bi bi-calendar" style="color:#006699;"></i>
                            <strong>Dias:</strong> <?= $confgadm->dias; ?>
                        </div>

                        <?php if ($novocli == 1) : ?>
                            <hr>
                            <div class="mb-3">
                                <i class="bi bi-link" style="color:#006699;"></i>
                                <strong>Sua URL personalizada:</strong><br>
                                <span style="color:#006699"><?php print $urlmaster; ?>/<?php print $url; ?></span>
                            </div>
                            <div class="mb-3">
                                <i class="bi bi-lock" style="color:#006699;"></i>
                                <strong>Painel Administrativo:</strong><br>
                                <span style="color:#006699"><?php print $urlmaster; ?>/<?php print $url; ?>/master</span>
                            </div>
                        <?php endif; ?>

                        <hr>
                        <div class="mb-3">
                            <i class="bi bi-person" style="color:#006699;"></i>
                            <strong>Login:</strong> <span style="color:#006699"><?php print $userx->cpf; ?></span>
                        </div>
                        <div class="mb-3">
                            <i class="bi bi-shield-lock" style="color:#006699;"></i>
                            <strong>Senha:</strong> <span style="color:#006699">****</span>
                        </div>

                        <div class="mt-4">
                            <form action="./AddEmpresa.php" method="POST">
                                <a href="controle.php" class="btn btn-primary me-2">Voltar</a>
                                <button type="submit" name="newUser" class="btn btn-success">Criar Novo Usuário</button>
                            </form>
                        </div>
                    </div>
                </div>

                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">


            <?php
            } else {
            ?>
                <div class="section-wrapper mg-t-20">
                    <!-- <label class="section-title">
                        <a href="configuracoes.php" class="btn btn-success btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> Configurações</a>
                        <a href="usuario.php" class="btn btn-info btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> Dados do Usuários</a>
                        <a href="AddEmpresa.php" class="btn btn-dark btn-sm"><i class="fa fa-briefcase" aria-hidden="true"></i> Adicionar Empresas</a>
                    </label> -->

                    <label class="section-title">
                        <a href="controle.php" class="btn btn-success btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i></a> -
                        Criar Empresas
                    </label>
                    <hr>
                    <hr>

                    <?php
                    if (isset($_SESSION['url_indisponivel'])) :

                    ?>
                        <div id="error-message" class="alert alert-danger" role="alert">
                            <p><?php echo $_SESSION["url_indisponivel"]; ?></p>
                        </div>
                        <script>
                            setTimeout(function() {
                                document.getElementById('error-message').style.display = 'none';
                            }, 2000); // 2000 milissegundos = 2 segundos
                        </script>

                    <?php
                        unset($_SESSION['url_indisponivel']);

                    endif;
                    ?>

                    <div>
                        <p style="font-size:16px;" align="justify">Informe um nome para sua url sem espaços e sem acentos. Não será possível alterar este nome posteriormente.</p>
                        <form method="post" action="AddEmpresa.php">
                            <div class="form-group">
                                <label><i class="fa fa-sign-out" aria-hidden="true" style="color:#0099FF"></i> <?php print $urlmaster; ?>/</label>

                                <input type="text" name="urlNewEmpresa" class="form-control" placeholder="informe aqui o nome da empresa" maxlength="30" required>
                                <p style="font-size:12px"><strong>Ex: reidopastel</strong></p>

                            </div>
                            <input type="hidden" name="modelo" value="1" required>
                            <br>

                            <button type="submit" name="submitURL" class="btn btn-primary btn-block btn-signin" style="margin-top:-20px;">Verificar Disponibilidade</button>
                        </form>
                        <center>
                            <?php if (isset($_GET["cod"])) {
                                echo "<strong style=\"color:#FF0000; font-size:14px; margin-top:-30px;\">Este nome não esta disponível.</strong>";
                            } ?>
                        </center>
                    </div>

                    <?php
                    if (isset($_SESSION['url_disponivel'])) :
                    ?>
                        <div id="success-message" class="alert alert-success" role="alert">
                            <p><?php echo $_SESSION["url_disponivel"]; ?></p>
                        </div>
                        <script>
                            setTimeout(function() {
                                document.getElementById('success-message').style.display = 'none';
                            }, 2000); // 2000 milissegundos = 2 segundos
                        </script>

                        <div>
                            <form method="post" action="">
                                <div>
                                    <div style="display: flex;width:100%;">
                                        <div class="icon_master">
                                            <i class="fa fa-sign-out" aria-hidden="true" style="color:#0099FF"></i>
                                            <p>URL/MASTER</p>
                                        </div>
                                        <h1 style="width: 100%;">Criando Conta ⇢ <?php print $urlnewEmpresa; ?></h1>
                                    </div>
                                </div>
                                <hr>
                                <input type="hidden" name="addEmpresa_urln" value="<?php print $urlnewEmpresa; ?>">
                                <input type="hidden" name="addEmpresa_urlm" value="<?php print $urlmaster; ?>">
                                <input type="hidden" name="addEmpresa_modelon" value="<?php print $modelo; ?>">
                                <div class="row row-xs mg-b-10">
                                    <div class="col-sm"><input type="text" name="addEmpresa_empresa" class="form-control" placeholder="Nome da Empresa" maxlength="60" required>
                                        <label style="font-size:10px">Nome exibido no cardápio.</label>
                                    </div>
                                    <div class="col-sm mg-t-10 mg-sm-t-0"><input type="text" name="addEmpresa_celular" id="phone-number" class="form-control" placeholder="Nº de celular com Whatsapp" required>
                                        <label style="font-size:10px">Número que receberá o pedido.</label>
                                    </div>
                                </div>
                                <div class="row row-xs mg-b-10">
                                    <div class="col-sm"><input type="text" name="addEmpresa_nome" class="form-control" placeholder="Primeiro Nome" maxlength="30" required>
                                        <label style="font-size:10px">Nome para contato.</label>
                                    </div>
                                    <div class="col-sm mg-t-10 mg-sm-t-0"><input type="email" name="addEmpresa_email" class="form-control" placeholder="Seu e-mail" maxlength="60" required>
                                        <label style="font-size:10px">E-mail para receber informações.</label>
                                    </div>
                                </div>
                                <div class="signup-separator" style="margin-top:20px;"><span>Criar Dados de Acesso</span></div>
                                <div class="row row-xs mg-b-10">
                                    <div class="col-sm"><input type="text" name="addEmpresa_cpf" id="cpf" class="form-control" placeholder="CPF ou CNPJ" required>
                                        <label style="font-size:10px">Será utilizado como login.</label>
                                    </div>
                                    <div class="col-sm mg-t-10 mg-sm-t-0"><input type="password" class="form-control" name="addEmpresa_senha" placeholder="Informe uma senha" maxlength="8" required>
                                        <label style="font-size:10px">Máximo de oito caracteres.</label>
                                    </div>
                                </div>
                                <button type="submit" name="submitAddNewEmpresa" class="btn btn-primary btn-block btn-signin">Criar Conta</button>
                            </form>
                        </div>

                    <?php
                        unset($_SESSION['url_disponivel']);
                    endif;
                    ?>
                </div>
            <?php
            }
            ?>



        </div>

        <br>
        <br>

    </div><!-- container -->
    </div><!-- slim-mainpanel -->

    <script src="../basecard/lib/jquery/js/jquery.js"></script>
    <script src="../basecard/lib/datatables/js/jquery.dataTables.js"></script>
    <script src="../basecard/lib/datatables-responsive/js/dataTables.responsive.js"></script>
    <script src="../basecard/lib/select2/js/select2.min.js"></script>

</body>

</html>