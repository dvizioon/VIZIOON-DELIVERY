<?php
include_once('../../funcoes/Conexao.php');
include_once('../../funcoes/Key.php');


if (isset($_POST['telefone']) && isset($_POST['id_empresa'])) {
   $telefone = $_POST['telefone'];
   $idu = $_POST['id_empresa'];

   // Consulta para verificar se o telefone já existe na empresa
   $query = $connect->prepare("SELECT * FROM registroDados WHERE telefone = :telefone AND idu = :idu");

   // Executa a consulta passando ambos os parâmetros ao mesmo tempo
   $query->execute(['telefone' => $telefone, 'idu' => $idu]);

   $registro = $query->fetch(PDO::FETCH_ASSOC);

   if ($registro) {
      // Retorna os dados como JSON
      echo json_encode([
         'existe' => true,
         'nome' => $registro['nome'],
         'bairro' => $registro['bairro'],
         'endereco' => $registro['endereco'],
         'complemento' => $registro['complemento'],
         'cep' => $registro['cep'],
         'casa' => $registro['casa'],
         'primeiro_nome' => $registro['primeiro_nome']
      ]);
   } else {
      // Retorna um JSON indicando que o telefone não foi encontrado
      echo json_encode(['existe' => false, 'telefone' => $telefone]);


   }
}
