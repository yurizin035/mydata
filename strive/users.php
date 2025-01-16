<?php
// Definindo o caminho do arquivo JSON
$jsonFile = 'users.json';

// Configura os cabeçalhos para aceitar CORS
header("Access-Control-Allow-Origin: *"); // Permite qualquer origem
header("Access-Control-Allow-Methods: GET, OPTIONS"); // Permite GET e OPTIONS
header("Access-Control-Allow-Headers: Content-Type"); // Permite o cabeçalho Content-Type

// Tratamento de pré-voo OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Verifica se o arquivo existe e contém dados
if (file_exists($jsonFile)) {
    // Lê o conteúdo do arquivo JSON
    $data = file_get_contents($jsonFile);

    // Converte os dados JSON para um array associativo
    $users = json_decode($data, true);

    // Retorna os dados como resposta JSON
    echo json_encode($users);
} else {
    // Se o arquivo não existir ou estiver vazio
    echo json_encode(["erro" => "Não há usuários cadastrados ou o arquivo não foi encontrado."]);
}
?>
