

<?php
// session_start();


$data = date("d-m-Y");
$hora = date("H:i:s");

if (isset($_POST["totalg"])) {
	// Sanitiza e prepara os dados
	$nome = strtoupper(trim($_POST['nome']));
	$wps = preg_replace("/[\(\)\-\s]/", "", $_POST['wps']);
	$fmpgto_post = $_POST['fmpgto'];
	$fmpgto_json = json_encode(['DELIVERY', $fmpgto_post]);
	$primeiro_elemento_delivery = json_decode($fmpgto_json, true)[1] ?? null;

	// Define o troco
	$troco = ($primeiro_elemento_delivery == "CARTAO") ? "0.00" : str_replace(",", ".", $_POST['troco']);
	$troco = number_format((float)$troco, 2, '.', '');

	// Prepara dados de endereço e cookies
	$cidade = $_POST['cidade'];
	$uf = $_POST['uf'];
	$numero = $_POST['numero'];
	$rua = $_POST['rua'];
	$bairro = $_POST['bairro'];
	$complemento = $_POST['complemento'] ?? "";
	$cep = $_POST['cep'] ?? "";
	$primeiro_nome = $_POST['nome'] ?? "";

	$cookies = [
		"nomecli" => $nome,
		"celcli" => $wps,
		"numero" => $numero,
		"rua" => $rua,
		"comp" => $complemento
	];

	foreach ($cookies as $key => $value) {
		setcookie($key, $value, time() + (86400 * 90));
	}

	// Prepara valores monetários
	$subtotalx = str_replace(",", ".", $_POST['subtotal']);
	$adcionaisx = str_replace(",", ".", $_POST['adcionais']);
	$totalgx = str_replace(",", ".", $_POST['totalg']);
	$taxa = str_replace(",", ".", $_POST['taxa']) ?: "0.00";

	if ($troco > 0 && $troco < $totalgx) {
		header("Location: " . $site . "delivery&troco=");
		exit;
	}

	// Insere pedido
	// $stmt = $connect->prepare("
	//     INSERT INTO pedidos (
	//         idu, idpedido, fpagamento, cidade, numero, complemento, rua, bairro, troco, nome, data, hora, celular, taxa, mesa, pessoas, obs, vsubtotal, vadcionais, vtotal
	//     ) VALUES (
	//         :idu, :id_cliente, :fmpgto_json, :cidade, :numero, :complemento, :rua, :bairro, :troco, :nome, :data, :hora, :wps, :taxa, '0', '0', '0', :subtotalx, :adcionaisx, :totalgx
	//     )
	// ");

	// $stmt->execute([
	// 	':idu' => $idu,
	// 	':id_cliente' => $id_cliente,
	// 	':fmpgto_json' => $fmpgto_json,
	// 	':cidade' => $cidade,
	// 	':numero' => $numero,
	// 	':complemento' => $complemento,
	// 	':rua' => $rua,
	// 	':bairro' => $bairro,
	// 	':troco' => $troco,
	// 	':nome' => $nome,
	// 	':data' => $data,
	// 	':hora' => $hora,
	// 	':wps' => $wps,
	// 	':taxa' => $taxa,
	// 	':subtotalx' => $subtotalx,
	// 	':adcionaisx' => $adcionaisx,
	// 	':totalgx' => $totalgx
	// ]);

	$stmt = $connect->prepare("
    INSERT INTO pedidos (
        idu, idpedido, fpagamento, cidade, numero, complemento, rua, bairro, troco, nome, data, hora, celular, taxa, mesa, pessoas, obs, status, vsubtotal, vadcionais, vtotal
    ) VALUES (
        :idu, :id_cliente, :fmpgto_json, :cidade, :numero, :complemento, :rua, :bairro, :troco, :nome, :data, :hora, :wps, :taxa, '0', '0', '0', '1', :subtotalx, :adcionaisx, :totalgx
    )
");

	$stmt->execute([
		':idu' => $idu,
		':id_cliente' => $id_cliente,
		':fmpgto_json' => $fmpgto_json,
		':cidade' => $cidade,
		':numero' => $numero,
		':complemento' => $complemento,
		':rua' => $rua,
		':bairro' => $bairro,
		':troco' => $troco,
		':nome' => $nome,
		':data' => $data,
		':hora' => $hora,
		':wps' => $wps,
		':taxa' => $taxa,
		':subtotalx' => $subtotalx,
		':adcionaisx' => $adcionaisx,
		':totalgx' => $totalgx
	]);


	// Insere os novos dados no banco de dados
	// $insertDadosRegistro = $connect->prepare("INSERT INTO registroDados (telefone, idu, nome, bairro, endereco, complemento, cep, casa, primeiro_nome) 
	//                                      VALUES (:telefone, :idu, :nome, :bairro, :endereco, :complemento, :cep, :casa, :primeiro_nome)");
	// $insertDadosRegistro->execute([
	// 	'telefone' => $wps,
	// 	'idu' => $idu,
	// 	'nome' => $nome,
	// 	'bairro' => $bairro,
	// 	'endereco' => $rua,
	// 	'complemento' => $complemento,
	// 	'cep' => $cep,
	// 	'casa' => $numero,
	// 	'primeiro_nome' => $primeiro_nome
	// ]);

	if (isset($wps) && isset($idu)) {
		// Primeiro, vamos verificar se o telefone já existe na empresa específica
		$query = $connect->prepare("SELECT * FROM registroDados WHERE telefone = :telefone AND idu = :idu");
		$query->execute(['telefone' => $wps, 'idu' => $idu]);

		$registro = $query->fetch(PDO::FETCH_ASSOC);

		if ($registro) {
			// Verificamos se os dados são diferentes dos existentes no banco
			$dadosAtualizados = [];

			if ($registro['nome'] !== $nome
			) {
				$dadosAtualizados['nome'] = $nome;
			}
			if ($registro['bairro'] !== $bairro
			) {
				$dadosAtualizados['bairro'] = $bairro;
			}
			if ($registro['endereco'] !== $rua) {
				$dadosAtualizados['endereco'] = $rua;
			}
			if ($registro['complemento'] !== $complemento) {
				$dadosAtualizados['complemento'] = $complemento;
			}
			if ($registro['cep'] !== $cep) {
				$dadosAtualizados['cep'] = $cep;
			}
			if ($registro['casa'] !== $numero) {
				$dadosAtualizados['casa'] = $numero;
			}
			if ($registro['primeiro_nome'] !== $primeiro_nome) {
				$dadosAtualizados['primeiro_nome'] = $primeiro_nome;
			}

			// Se houver dados diferentes, atualizamos apenas os campos que mudaram
			if (!empty($dadosAtualizados)) {
				$setPart = [];
				foreach ($dadosAtualizados as $campo => $valor) {
					$setPart[] = "$campo = :$campo";
				}
				$setString = implode(", ", $setPart);

				$updateQuery = $connect->prepare("UPDATE registroDados SET $setString WHERE telefone = :telefone AND idu = :idu");
				$dadosAtualizados['telefone'] = $wps;
				$dadosAtualizados['idu'] = $idu;
				$updateQuery->execute($dadosAtualizados);
			}
		} else {
			// Se o telefone não existir, inserimos os dados
			$insertDadosRegistro = $connect->prepare("INSERT INTO registroDados (telefone, idu, nome, bairro, endereco, complemento, cep, casa, primeiro_nome) 
                                     VALUES (:telefone, :idu, :nome, :bairro, :endereco, :complemento, :cep, :casa, :primeiro_nome)");
			$insertDadosRegistro->execute([
				'telefone' => $wps,
				'idu' => $idu,
				'nome' => $nome,
				'bairro' => $bairro,
				'endereco' => $rua,
				'complemento' => $complemento,
				'cep' => $cep,
				'casa' => $numero,
				'primeiro_nome' => $primeiro_nome
			]);
		}
	}




	$connect->query("UPDATE store SET status='1' WHERE idsecao='$id_cliente'");
	$connect->query("UPDATE store_o SET status='1' WHERE ids='$id_cliente'");

	// Monta mensagem WhatsApp
	$msg = "NOVO PEDIDO - $id_cliente\n";
	$msg .= "*Data:* $data\n";
	$msg .= "*Hora:* $hora\n\n";
	$msg .= "DADOS DO PEDIDO\n\n";

	$produtosca = $connect->query("SELECT * FROM store WHERE idsecao = '$id_cliente' AND status='1' AND idu='$idu' ORDER BY id DESC");
	while ($carpro = $produtosca->fetch(PDO::FETCH_OBJ)) {
		$nomepro = $connect->query("SELECT nome FROM produtos WHERE id = '" . $carpro->produto_id . "' AND idu='$idu'")->fetch(PDO::FETCH_OBJ)->nome;
		$msg .= "*Item:* $nomepro\n";
		if ($carpro->tamanho != "N") {
			$msg .= "*Tamanho:* " . $carpro->tamanho . "\n";
		}
		$msg .= "*Qnt:* " . $carpro->quantidade . "\n";
		$msg .= "*V. Unitário:* " . $carpro->valor . "\n";
		if ($carpro->obs) {
			$msg .= "*Obs:* " . $carpro->obs . "\n";
		}

		// Adicionais e Meio a Meio
		$meiom = $connect->query("SELECT * FROM store_o WHERE idp='" . $carpro->idpedido . "' AND status = '1' AND idu='$idu' AND meioameio='1'");
		if ($meiom->rowCount() > 0) {
			$msg .= "*Sabores:*\n";
			while ($meiomv = $meiom->fetch(PDO::FETCH_OBJ)) {
				$msg .= $meiomv->nome . "\n";
			}
		}

		$adicionais = $connect->query("SELECT * FROM store_o WHERE idp='" . $carpro->idpedido . "' AND status = '1' AND idu='$idu' AND meioameio='0' AND id_referencia='$carpro->referencia'");
		if ($adicionais->rowCount() > 0
		) {
			$msg .= "*Ingredientes/Adicionais:*\n";
			while ($adicionaisv = $adicionais->fetch(PDO::FETCH_OBJ)) {
				$msg .= "-  R$: " . $adicionaisv->valor . " | " . $adicionaisv->nome . "\n";
			}
		}
		$msg .= "\n";
	}

	$msg .= "DADOS DA ENTREGA\n\n";
	$msg .= "*Tipo:* Delivery\n";
	$msg .= "*Tempo de Entrega:* " . $dadosempresa->timerdelivery . "\n";
	$msg .= "*Cliente:* $nome\n";
	$msg .= "*Contato:* $wps\n";
	$msg .= "*Endereço:* $rua - $numero\n";
	$msg .= "*Complemento:* $complemento\n";
	$msg .= "*Bairro:* $bairro\n";
	$msg .= "*Cidade:* $cidade/$uf\n\n";

	$msg .= "DADOS DO PAGAMENTO\n\n";
	$msg .= "*Pagamento em:* $primeiro_elemento_delivery\n";
	$msg .= "*Subtotal:* R$: " . number_format($subtotalx, 2, ',', ' ') . "\n";
	if ($adcionaisx > 0.00) {
		$msg .= "*Adicionais:* R$: " . $adcionaisx . "\n";
	}
	$msg .= "*Taxa de Entrega:* " . ($taxa > 0.00 ? "R$: " . $taxa : "Grátis") . "\n";
	$msg .= "*Total:* R$: " . number_format($totalgx, 2, ',', ' ') . "\n";
	if ($troco > 0.00) {
		$msg .= "*Troco para:* R$: " . number_format($troco, 2, ',', ' ') . "\n";
		$msg .= "*Valor do Troco:* R$: " . number_format($troco - $totalgx, 2, ',', ' ') . "\n";
	}


	// Consulta para obter os produtos da seção especificada
	$produtosca = $connect->query("SELECT * FROM store WHERE idsecao = '$id_cliente' AND status='1' AND idu='$idu' ORDER BY id DESC");

	// Início da tabela HTML
	$pedido_html = "<table class='responsive-table'>
    <thead>
        <tr>
		 	<th>ID referencia</th>
            <th>Item</th>
            <th>Tamanho</th>
            <th>Quantidade</th>
            <th>V. Unitário</th>
            <th>Observações</th>
            <th>Sabores</th>
            <th>Ingredientes/Adicionais</th>
        </tr>
    </thead>
    <tbody>";

	// Loop através dos produtos
	while ($carpro = $produtosca->fetch(PDO::FETCH_OBJ)) {
		// Consulta para obter o nome do produto
		$nomepro = $connect->query("SELECT nome FROM produtos WHERE id = '" . $carpro->produto_id . "' AND idu='$idu'")
			->fetch(PDO::FETCH_OBJ)->nome;

		$sabores = '';
		$adcionais = '';

		// Consulta para obter os sabores (meio a meio)
		$meiom = $connect->query("SELECT * FROM store_o WHERE idp='" . $carpro->idpedido . "' AND status = '1' AND idu='$idu' AND meioameio='1' AND id_referencia='$carpro->referencia'");
		while ($meiomv = $meiom->fetch(PDO::FETCH_OBJ)) {
			$sabores .= $meiomv->nome;
		}

		// Consulta para obter os adicionais
		$adcionais_res = $connect->query("SELECT * FROM store_o WHERE idp='" . $carpro->idpedido . "' AND status = '1' AND idu='$idu' AND meioameio='0' AND id_referencia='$carpro->referencia' ");
		while ($adcionaisv = $adcionais_res->fetch(PDO::FETCH_OBJ)) {
			$adcionais .= "- R$: " . $adcionaisv->valor . " | " . $adcionaisv->nome ;
		}

		// Adiciona a linha à tabela HTML, substituindo campos vazios por '-'
		$pedido_html .= "<tr>
		 <td>" . htmlspecialchars($carpro->referencia ?: '-') . "</td>
        <td>" . htmlspecialchars($nomepro ?: '-') . "</td>
        <td>" . htmlspecialchars($carpro->tamanho != "N" ? $carpro->tamanho : '-') . "</td>
        <td>" . htmlspecialchars($carpro->quantidade ?: '-') . "</td>
        <td>" . htmlspecialchars($carpro->valor ?: '-') . "</td>
        <td>" . htmlspecialchars($carpro->obs ?: '-') . "</td>
        <td>" . htmlspecialchars($sabores ?: '-') . "</td>
        <td>" . htmlspecialchars($adcionais ?: '-') . "</td>
    </tr>";
	}

	// Fecha a tabela HTML
	$pedido_html .= "</tbody></table>";

	// Exibe a tabela
	// echo $pedido_html;

	// Execute a consulta diretamente com interpolação de variáveis
	$sql_dados_empresa = "SELECT * FROM config WHERE id = '$idu'";
	$stmt_empresa = $connect->query($sql_dados_empresa);

	// Verifique se a execução da consulta foi bem-sucedida
	if ($stmt_empresa) {
		$result = $stmt_empresa->fetch(PDO::FETCH_OBJ);

		if ($result) {
			$nome_empresa = $result->url;
		} else {
			$nome_empresa = 'Pro - Vendas'; // Valor padrão se não encontrar
		}
	} else {
		// Recuperar informações de erro
		$errorInfo = $connect->errorInfo();
		echo "Erro na execução da consulta: " . $errorInfo[2];
	}

	// var_dump($result);

	// Criar uma Mensagem de Finalização
	$dadosSimples = [
		"nome" => $nome,
		"celcli" => $wps,
		"nome_empresa" => $nome_empresa,
		"pedidos" => $pedido_html
	];


	// Armazena a mensagem na sessão
	$_SESSION['mensagem_usuario'] = $dadosSimples;
	$_SESSION['mensagem_whatsapp'] = $msg;

	// Destruir o ID do cliente para gerar um novo
	unset($_SESSION['id_cliente']);
	session_write_close();

	// Redireciona para a página de sucesso
	header("Location: success.php");
	exit();
} else {
	// Redireciona para a página inicial ou para uma página de erro
	unset($_SESSION['id_cliente']);
	session_write_close();
	header("Location: ./");
	exit();
}
