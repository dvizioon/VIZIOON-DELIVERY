<?php
// function sanitize_post()
// {
//     global $servidor;
//     global $usuario;
//     global $senha;
//     global $banco;
//     $conexao = new mysqli($servidor, $usuario, $senha, $banco);
//     // Verifica se $_POST está definido e não é nulo
//     if (isset($_POST) && !empty($_POST)) {
//         // Itera sobre cada elemento do array $_POST
//         foreach ($_POST as $key => $value) {
//             // Remove espaços em branco no início e no final do valor
//             $value = trim($value);

//             // Usa mysqli_real_escape_string para prevenir SQL injection
//             $value = mysqli_real_escape_string($conexao, $value);

//             // Usa htmlspecialchars para prevenir XSS
//             $value = htmlspecialchars($value);

//             // Atualiza o valor no array $_POST
//             $_POST[$key] = $value;
//         }
//     }
// }


function sanitize_post() {
    global $servidor;
    global $usuario;
    global $senha;
    global $banco;

    // Cria uma nova conexão com o banco de dados
    $conexao = new mysqli($servidor, $usuario, $senha, $banco);

    // Verifica se há erros na conexão
    if ($conexao->connect_error) {
        die("Conexão falhou: " . $conexao->connect_error);
    }

    // Verifica se $_POST está definido e não é nulo
    if (isset($_POST) && !empty($_POST)) {
        // Itera sobre cada elemento do array $_POST
        foreach ($_POST as $key => $value) {
            // Se o valor é um array, processa cada item no array
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    // Remove espaços em branco no início e no final do valor
                    $subValue = trim($subValue);
                    
                    // Usa mysqli_real_escape_string para prevenir SQL injection
                    $subValue = mysqli_real_escape_string($conexao, $subValue);

                    // Usa htmlspecialchars para prevenir XSS
                    $subValue = htmlspecialchars($subValue);

                    // Atualiza o valor no array $_POST
                    $_POST[$key][$subKey] = $subValue;
                }
            } else {
                // Remove espaços em branco no início e no final do valor
                $value = trim($value);

                // Usa mysqli_real_escape_string para prevenir SQL injection
                $value = mysqli_real_escape_string($conexao, $value);

                // Usa htmlspecialchars para prevenir XSS
                $value = htmlspecialchars($value);

                // Atualiza o valor no array $_POST
                $_POST[$key] = $value;
            }
        }
    }
}


function sanitize_get()
{
    global $servidor;
    global $usuario;
    global $senha;
    global $banco;
    $conexao = new mysqli($servidor, $usuario, $senha, $banco);
    // Verifica se $_POST está definido e não é nulo
    if (isset($_GET) && !empty($_POST)) {
        // Itera sobre cada elemento do array $_POST
        foreach ($_GET as $key => $value) {
            // Remove espaços em branco no início e no final do valor
            $value = trim($value);

            // Usa mysqli_real_escape_string para prevenir SQL injection
            $value = mysqli_real_escape_string($conexao, $value);

            // Usa htmlspecialchars para prevenir XSS
            $value = htmlspecialchars($value);

            // Atualiza o valor no array $_POST
            $_GET[$key] = $value;
        }
    }
}
sanitize_post();
sanitize_get();