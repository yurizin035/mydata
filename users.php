<?php
// Permite requisições de qualquer origem (use seu domínio específico em produção)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Permite que a requisição use o método OPTIONS (para pré-fluxo CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;  // Termina a requisição aqui para o pré-fluxo CORS
}

// Definir o caminho para o arquivo JSON
$jsonFile = 'users.json';

// Verificar se o arquivo existe
if (file_exists($jsonFile)) {
    // Ler o conteúdo do arquivo
    $jsonData = file_get_contents($jsonFile);

    // Decodificar o JSON para validar e trabalhar com ele
    $data = json_decode($jsonData, true);

    // Verificar se o JSON foi decodificado corretamente
    if ($data !== null) {
        // Retornar o conteúdo como JSON novamente
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
    } else {
        // Erro na leitura do JSON
        echo json_encode(['error' => 'O arquivo JSON é inválido.']);
    }
} else {
    // Erro caso o arquivo não seja encontrado
    echo json_encode(['error' => 'Arquivo "users.json" não encontrado.']);
}
