<?php
// Permitir CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Verifica se o método de requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Método não permitido. Use POST."]);
    http_response_code(405);
    exit;
}

// Lê o corpo da requisição
$input = json_decode(file_get_contents("php://input"), true);

// Verifica se o e-mail foi enviado
if (!isset($input['email']) || empty($input['email'])) {
    echo json_encode(["error" => "O campo 'email' é obrigatório."]);
    http_response_code(400);
    exit;
}

$email = $input['email'];

// Carrega o arquivo JSON
$jsonFile = 'users.json';
if (!file_exists($jsonFile)) {
    echo json_encode(["error" => "Arquivo 'users.json' não encontrado."]);
    http_response_code(500);
    exit;
}

$data = json_decode(file_get_contents($jsonFile), true);

// Verifica se o arquivo JSON foi lido corretamente
if ($data === null) {
    echo json_encode(["error" => "Erro ao processar o arquivo JSON."]);
    http_response_code(500);
    exit;
}

// Busca pelo usuário com o email fornecido
$user = array_filter($data, function ($user) use ($email) {
    return isset($user['email']) && $user['email'] === $email;
});

// Retorna o resultado
if (!empty($user)) {
    echo json_encode(array_values($user)); // Converte para array indexado
    http_response_code(200);
} else {
    echo json_encode(["error" => "Usuário não encontrado."]);
    http_response_code(404);
}
?>
