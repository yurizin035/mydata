<?php
// Configura os cabeçalhos para aceitar CORS
header("Access-Control-Allow-Origin: *"); // Permite qualquer origem
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Permite apenas POST e OPTIONS
header("Access-Control-Allow-Headers: Content-Type"); // Permite o cabeçalho Content-Type

// Tratamento de pré-voo OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Definindo o caminho do arquivo JSON
$jsonFile = 'users.json';

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados enviados
    $email = $_POST['email'] ?? null;
    $senha = $_POST['senha'] ?? null;

    // Verifica se os campos foram preenchidos
    if ($email && $senha) {
        // Lê o conteúdo do arquivo JSON
        $data = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

        // Busca o usuário pelo email
        $userFound = null;
        foreach ($data as $user) {
            if ($user['email'] === $email) {
                $userFound = $user;
                break;
            }
        }

        // Verifica se o usuário foi encontrado e se as senhas coincidem
        if ($userFound) {
            if ($userFound['senha'] === $senha) { // Em produção, use hash para senhas
                echo json_encode(["sucesso" => "Login bem-sucedido!"]);
            } else {
                echo json_encode(["erro" => "Senha incorreta!"]);
            }
        } else {
            echo json_encode(["erro" => "Usuário não encontrado!"]);
        }
    } else {
        echo json_encode(["erro" => "Email e senha são obrigatórios!"]);
    }
} else {
    echo json_encode(["erro" => "Método inválido!"]);
}
?>
