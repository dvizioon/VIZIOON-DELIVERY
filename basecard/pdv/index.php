<?php
session_start();
require_once "../../funcoes/Conexao.php";
require_once "../../funcoes/Key.php";
require_once "../db/base.php";
$site = HOME;

if (isset($_POST["loginCPF"])) {
	$login_cpf = $_POST['loginCPF'];
	$login_sn1 = $_POST['loginSENHA'];
	$login_snh = sha1($login_sn1);

	// Consulta para verificar login e senha, e buscar o nome do usuário
	$buscauser = $connect->query("SELECT id,idu, nome FROM funcionarios WHERE login='$login_cpf' AND senha='$login_snh' AND acesso='1'");
	$count_user = $buscauser->rowCount();
	if ($count_user <= 0) {
		header("location: ./?erro=login");
		exit;
	}

	$dadoscliente = $buscauser->fetch(PDO::FETCH_OBJ);
	$comparaid = $dadoscliente->idu;

	// Consulta para verificar a URL
	$buscauserx = $connect->query("SELECT url FROM config WHERE id='$comparaid'");
	$dadosurl = $buscauserx->fetch(PDO::FETCH_OBJ);
	$comparaid2 = $dadosurl->url;

	$tagsArray = explode('/', $site);
	$termo = $comparaid2;

	$count = 0;
	foreach ($tagsArray as $tag) {
		if ($tag == $termo) {
			$count++;
		}
	}

	if ($count == 0) {
		header("location: ./?erro=login");
		exit;
	}

	if ($count_user >= 1) {
		// Armazena o id e o nome do usuário na sessão
		$_SESSION["cod_id"] = $dadoscliente->idu;
		$_SESSION["nome_funcionario"] = $dadoscliente->nome;
		$_SESSION["id_funcionario"] = $dadoscliente->id;

		// Configura cookie
		$cookie_cel = "pdvx";
		$cookie_value2 = $dadoscliente->idu;
		setcookie($cookie_cel, $cookie_value2, time() + (86400 * 90));

		// Redireciona para a página principal
		header("location: pdv.php");
		exit;
	} else {
		header("location: ./?erro=login");
		exit;
	}
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Painel Administrativo Delivery.">
	<meta name="author" content="MDINELLY">
	<title>:: PAINEL ADMINISTRATIVO ::</title>
	<link href="../lib/font-awesome/css/font-awesome.css" rel="stylesheet">
	<link href="../lib/Ionicons/css/ionicons.css" rel="stylesheet">

	<!-- Slim CSS -->
	<link rel="stylesheet" href="../css/slim.css">
	<style>
		.logomaca {
			width: 100%;
			/* background-color: red; */
			padding: 1rem;
			overflow: hidden;
			display: flex;
			align-items: center;
			justify-content: center;

		}

		.logomaca img {
			width: 10rem;
			border-radius: 1rem;
			-webkit-user-drag: none;
			-moz-window-dragging: none;
			user-select: none;
		}
	</style>
</head>

<body>

	<?php
	// Analisa a URL e extrai o componente do caminho
	$parsedUrl = parse_url($site);
	$path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
	$path = trim($path, '/');
	$pathParts = explode('/', $path);

	?>

	<div class="signin-wrapper">

		<div class="signin-box" align="center">
			<?php

			if (!empty($pathParts[0])) {
				$nome = $pathParts[0];

				$query_configuracoes = $connect->prepare("SELECT * FROM config WHERE url = :url");
				$query_configuracoes->bindParam(':url', $nome);
				$query_configuracoes->execute();
				$result = $query_configuracoes->fetch(PDO::FETCH_ASSOC);

				if ($result) {
					$idu_empresa = $result['id'];

					$logo_empresa = $connect->prepare("SELECT foto FROM logo WHERE idu = :idu ORDER BY id DESC LIMIT 1");
					$logo_empresa->bindParam(':idu', $idu_empresa);
					$logo_empresa->execute();
					$dadoslogo = $logo_empresa->fetch(PDO::FETCH_OBJ);

					if ($dadoslogo) {

						echo '<div class="logomaca">
								<img src="../img/logomarca/' . htmlspecialchars($dadoslogo->foto) . '" />
							 </div>';
						// echo '<img src="../img/logomarca/' . htmlspecialchars($dadoslogo->foto) . '" width="350" />';
					} else {
						echo "Logo não encontrado.";
					}
				}
			}

			?>
			<h3 class="slim-logo">Painel PDV<span></h3>
			<form action="" method="post">
				<div class="form-group">
					<input type="text" class="form-control" name="loginCPF" placeholder="Login" maxlength="14" required>
				</div><!-- form-group -->
				<div class="form-group">
					<input type="password" class="form-control" name="loginSENHA" placeholder="Senha" maxlength="8" required>
				</div><!-- form-group -->
				<?php if (isset($_GET["erro"])) { ?>
					<div class="form-group" style="color:#FF0000">
						<i class="fa fa-certificate"></i> Login ou Senha incorreto.
					</div>
				<?php } ?>
				<button type="submit" class="btn btn-primary btn-block btn-signin">Entrar</button>
			</form>
		</div>
	</div>
</body>

</html>