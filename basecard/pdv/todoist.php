<style>
    /* Estilos para o componente To-Do List */
    .todo-list {
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        width: 100%;
        margin: 20px auto;
    }

    form {
        width: 100%;
    }

    .todo-list ul {
        list-style-type: none;
        padding: 0;
    }

    .todo-list li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #ddd;
    }

    .todo-list li:last-child {
        border-bottom: none;
    }

    .todo-list input[type="checkbox"] {
        margin-right: 10px;
        width: 20px;
        height: 20px;
        cursor: pointer;
        -webkit-appearance: none;
        appearance: none;
        border: 2px solid #28a745;
        border-radius: 4px;
        outline: none;
        transition: background-color 0.3s ease;
    }

    .todo-list input[type="checkbox"]:checked {
        background-color: #28a745;
        border-color: #28a745;
    }

    .pedido-nome {
        font-weight: bold;
        color: #333;
    }

    .pedido-valor {
        font-size: 14px;
        color: #555;
    }

    .btn-resolver {
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 15px;
        display: block;
        width: 100%;
        text-align: center;
        transition: background-color 0.3s ease;
    }

    .btn-resolver:hover {
        background-color: #218838;
    }

    .btn-reopen {
        background-color: #ffc107;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-reopen:hover {
        background-color: #e0a800;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        padding: 10px;
        border-radius: 5px;
        margin: 20px;
    }

    .pedido-entregue {
        text-decoration: line-through;
        color: #888;
    }
</style>


<?php

class TodoListComponent
{
    private $connect;
    private $cod_id;
    private $idpedido;

    public function __construct($connect, $cod_id, $idpedido = null)
    {
        $this->connect = $connect;
        $this->cod_id = $cod_id;
        $this->idpedido = $idpedido;
    }

    // Método para obter todos os pedidos, filtrando por idpedido se fornecido
    private function getOrders()
    {
        $query = "
            SELECT s.id, s.idpedido, s.valor, s.pedido_entregue, p.nome 
            FROM store s
            INNER JOIN produtos p ON s.produto_id = p.id
            WHERE s.idu = :idu";

        if ($this->idpedido !== null) {
            $query .= " AND s.idpedido = :idpedido";
        }

        $stmt = $this->connect->prepare($query);
        $stmt->bindParam(':idu', $this->cod_id, PDO::PARAM_INT);

        if ($this->idpedido !== null) {
            $stmt->bindParam(':idpedido', $this->idpedido, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para marcar pedidos como entregues
    private function resolveOrders($pedidos_resolvidos)
    {
        foreach ($pedidos_resolvidos as $id) {
            $update_query = "UPDATE store SET pedido_entregue = 'sim' WHERE id = :id AND idu = :idu";
            $stmt_update = $this->connect->prepare($update_query);
            $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_update->bindParam(':idu', $this->cod_id, PDO::PARAM_INT);
            $stmt_update->execute();
        }
        // Redirecionar para evitar o reenvio do formulário
        echo '<form id="autoRedirectForm" action="./verpedido.php" method="post">';
        echo '<input type="hidden" name="codigop" value="' . htmlspecialchars($_POST['codigop']) . '" />';
        echo '</form>';
        echo '<script type="text/javascript">
            document.getElementById("autoRedirectForm").submit();
          </script>';
        exit;
    }

    // Método para desmarcar pedidos como entregues
    private function reopenOrders($pedidos_reabertos)
    {
        foreach ($pedidos_reabertos as $id) {
            $update_query = "UPDATE store SET pedido_entregue = 'nao' WHERE id = :id AND idu = :idu";
            $stmt_update = $this->connect->prepare($update_query);
            $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_update->bindParam(':idu', $this->cod_id, PDO::PARAM_INT);
            $stmt_update->execute();
        }
        // Redirecionar para evitar o reenvio do formulário
        echo '<form id="autoRedirectForm" action="./verpedido.php" method="post">';
        echo '<input type="hidden" name="codigop" value="' . htmlspecialchars($_POST['codigop']) . '" />';
        echo '</form>';
        echo '<script type="text/javascript">
            document.getElementById("autoRedirectForm").submit();
          </script>';
        exit;
    }

    // Método para renderizar o componente To-Do List
    public function render()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['pedidos_resolvidos'])) {
                $this->resolveOrders($_POST['pedidos_resolvidos']);
            } elseif (isset($_POST['pedidos_reabertos'])) {
                $this->reopenOrders($_POST['pedidos_reabertos']);
            }
        }

        $pedidos = $this->getOrders();

        if ($pedidos) {
            echo '<form method="post" action="./verpedido.php">';
            echo '<div class="todo-list">';
            echo '<ul>';
            foreach ($pedidos as $pedido) {
                $class = $pedido['pedido_entregue'] === 'sim' ? 'pedido-entregue' : '';
                $checkbox = $pedido['pedido_entregue'] === 'nao' ? '<input type="checkbox" name="pedidos_resolvidos[]" value="' . htmlspecialchars($pedido['id']) . '"> ' : '';
                $reopen_button = $pedido['pedido_entregue'] === 'sim' ? '<button type="submit" name="pedidos_reabertos[]" value="' . htmlspecialchars($pedido['id']) . '" class="btn-reopen">Reabrir Pedido</button>' : '';

                echo '<li class="' . $class . '">';
                echo '<label>';
                echo $checkbox;
                echo '<span class="pedido-nome">Produto: ' . htmlspecialchars($pedido['nome']) . '</span>';
                echo '<span class="pedido-valor"> | Valor: R$ ' . htmlspecialchars($pedido['valor']) . '</span>';
                echo '</label>';
                echo $reopen_button;
                echo '</li>';
            }
            echo '</ul>';
            echo '<input type="hidden" name="codigop" value="' . htmlspecialchars($this->idpedido) . '" />';
            echo $pedido['pedido_entregue'] === 'nao' ? '<button type="submit" class="btn-resolver">Resolver Pedidos</button>' : '';
            echo '</div>';
            echo '</form>';
        } else {
            echo '<p>Não há pedidos pendentes de entrega.</p>';
        }
    }
}


?>