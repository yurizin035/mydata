<?php
// Permite requisições de qualquer origem (use seu domínio específico em produção)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Permite que a requisição use o método OPTIONS (para pré-fluxo CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;  // Termina a requisição aqui para o pré-fluxo CORS
}

// Definir o caminho do arquivo JSON
$jsonFilePath = 'users.json';

// Verificar se a requisição é do tipo POST e se o conteúdo é JSON
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    // Receber os dados JSON da requisição
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $email = $data['email'] ?? '';
    $senha = $data['senha'] ?? '';

    // Verificar se o arquivo JSON existe
    if (file_exists($jsonFilePath)) {
        // Ler o conteúdo do arquivo JSON
        $jsonContent = file_get_contents($jsonFilePath);

        // Converter o conteúdo JSON em um array
        $users = json_decode($jsonContent, true);

        // Inicializar variáveis de controle
        $userFound = false;
        $passwordCorrect = false;

        // Verificar se o email existe no arquivo JSON
        foreach ($users as $user) {
            if ($user['email'] == $email) {
                $userFound = true;
                // Verificar se a senha está correta
                if ($user['senha'] == $senha) {
                    $passwordCorrect = true;
                }
                break; // Não precisa continuar o loop se já encontrou o usuário
            }
        }

        // Enviar a resposta como JSON
        if ($userFound) {
            if ($passwordCorrect) {
                echo json_encode(['message' => 'Sucesso! Login realizado.']);
            } else {
                echo json_encode(['message' => 'Senha incorreta.']);
            }
        } else {
            echo json_encode(['message' => 'Email não encontrado.']);
        }
    } else {
        echo json_encode(['message' => 'Erro ao acessar o arquivo de usuários.']);
    }
} else {
    echo json_encode(['message' => 'Método de requisição inválido ou conteúdo não é JSON.']);
}
?>
