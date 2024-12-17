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

// Função para obter o próximo ID
function getNextId($data) {
    if (empty($data)) {
        return 1;
    }
    $lastUser = end($data);
    return $lastUser['id'] + 1;
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados do formulário
    $name = $_POST['name'] ?? null;
    $whatsapp = $_POST['whatsapp'] ?? null;
    $email = $_POST['email'] ?? null;
    $senha = $_POST['senha'] ?? null;

    // Verifica se todos os campos necessários foram preenchidos
    if ($name && $whatsapp && $email && $senha) {
        // Lê o conteúdo do arquivo JSON (ou inicializa um array vazio)
        $data = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

        // Verifica se o email ou WhatsApp já existem
        foreach ($data as $user) {
            if ($user['email'] === $email) {
                echo json_encode(["erro" => "Este email já está em uso!"]);
                exit;
            }
            if ($user['whatsapp'] === $whatsapp) {
                echo json_encode(["erro" => "Este WhatsApp já está em uso!"]);
                exit;
            }
        }

        // Cria um novo usuário
        $newUser = [
            'id' => getNextId($data),
            'nome' => $name,
            'whatsapp' => $whatsapp,
            'email' => $email,
            'senha' => $senha, // Armazena a senha diretamente (use hash em produção)
            'saldo' => 150
        ];

        // Adiciona o novo usuário ao array
        $data[] = $newUser;

        // Salva os dados de volta no arquivo JSON
        if (file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT))) {
            echo json_encode(["sucesso" => "Conta criada com sucesso!"]);
        } else {
            echo json_encode(["erro" => "Erro ao salvar os dados. Tente novamente!"]);
        }
    } else {
        echo json_encode(["erro" => "Todos os campos são obrigatórios!"]);
    }
} else {
    echo json_encode(["erro" => "Método inválido!"]);
}
?>
