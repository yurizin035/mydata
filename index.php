<?php
// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém os dados do formulário
    $nome = $_POST['name'];
    $whatsapp = $_POST['whatsapp'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $id = 0;
    $saldo = 50;  // Saldo padrão

    // Caminho do arquivo JSON
    $filePath = 'users.json';

    // Verifica se o arquivo já existe e carrega os dados existentes
    if (file_exists($filePath)) {
        $jsonData = file_get_contents($filePath);
        $users = json_decode($jsonData, true);
        $id = count($users) + 1;  // Novo ID sequencial
    } else {
        $users = [];
        $id = 1;  // ID inicial
    }

    // Verifica se o e-mail ou WhatsApp já estão em uso
    $emailExists = false;
    $whatsappExists = false;
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            $emailExists = true;
        }
        if ($user['whatsapp'] === $whatsapp) {
            $whatsappExists = true;
        }
    }

    // Exibe a mensagem de erro correta, se necessário
    if ($emailExists && $whatsappExists) {
        echo "Este e-mail e WhatsApp já estão em uso.";
    } elseif ($emailExists) {
        echo "Este e-mail já está em uso.";
    } elseif ($whatsappExists) {
        echo "Este WhatsApp já está em uso.";
    } else {
        // Cria um novo usuário
        $newUser = [
            'id' => $id,
            'nome' => $nome,
            'whatsapp' => $whatsapp,
            'email' => $email,
            'senha' => $senha,
            'saldo' => $saldo
        ];

        // Adiciona o novo usuário ao array
        $users[] = $newUser;

        // Salva os dados de volta no arquivo JSON
        file_put_contents($filePath, json_encode($users, JSON_PRETTY_PRINT));

        // Exibe uma mensagem de sucesso
        echo "Usuário registrado com sucesso!";
    }
}
?>
