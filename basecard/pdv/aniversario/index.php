<?php

function aniversarioCliente($connect, $idu = null, $telefone = null)
{
    // Obtém a data atual no formato MM-DD (sem o ano, pois estamos comparando só o mês e o dia)
    $dataAtual = date('m-d');
    

    // Verifica se o checkbox "não mostrar novamente" foi marcado antes, com base no `idu` ou `telefone`
    $identificador = $idu ? $idu : $telefone;
    if (isset($_COOKIE["naoMostrarAniversario_$identificador"])) {
        return ''; // Não exibe o card ou modal
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

    // Se houver um cliente com aniversário hoje, gera o HTML do card/modal
    if ($cliente) {
        return gerarCardAniversario($cliente['nome'], $identificador);
    }

    return ''; // Se não houver cliente com aniversário, retorna uma string vazia
}

function gerarCardAniversario($nomeCliente, $identificador)
{
    // Gera o modal e o card inline, retornando como string
    return '
    <!-- Modal que bloqueia a página -->
    <div id="modalAniversario" class="modal" tabindex="-1" style="display: block; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog" style="margin-top: 10%; max-width: 400px; margin-left: auto; margin-right: auto;">
            <div class="modal-content" style="padding: 20px; border-radius: 10px; background-color: #fff;">
                <div class="modal-header" style="border-bottom: none;">
                    <h5 class="modal-title" style="font-size: 24px; font-weight: bold; text-align: center; color: #007bff;">Feliz Aniversário, ' . htmlspecialchars($nomeCliente) . '!</h5>
                </div>
                <div class="modal-body" style="text-align: center;">
                    <p>Hoje é um dia especial, e nós da nossa equipe queremos te desejar muitas felicidades!</p>
                    <p>Obrigado por estar conosco! 🎉</p>
                </div>
                <div class="modal-footer" style="justify-content: center; border-top: none;">
                    <input type="checkbox" id="naoMostrarNovamente" onchange="naoMostrarNovamente(\'' . $identificador . '\')">
                    <label for="naoMostrarNovamente">Não mostrar novamente</label>
                    <button class="btn btn-primary" onclick="fecharModalEExibirCard()" style="padding: 5px 15px; background-color: #007bff; color: white; border: none; border-radius: 5px;">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Card inline que será mostrado após o modal -->
    <div id="cardAniversario" style="display: none; width: 260px; background-color: #fff; border: 1px solid #ccc; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); z-index: 1000;">
        <div style="padding: 20px; border-radius: 10px; background-color: #007bff; color: white;">
            <h5 style="font-size: 20px; font-weight: bold; margin: 0; text-align: center;">Feliz Aniversário, ' . htmlspecialchars($nomeCliente) . '!</h5>
        </div>
        <div style="padding: 15px; text-align: center;">
            <p>Hoje é um dia especial, e nós da nossa equipe queremos te desejar muitas felicidades!</p>
            <p>Obrigado por estar conosco! 🎉</p>
        </div>
         <!--
        <div style="padding: 10px; text-align: center;">
            <input type="checkbox" id="naoMostrarNovamenteCard" onchange="naoMostrarNovamente(\'' . $identificador . '\')">
            <label for="naoMostrarNovamenteCard">Não mostrar novamente</label>
        </div>
        -->
        <!--
        <div style="text-align: center; padding-bottom: 10px;">
            <button class="btn btn-primary" onclick="fecharCard()" style="padding: 5px 15px; background-color: #007bff; color: white; border: none; border-radius: 5px;">Fechar</button>
        </div>
        -->
    </div>
    
    <script>
        // Fecha o modal e exibe o card inline
        function fecharModalEExibirCard() {
            document.getElementById("modalAniversario").style.display = "none";
            document.getElementById("cardAniversario").style.display = "block";
        }

        // Fecha o card inline
        function fecharCard() {
            document.getElementById("cardAniversario").style.display = "none";
        }

        // Função para criar o cookie "não mostrar novamente"
        function naoMostrarNovamente(identificador) {
            var checkbox = document.getElementById("naoMostrarNovamente").checked || document.getElementById("naoMostrarNovamenteCard").checked;
            if (checkbox) {
                var data = new Date();
                data.setTime(data.getTime() + (365*24*60*60*1000)); // 1 ano de validade
                var expires = "expires=" + data.toUTCString();
                document.cookie = "naoMostrarAniversario_" + identificador + "=true; " + expires + "; path=/";
            }
        }
    </script>
    ';
}
