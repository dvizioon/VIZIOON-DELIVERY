<?php
$configadmin    = $connect->query("SELECT * FROM adm");
$configadmin     = $configadmin->fetch(PDO::FETCH_OBJ);

if (isset($_GET["up"])) {
    echo unlink("" . $_GET["up"] . "");
}

$empresa         = $connect->query("SELECT * FROM config WHERE url='$xurl'");
$dadosempresa     = $empresa->fetch(PDO::FETCH_OBJ);
$idu             = $dadosempresa->id;
$bloc             = $dadosempresa->status;

if ($bloc == 3) {
    header("location: ../site/indisponivel/");
    exit;
}

if ($configadmin->bloquear == 1) {
    $blocx            = date("Y-m-d");
    if ($blocx > $dadosempresa->expiracao) {
        header("location: ../site/indisponivel/");
        exit;
    }
}

$dataatual      =   date('w');
$horaatual      =   date('H:i:s');

$dom             = $dadosempresa->dom;
$seg             = $dadosempresa->seg;
$ter             = $dadosempresa->ter;
$qua             = $dadosempresa->qua;
$qui             = $dadosempresa->qui;
$sex             = $dadosempresa->sex;
$sab             = $dadosempresa->sab;

$aberto = "";

if ($dataatual == 0) {
    $arrayx = explode(',', $dom);

    if ($horaatual >= $arrayx[0] and $horaatual <= $arrayx[1]) {
        $aberto = "1";
    }
    if ($horaatual >= $arrayx[2] and $horaatual <= $arrayx[3]) {
        $aberto = "1";
    }
}

if ($dataatual == 1) {
    $arrayx = explode(',', $seg);

    if ($horaatual >= $arrayx[0] and $horaatual <= $arrayx[1]) {
        $aberto = "1";
    }
    if ($horaatual >= $arrayx[2] and $horaatual <= $arrayx[3]) {
        $aberto = "1";
    }
}

if ($dataatual == 2) {
    $arrayx = explode(',', $ter);

    if ($horaatual >= $arrayx[0] and $horaatual <= $arrayx[1]) {
        $aberto = "1";
    }
    if ($horaatual >= $arrayx[2] and $horaatual <= $arrayx[3]) {
        $aberto = "1";
    }
}

if ($dataatual == 3) {
    $arrayx = explode(',', $qua);

    if ($horaatual >= $arrayx[0] and $horaatual <= $arrayx[1]) {
        $aberto = "1";
    }
    if ($horaatual >= $arrayx[2] and $horaatual <= $arrayx[3]) {
        $aberto = "1";
    }
}

if ($dataatual == 4) {
    $arrayx = explode(',', $qui);

    if ($horaatual >= $arrayx[0] and $horaatual <= $arrayx[1]) {
        $aberto = "1";
    }
    if ($horaatual >= $arrayx[2] and $horaatual <= $arrayx[3]) {
        $aberto = "1";
    }
}

if ($dataatual == 5) {
    $arrayx = explode(',', $sex);

    if ($horaatual >= $arrayx[0] and $horaatual <= $arrayx[1]) {
        $aberto = "1";
    }
    if ($horaatual >= $arrayx[2] and $horaatual <= $arrayx[3]) {
        $aberto = "1";
    }
}

if ($dataatual == 6) {
    $arrayx = explode(',', $sab);

    if ($horaatual >= $arrayx[0] and $horaatual <= $arrayx[1]) {
        $aberto = "1";
    }
    if ($horaatual >= $arrayx[2] and $horaatual <= $arrayx[3]) {
        $aberto = "1";
    }
}

$banner         = $connect->query("SELECT img FROM banner WHERE idu='$idu' ORDER BY RAND() LIMIT 1;");
$dadosbanner     = $banner->fetch(PDO::FETCH_OBJ);

$logo             = $connect->query("SELECT foto FROM logo WHERE idu='$idu' ORDER BY id DESC LIMIT 1");
$dadoslogo         = $logo->fetch(PDO::FETCH_OBJ);

$fundo             = $connect->query("SELECT * FROM fundotopo WHERE idu='$idu' ORDER BY id DESC LIMIT 1");
$dadosfundo     = $fundo->fetch(PDO::FETCH_OBJ);

$categorias     = $connect->query("SELECT * FROM categorias WHERE idu='$idu' ORDER BY posicao ASC");

$categoriasm     = $connect->query("SELECT * FROM categorias WHERE idu='$idu' ORDER BY posicao ASC");

$categoriasd     = $connect->query("SELECT * FROM categorias WHERE idu='$idu' ORDER BY posicao ASC");

$destaques         = $connect->query("SELECT * FROM produtos WHERE destaques = '1' AND idu='$idu' AND (visivel='G' OR visivel='$dataatual') AND status='1' ORDER BY nome ASC");

