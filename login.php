<?php
// Definir o caminho do arquivo JSON
$jsonFilePath = './users.json';

// Verificar se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Receber os dados do formulário
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

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

        // Enviar a resposta baseada nas verificações
        if ($userFound) {
            if ($passwordCorrect) {
                echo 'Sucesso! Login realizado.';
            } else {
                echo 'Senha incorreta.';
            }
        } else {
            echo 'Email não encontrado.';
        }
    } else {
        echo 'Erro ao acessar o arquivo de usuários.';
    }
} else {
    echo 'Método de requisição inválido.';
}
?>
