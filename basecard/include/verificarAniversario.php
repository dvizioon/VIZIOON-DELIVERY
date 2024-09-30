<?php
include_once('../../funcoes/Conexao.php');
include_once('../../funcoes/Key.php');

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recebe os dados da requisição AJAX
    $telefone = $_POST['telefone'] ?? null;
    $id_empresa = $_POST['id_empresa'] ?? null;

    // Verifica se os parâmetros estão corretos
    if ($telefone && $id_empresa) {
        // Chama a função aniversarioCliente que retorna a modal, se aplicável
        $modalAniversario = aniversarioCliente($connect, $id_empresa, $telefone);

        // Verifica se a modal foi gerada
        if ($modalAniversario) {
            // Retorna a modal de aniversário como resposta
            echo $modalAniversario;
        } else {
            // Caso não tenha aniversariante, retorna uma resposta vazia
            echo '';
        }
    } else {
        // Caso os parâmetros estejam faltando ou incorretos
        echo '';
    }
}

// Função que verifica se é o aniversário do cliente
function aniversarioCliente($connect, $idu = null, $telefone = null)
{
    // Obtém a data atual no formato MM-DD (sem o ano, pois estamos comparando só o mês e o dia)
    $dataAtual = date('m-d');

    // Verifica se o checkbox "não mostrar novamente" foi marcado antes, com base no `idu` ou `telefone`
    $identificador = $idu ? $idu : $telefone;
    if (isset($_COOKIE["naoMostrarAniversario_$identificador"])) {
        return ''; // Não exibe a modal
    }

    // Define a consulta SQL para buscar pelo `idu` e/ou `telefone`
    $sql = "SELECT nome, data_nascimento FROM registroDados WHERE DATE_FORMAT(data_nascimento, '%m-%d') = :dataAtual";

    // Verifica quais parâmetros foram fornecidos e adapta a consulta
    if ($idu && $telefone) {
        $sql .= " AND idu = :idu AND telefone = :telefone";
    } elseif ($idu) {
        $sql .= " AND idu = :idu";
    } elseif ($telefone) {
        $sql .= " AND telefone = :telefone";
    }

    $stmt = $connect->prepare($sql);
    $stmt->bindParam(':dataAtual', $dataAtual);

    // Liga os parâmetros dinamicamente
    if ($idu) {
        $stmt->bindParam(':idu', $idu);
    }
    if ($telefone) {
        $stmt->bindParam(':telefone', $telefone);
    }

    $stmt->execute();
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    // Se houver um cliente com aniversário hoje, gera o HTML da modal
    if ($cliente) {
        return gerarModalAniversario($cliente['nome'], $identificador);
    }

    return ''; // Se não houver cliente com aniversário, retorna uma string vazia
}
// Função que gera a modal de aniversário
function gerarModalAniversario($nomeCliente, $identificador)
{
    // Gera apenas a modal de aniversário
    return '
    <!-- Modal que bloqueia a página -->
    <div id="modalAniversario" class="modal" tabindex="-1" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1050;">
        <div class="modal-dialog" style="margin-top: 10%; max-width: 400px; margin-left: auto; margin-right: auto;">
            <div class="modal-content" style="padding: 20px; border-radius: 10px; background-color: #fff;">
                <div class="modal-header" style="border-bottom: none;">
                    <h5 class="modal-title" style="font-size: 24px; font-weight: bold; text-align: center; color: #007bff;">Feliz Aniversário, ' . htmlspecialchars($nomeCliente, ENT_QUOTES, 'UTF-8') . '!</h5>
                </div>
                <div class="modal-body" style="text-align: center;">
                    <p>Hoje é um dia especial, e nós da nossa equipe queremos te desejar muitas felicidades!</p>
                    <p>Obrigado por estar conosco! 🎉</p>
                </div>
                <div class="modal-footer" style="justify-content: center; border-top: none;">
                    <input type="checkbox" id="naoMostrarNovamente" onchange="naoMostrarNovamente(\'' . addslashes($identificador) . '\')">
                    <label for="naoMostrarNovamente">Não mostrar novamente</label>
                    <button class="btn btn-primary" id="fecharModalBtn" style="padding: 5px 15px; background-color: #007bff; color: white; border: none; border-radius: 5px;">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Função para fechar a modal e remover a modal e o backdrop do DOM
        document.getElementById("fecharModalBtn").addEventListener("click", function() {
            fecharModalAniversario();
        });

        // Fecha a modal ao clicar fora da modal
        document.getElementById("modalAniversario").addEventListener("click", function(event) {
            if (event.target === this) {
                fecharModalAniversario();
            }
        });

        // Função para fechar a modal e remover o backdrop
        function fecharModalAniversario() {
            var modal = document.getElementById("modalAniversario");
            if (modal) {
                modal.remove();  // Remove a modal do DOM
            }
            var backdrop = document.querySelector(".modal-backdrop");
            if (backdrop) {
                backdrop.remove();  // Remove o backdrop do DOM
            }
            document.body.classList.remove("modal-open"); // Remove a classe que impede o scroll
            document.body.style.overflow = ""; // Restaura o scroll da página
        }

        // Função para criar o cookie "não mostrar novamente"
        function naoMostrarNovamente(identificador) {
            var checkbox = document.getElementById("naoMostrarNovamente").checked;
            if (checkbox) {
                var data = new Date();
                data.setTime(data.getTime() + (30 * 60 * 1000)); // 30 minutos de validade
                var expires = "expires=" + data.toUTCString();
                document.cookie = "naoMostrarAniversario_" + identificador + "=true; " + expires + "; path=/";
            }
        }

        // Certifique-se de remover o scroll da página quando a modal estiver aberta
        document.body.classList.add("modal-open");
        document.body.style.overflow = "hidden";
    </script>
    ';
}