$produtosca     = $connect->query("SELECT * FROM store WHERE idsecao = '$id_cliente' AND status='0' AND idu='$idu' ORDER BY id DESC");
$produtoscx     = $produtosca->rowCount();

$produtoscax     = $connect->query("SELECT * FROM store WHERE idsecao = '$id_cliente' AND idu='$idu'");

//

if ($produtoscx > 0) {
    $somando     = $connect->query("SELECT valor, SUM(valor * quantidade) AS soma FROM store WHERE idsecao='" . $id_cliente . "' and status='0' and idu='$idu'");
    $somando     = $somando->fetch(PDO::FETCH_OBJ);
    $somandop     = $connect->query("SELECT quantidade, SUM(quantidade) AS somap FROM store WHERE idsecao='" . $id_cliente . "' and status='0' and idu='$idu'");
    $somandop     = $somandop->fetch(PDO::FETCH_OBJ);
}

//

// if (isset($_GET["apagaritem"])) {
//     $idel = $_GET['apagaritem'];
//     $delitem = $connect->query("DELETE FROM store WHERE idpedido='$idel'");
//     $delopci = $connect->query("DELETE FROM store_o WHERE idp='$idel'");
//     if ($delitem) {
//         header("location: ./&retirado=ok");
//         exit;
//     }
// }

//

// if(isset($_GET["apagaritemp"])){
// $idel = $_GET['apagaritemp'];
// $delitem = $connect->query("DELETE FROM store WHERE idpedido='$idel'");
// $delopci = $connect->query("DELETE FROM store_o WHERE idp='$idel'");
// if ( $delitem ) {
// header("location: ./&retiradop=ok"); 
// exit;
// }
// }

if (
    isset($_GET["apagaritemp"]) && isset($_GET["iditemp"])
) {
    $idpedido = $_GET['apagaritemp'];
    $iditem = $_GET['iditemp'];

    try {
        // Inicia a transação
        $connect->beginTransaction();

        // Consulta para obter o valor da coluna 'referencia' baseado no id
        $pegar_referencia_stmt = $connect->prepare("SELECT referencia FROM store WHERE id = :iditem");
        $pegar_referencia_stmt->bindParam(':iditem', $iditem, PDO::PARAM_INT);
        $pegar_referencia_stmt->execute();

        // Verifica se a consulta retornou algum resultado
        if ($pegar_referencia_stmt->rowCount() > 0) {
            // Obtém o resultado da consulta
            $pegar_referencia_result = $pegar_referencia_stmt->fetch(PDO::FETCH_ASSOC);
            // Obtém o valor da coluna 'referencia'
            $referencia = $pegar_referencia_result['referencia'];

            // Consulta a tabela store_o para verificar se existe algum registro com o id_referencia igual à $referencia
            $verifica_referencia_stmt = $connect->prepare("SELECT * FROM store_o WHERE id_referencia = :referencia");
            $verifica_referencia_stmt->bindParam(
                ':referencia',
                $referencia,
                PDO::PARAM_STR
            );
            $verifica_referencia_stmt->execute();

            // Verifica se a consulta retornou algum resultado
            if ($verifica_referencia_stmt->rowCount() > 0) {
                // Prepara a exclusão das opções específicas na tabela 'store_o'
                $delopci_stmt = $connect->prepare("DELETE FROM store_o WHERE id_referencia = :referencia");
                $delopci_stmt->bindParam(':referencia', $referencia, PDO::PARAM_STR);
                $delopci = $delopci_stmt->execute();

                if (!$delopci) {
                    // Rollback da transação se não conseguir excluir da tabela store_o
                    $connect->rollBack();
                    echo "Erro ao excluir as opções da tabela store_o.";
                    exit;
                }
            }

            // Prepara a exclusão do item específico na tabela 'store'
            $delitem_stmt = $connect->prepare("DELETE FROM store WHERE id = :iditem");
            $delitem_stmt->bindParam(':iditem', $iditem, PDO::PARAM_INT);
            $delitem = $delitem_stmt->execute();

            if ($delitem) {
                // Commit da transação
                $connect->commit();

                // Redireciona para a página de edição do pedido após a exclusão
                header("Location: ./&retiradop=ok");
                exit;
            } else {
                // Rollback da transação se não conseguir excluir da tabela store
                $connect->rollBack();
                echo "Erro ao excluir o item da tabela store.";
            }
        } else {
            // Rollback da transação se a referência não for encontrada na tabela store
            $connect->rollBack();
            echo "Nenhuma referência encontrada para o id especificado na tabela store.";
        }
    } catch (PDOException $e) {
        // Rollback da transação em caso de erro
        $connect->rollBack();
        echo "Erro: " . $e->getMessage();
    }
}

//

if (isset($_POST["balcao"])) {
    header("location: ./");
    exit;
}

//

if (isset($_POST["pedidomesa"])) {
    header("location: ./");
    exit;
}

//

if (isset($_POST["pedidodelivery"])) {
    header("location: ./");
    exit;
}
