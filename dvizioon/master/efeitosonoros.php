<?php
require_once "topo.php";

// Lista de efeitos sonoros padrão para adicionar ao banco de dados
$efeitos_padrao = [
    ["nome" => "Campainha", "caminho" => "../pdv/sounds/campainha.mp3", "padrao" => "h"],
];

foreach ($efeitos_padrao as $efeito) {
    $stmt = $connect->prepare("SELECT COUNT(*) FROM efeitosSonoros WHERE idu = ? AND nome = ?");
    $stmt->execute([$cod_id, $efeito['nome']]);
    $count = $stmt->fetchColumn();
    if ($count == 0) {
        $stmt_insert = $connect->prepare("INSERT INTO efeitosSonoros (idu, nome, caminho, padrao) VALUES (?, ?, ?, ?)");
        $stmt_insert->execute([$cod_id, $efeito['nome'], $efeito['caminho'], $efeito['padrao']]);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Verifica se é para cadastrar um efeito sonoro
    if (isset($_POST['cadefeitosonoro']) && $_POST['cadefeitosonoro'] == 'ok') {
        $nome = $_POST['nome'];
        $padrao = $_POST['padrao'];
        $idu = $cod_id;

        // Diretório onde os arquivos serão armazenados
        $uploadDir = '../pdv/sounds/';

        // Verifica se o diretório existe e tenta criá-lo se não existir
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                die('Erro ao criar o diretório para uploads.');
            }
        }

        // Processa o upload do arquivo
        if (isset($_FILES['caminho']) && $_FILES['caminho']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['caminho']['tmp_name'];
            $fileName = $_FILES['caminho']['name'];
            $fileSize = $_FILES['caminho']['size'];
            $fileType = $_FILES['caminho']['type'];
            $uploadPath = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                $caminho = $uploadPath;
            } else {
                $caminho = ''; // Ou defina um caminho padrão ou uma mensagem de erro
                $uploadError = 'Erro ao mover o arquivo para o diretório.';
            }
        } else {
            $caminho = ''; // Ou defina um caminho padrão ou uma mensagem de erro
            if ($_FILES['caminho']['error'] !== UPLOAD_ERR_OK) {
                $uploadError = 'Erro no upload do arquivo: ' . $_FILES['caminho']['error'];
            }
        }

        // Se o novo efeito sonoro for marcado como padrão, desmarque o padrão atual
        if ($padrao == 'h') {
            // Remove o padrão de todos os outros efeitos sonoros
            $stmt_update = $connect->prepare("UPDATE efeitosSonoros SET padrao = 'd' WHERE idu = ?");
            $stmt_update->execute([$idu]);
        }

        $stmt = $connect->prepare("INSERT INTO efeitosSonoros (idu, nome, caminho, padrao) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$idu, $nome, $caminho, $padrao])) {
            header("Location: ?ok=1");
        } else {
            header("Location: ?erro=1");
        }
        exit();
    }

    // Verifica se é para deletar um efeito sonoro
    if (isset($_POST['delefeitosonoro'])) {
        $id = $_POST['delefeitosonoro'];

        $stmt = $connect->prepare("DELETE FROM efeitosSonoros WHERE id = ?");
        if ($stmt->execute([$id])) {
            header("Location: ?ok=1");
        } else {
            header("Location: ?erro=1");
        }
        exit();
    }

    // Verifica se é para alternar o status de um efeito sonoro
    if (isset($_POST['toggle_padrao'])) {
        $id = $_POST['toggle_padrao'];
        $stmt = $connect->prepare("SELECT padrao FROM efeitosSonoros WHERE id = ?");
        $stmt->execute([$id]);
        $currentPadrao = $stmt->fetchColumn();

        // Alterna entre 'h' e 'd'
        $novoPadrao = ($currentPadrao === 'h') ? 'd' : 'h';

        // Se o novo padrão for 'h', desmarque o padrão de todos os outros efeitos sonoros
        if ($novoPadrao === 'h') {
            $stmt_update = $connect->prepare("UPDATE efeitosSonoros SET padrao = 'd' WHERE idu = ?");
            $stmt_update->execute([$cod_id]);
        }

        $stmt_update = $connect->prepare("UPDATE efeitosSonoros SET padrao = ? WHERE id = ?");
        if ($stmt_update->execute([$novoPadrao, $id])) {
            header("Location: ?ok=1");
        } else {
            header("Location: ?erro=1");
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
        <?php if (isset($uploadError)) { ?>
            <div class="alert alert-warning" role="alert">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $uploadError; ?>
            </div>
        <?php } ?>

        <div class="section-wrapper mg-b-20">
            <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Cadastrar Efeito Sonoro</label>
            <hr>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="cadefeitosonoro" value="ok">
                <div class="form-layout">
                    <div class="row mg-b-25">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Nome do Efeito Sonoro: <span class="tx-danger">*</span></label>
                                <input type="text" class="form-control" name="nome" maxlength="150" required>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Arquivo do Efeito Sonoro: <span class="tx-danger">*</span></label>
                                <input type="file" class="form-control" name="caminho" required>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Padrão: <span class="tx-danger">*</span></label>
                                <select class="form-control" name="padrao" required>
                                    <option value="h">Habilitado</option>
                                    <option value="d">Desabilitado</option>
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

        <div class="section-wrapper">
            <label class="section-title"><i class="fa fa-check-square-o" aria-hidden="true"></i> Lista de Efeitos Sonoros</label>
            <hr>
            <div class="table-wrapper">
                <table id="datatable1" class="table display responsive nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Caminho</th>
                            <th>Padrão</th>
                            <th>Reproduzir</th>
                            <th>Alternar</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $efeitos = $connect->query("SELECT * FROM efeitosSonoros WHERE idu = '$cod_id' ORDER BY id ASC");
                        while ($dadosefeito = $efeitos->fetch(PDO::FETCH_OBJ)) {
                        ?>
                            <tr>
                                <td><?php print $dadosefeito->id; ?></td>
                                <td><?php print $dadosefeito->nome; ?></td>
                                <td><?php print $dadosefeito->caminho; ?></td>
                                <td> <?php echo ($dadosefeito->padrao === 'h') ? 'Desabilitado' : 'Habilitado'; ?></td>
                                <td>
                                    <!-- Player de Áudio -->
                                    <audio controls>
                                        <source src="<?php echo $dadosefeito->caminho; ?>" type="audio/mpeg">
                                        Seu navegador não suporta o elemento de áudio.
                                    </audio>
                                </td>
                                <td>
                                    <!-- Toggle -->
                                    <form action="" method="post" style="display: inline;">
                                        <input type="hidden" name="toggle_padrao" value="<?php print $dadosefeito->id; ?>" />
                                        <button type="submit" class="btn btn-info btn-sm">
                                            <?php echo ($dadosefeito->padrao === 'h') ? 'Desabilitar' : 'Habilitar'; ?>
                                        </button>
                                    </form>
                                </td>
                                <td align="center">
                                    <form action="" method="post">
                                        <input type="hidden" name="delefeitosonoro" value="<?php print $dadosefeito->id; ?>" />
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="icon fa fa-times"></i></button>
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