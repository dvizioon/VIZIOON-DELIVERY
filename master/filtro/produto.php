<?php
session_start();

require_once "../../../funcoes/Conexao.php";
require_once "../../../funcoes/Key.php";

$codigo_empresa = $_SESSION['cod_id']; // ID da empresa atual

// Componente para obter produtos com base em filtros
class ProdutoComponent
{
    private $conn;
    private $codigo_empresa;

    // Construtor da classe, inicializando a conexão e o código da empresa
    public function __construct($conn, $codigo_empresa)
    {
        $this->conn = $conn;
        $this->codigo_empresa = $codigo_empresa;
    }

    // Função para obter produtos com base nos filtros fornecidos
    public function obterProdutos($filtros = array())
    {
        // Inicia a consulta SQL com o filtro para a empresa
        $query = "SELECT p.*, c.nome AS categoria_nome 
                  FROM produtos p 
                  JOIN categorias c ON p.categoria = c.id 
                  WHERE p.idu = ?";
        $params = array($this->codigo_empresa);

        // Adiciona filtros à consulta se existirem
        if (!empty($filtros['nome'])) {
            $query .= " AND p.nome LIKE ?";
            $params[] = "%" . $filtros['nome'] . "%";
        }

        if (!empty($filtros['categoria'])) {
            $query .= " AND c.nome = ?";
            $params[] = $filtros['categoria'];
        }

        if (!empty($filtros['valor_min'])) {
            $query .= " AND CAST(p.valor AS DECIMAL(10,2)) >= ?";
            $params[] = $filtros['valor_min'];
        }

        if (!empty($filtros['valor_max'])) {
            $query .= " AND CAST(p.valor AS DECIMAL(10,2)) <= ?";
            $params[] = $filtros['valor_max'];
        }

        if (isset($filtros['destaques'])) {
            $query .= " AND p.destaques = ?";
            $params[] = $filtros['destaques'];
        }

        if (isset($filtros['visivel'])) {
            $query .= " AND p.visivel = ?";
            $params[] = $filtros['visivel'];
        }

        if (isset($filtros['status'])) {
            $query .= " AND p.status = ?";
            $params[] = $filtros['status'];
        }

        // Contar a quantidade de produtos retornados pela pesquisa
        $countQuery = str_replace("SELECT p.*, c.nome AS categoria_nome", "SELECT COUNT(*) as total_pesquisa", $query);
        $stmtCount = $this->conn->prepare($countQuery);
        $stmtCount->execute($params);
        $totalPesquisa = $stmtCount->fetch(PDO::FETCH_ASSOC)['total_pesquisa'];
        

        // Ordena os resultados pelo nome do produto
        $query .= " ORDER BY p.nome ASC";

        // Prepara e executa a consulta para obter os produtos
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $produtos = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Retorna o total de produtos e os produtos encontrados
        return [
            'total_pesquisa' => $totalPesquisa,
            'produtos' => $produtos
        ];
    }
}

// Exemplo de uso do componente
try {
    // Cria uma instância do componente ProdutoComponent
    $produtoComponent = new ProdutoComponent($connect, $codigo_empresa);

    // Api Documentação
    // $filtros = array(
    //     'nome' => 'Bolo',
    //     'categoria' => 'massas',
    //     'valor_min' => 10.00,
    //     'valor_max' => 100.00,
    //     'destaques' => 1,
    //     'visivel' => 'G',
    //     'status' => 1,
    // );

  

    // Define os filtros (exemplo)
    $filtros = array(
        // 'nome' => 'Bolo',
        // 'valor_min' => 5.00,
        // 'valor_max' => 5.00,
        // 'status' => 1,
        'categoria' => 'Bebidas',
    );

    // Obtém os produtos e a quantidade total de produtos que correspondem à pesquisa
    $resultado = $produtoComponent->obterProdutos($filtros);

    // Exibe o total de produtos retornados pela pesquisa
    echo "Total de produtos retornados pela pesquisa: " . htmlspecialchars($resultado['total_pesquisa']) . "<br><br>";

    // Exibe os produtos
    foreach ($resultado['produtos'] as $produto) {
        echo "ID: " . htmlspecialchars($produto->id) . "<br>";
        echo "Nome: " . htmlspecialchars($produto->nome) . "<br>";
        echo "Categoria: " . htmlspecialchars($produto->categoria_nome) . "<br>";
        echo "Valor: " . htmlspecialchars($produto->valor) . "<br>";
        echo "Ingredientes: " . htmlspecialchars($produto->ingredientes) . "<br>";
        echo "Destaques: " . htmlspecialchars($produto->destaques) . "<br>";
        echo "Visível: " . htmlspecialchars($produto->visivel) . "<br>";
        echo "Status: " . htmlspecialchars($produto->status) . "<br><br>";
    }
} catch (PDOException $e) {
    // Exibe uma mensagem de erro em caso de falha na execução da consulta
    echo "Erro: " . $e->getMessage();
}

