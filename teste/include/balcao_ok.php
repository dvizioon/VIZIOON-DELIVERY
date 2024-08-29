<?php
// Configurações iniciais
$data = date("d-m-Y");
$hora = date("H:i:s");

if (isset($_POST["totalg"])) {
	// Sanitização e validação dos dados de entrada
	$nome = strtoupper(trim($_POST['nome']));
	$wps = preg_replace('/[^0-9]/', '', $_POST['wps']); // Remove caracteres não numéricos

	if (strlen($wps) != 11) {
		header("Location: {$site}balcao&erro=");
		exit;
	}

	$subtotal = str_replace(",", ".", $_POST['subtotal']);
	$adcionais = str_replace(",", ".", $_POST['adcionais']);
	$totalg = str_replace(",", ".", $_POST['totalg']);

	// Configuração de cookies
	setcookie("nomecli", $nome, time() + (86400 * 90));
	setcookie("celcli", $wps, time() + (86400 * 90));

	// Remoção de arquivo se necessário
	if (isset($_GET["up"])) {
		@unlink($_GET["up"]);
	}

	// Processamento do pagamento
	$fmpgto_array = ['DELIVERY', 'BALCAO'];
	$fmpgto_json = json_encode($fmpgto_array);

	// Conexão com banco de dados usando prepared statements
	// $stmt = $connect->prepare("
	//     INSERT INTO pedidos (
	//         idu, idpedido, fpagamento, cidade, numero, complemento, rua, bairro, troco, 
	//         nome, data, hora, celular, taxa, mesa, pessoas, obs, vsubtotal, vadcionais, vtotal
	//     ) VALUES (
	//         :idu, :idpedido, :fpagamento, '0', '0', '0', '0', '0', '0.00', 
	//         :nome, :data, :hora, :celular, '0', '0', '0', '0', :vsubtotal, :vadcionais, :vtotal
	//     )
	// ");
	// $stmt->execute([
	// 	':idu' => $idu,
	// 	':idpedido' => $id_cliente,
	// 	':fpagamento' => $fmpgto_json,
	// 	':nome' => $nome,
	// 	':data' => $data,
	// 	':hora' => $hora,
	// 	':celular' => $wps,
	// 	':vsubtotal' => $subtotal,
	// 	':vadcionais' => $adcionais,
	// 	':vtotal' => $totalg
	// ]);

	// Defina suas variáveis antes de usar na consulta
	$cidade = isset($cidade) ? $cidade : '0';
	$numero = isset($numero) ? $numero : '0';
	$complemento = isset($complemento) ? $complemento : '0';
	$rua = isset($rua) ? $rua : '0';
	$bairro = isset($bairro) ? $bairro : '0';
	$troco = isset($troco) ? $troco : '0.00';
	$taxa = isset($taxa) ? $taxa : '0';
	$mesa = isset($mesa) ? $mesa : '0';
	$pessoas = isset($pessoas) ? $pessoas : '0';
	$obs = isset($obs) ? $obs : '';

	// Preparar a consulta para inserção
	$stmt = $connect->prepare("
    INSERT INTO pedidos (
        idu, idpedido, fpagamento, cidade, numero, complemento, rua, bairro, troco, 
        nome, data, hora, celular, taxa, mesa, pessoas, obs, status, vsubtotal, vadcionais, vtotal
    ) VALUES (
        :idu, :idpedido, :fpagamento, :cidade, :numero, :complemento, :rua, :bairro, :troco, 
        :nome, :data, :hora, :celular, :taxa, :mesa, :pessoas, :obs, '1', :vsubtotal, :vadcionais, :vtotal
    )
");

	// Executar a consulta
	$stmt->execute([
		':idu' => $idu,
		':idpedido' => $id_cliente,
		':fpagamento' => $fmpgto_json,
		':cidade' => $cidade,
		':numero' => $numero,
		':complemento' => $complemento,
		':rua' => $rua,
		':bairro' => $bairro,
		':troco' => $troco,
		':nome' => $nome,
		':data' => $data,
		':hora' => $hora,
		':celular' => $wps,
		':taxa' => $taxa,
		':mesa' => $mesa,
		':pessoas' => $pessoas,
		':obs' => $obs,
		':vsubtotal' => $subtotal,
		':vadcionais' => $adcionais,
		':vtotal' => $totalg
	]);




	// Atualização de status na tabela store
	$stmt = $connect->prepare("UPDATE store SET status='1' WHERE idsecao = :idsecao");
	$stmt->execute([':idsecao' => $id_cliente]);

	$stmt = $connect->prepare("UPDATE store_o SET status='1' WHERE ids = :ids");
	$stmt->execute([':ids' => $id_cliente]);

	// Montagem da mensagem para WhatsApp
	$msg = "NOVO PEDIDO - {$id_cliente}\n";
	$msg .= "*Data:* {$data}\n";
	$msg .= "*Hora:* {$hora}\n\n";
	$msg .= "DADOS DO PEDIDO\n\n";

	$stmt = $connect->prepare("
        SELECT * FROM store 
        WHERE idsecao = :idsecao AND status = '1' AND idu = :idu 
        ORDER BY id DESC
    ");
	$stmt->execute([':idsecao' => $id_cliente, ':idu' => $idu]);

	while ($carpro = $stmt->fetch(PDO::FETCH_OBJ)) {
		$stmt_nome = $connect->prepare("SELECT nome FROM produtos WHERE id = :id AND idu = :idu");
		$stmt_nome->execute([':id' => $carpro->produto_id, ':idu' => $idu]);
		$nomeprox = $stmt_nome->fetch(PDO::FETCH_OBJ);

		$msg .= "*Item:* {$nomeprox->nome}\n";
		if ($carpro->tamanho != "N") {
			$msg .= "*Tamanho:* {$carpro->tamanho}\n";
		}
		$msg .= "*Qnt:* {$carpro->quantidade}\n";
		$msg .= "*V. Unitário:* {$carpro->valor}\n";
		if ($carpro->obs) {
			$msg .= "*Obs:* {$carpro->obs}\n";
		}

		// Dados do meio a meio
		$stmt_meiom = $connect->prepare("
            SELECT nome FROM store_o 
            WHERE idp = :idpedido AND status = '1' AND idu = :idu AND meioameio = '1'
        ");
		$stmt_meiom->execute([':idpedido' => $carpro->idpedido, ':idu' => $idu]);
		$meiomc = $stmt_meiom->rowCount();

		if ($meiomc > 0) {
			$msg .= "*{$meiomc} Sabores:*\n";
			while ($meiomv = $stmt_meiom->fetch(PDO::FETCH_OBJ)) {
				$msg .= "{$meiomv->nome}\n";
			}
		}

		// Dados dos adicionais
		$stmt_adcionais = $connect->prepare("
            SELECT nome, valor FROM store_o 
            WHERE idp = :idpedido AND status = '1' AND idu = :idu AND meioameio = '0'
        ");
		$stmt_adcionais->execute([':idpedido' => $carpro->idpedido, ':idu' => $idu]);
		if ($stmt_adcionais->rowCount() > 0) {
			$msg .= "*Ingredientes/Adicionais:*\n";
			while ($adcionaisv = $stmt_adcionais->fetch(PDO::FETCH_OBJ)) {
				$msg .= "- R$: {$adcionaisv->valor} | {$adcionaisv->nome}\n";
			}
		}
		$msg .= "\n";
	}

	$msg .= "DADOS DA ENTREGA\n\n";
	$msg .= "*Tipo:* Retirada no Balcão\n";
	$msg .= "*Tempo de Entrega:* {$dadosempresa->timerbalcao}\n";
	$msg .= "*Cliente:* {$nome}\n";
	$msg .= "*Contato:* {$wps}\n\n";

	$msg .= "DADOS DO PAGAMENTO\n\n";
	$msg .= "*Subtotal:* R$: {$subtotal}\n";
	if ($adcionais > "0.00") {
		$msg .= "*Adicionais:* R$: {$adcionais}\n";
	}
	$msg .= "*Valor Total:* R$: {$totalg}\n\n";
	$msg .= "ENDEREÇO DE RETIRADA\n\n";
	$msg .= "*{$dadosempresa->nomeempresa}*\n";
	$msg .= "{$dadosempresa->rua} - nº {$dadosempresa->numero}\n";
	$msg .= "{$dadosempresa->bairro}\n";

	// Início da tabela HTML
	$pedido_html = "<table class='responsive-table'>
    <thead>
        <tr>
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
		$stmt_nome = $connect->prepare("SELECT nome FROM produtos WHERE id = :produto_id AND idu = :idu");
		$stmt_nome->execute([':produto_id' => $carpro->produto_id, ':idu' => $idu]);
		$nomepro = $stmt_nome->fetch(PDO::FETCH_OBJ)->nome;

		$sabores = '';
		$adcionais = '';

		// Consulta para obter os sabores (meio a meio)
		$stmt_meiom = $connect->prepare("SELECT nome FROM store_o WHERE idp = :idpedido AND status = '1' AND idu = :idu AND meioameio = '1' AND meioameio='0' AND id_referencia='$carpro->referencia' ");
		$stmt_meiom->execute([':idpedido' => $carpro->idpedido, ':idu' => $idu]);
		while ($meiomv = $stmt_meiom->fetch(PDO::FETCH_OBJ)) {
			$sabores .= htmlspecialchars($meiomv->nome);
		}

		// Consulta para obter os adicionais
		$stmt_adcionais = $connect->prepare("SELECT nome, valor FROM store_o WHERE idp = :idpedido AND status = '1' AND idu = :idu AND meioameio = '0' AND meioameio='0' AND id_referencia='$carpro->referencia'");
		$stmt_adcionais->execute([':idpedido' => $carpro->idpedido, ':idu' => $idu]);
		while ($adcionaisv = $stmt_adcionais->fetch(PDO::FETCH_OBJ)) {
			$adcionais .= "- R$: " . htmlspecialchars($adcionaisv->valor) . " | " . htmlspecialchars($adcionaisv->nome);
		}

		// Adiciona a linha à tabela HTML, substituindo campos vazios por '-'
		$pedido_html .= "<tr>
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

	// Executa a consulta para obter dados da empresa
	$stmt_empresa = $connect->prepare("SELECT * FROM config WHERE id = :idu");
	$stmt_empresa->execute([':idu' => $idu]);

	// Verifica se a consulta foi bem-sucedida
	if ($stmt_empresa) {
		$result = $stmt_empresa->fetch(PDO::FETCH_OBJ);
		$nome_empresa = $result ? htmlspecialchars($result->url) : 'Pro - Vendas'; // Valor padrão se não encontrar
	} else {
		// Recupera informações de erro
		$errorInfo = $connect->errorInfo();
		echo "Erro na execução da consulta: " . htmlspecialchars($errorInfo[2]);
	}

	// Cria uma mensagem de finalização
	$dadosSimples = [
		"nome" => htmlspecialchars($nome),
		"celcli" => htmlspecialchars($wps),
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
