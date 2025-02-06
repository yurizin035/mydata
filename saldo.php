<?php
// Definir os headers CORS para permitir acesso de qualquer site
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// Coletar parâmetros da URL
$email = isset($_GET['email']) ? $_GET['email'] : null;
$saldo = isset($_GET['saldo']) ? $_GET['saldo'] : null;

// Verificar se os parâmetros estão presentes
if ($email && $saldo) {
    // Carregar o arquivo JSON
    $usersFile = 'users.json';
    if (file_exists($usersFile)) {
        $usersData = json_decode(file_get_contents($usersFile), true);
        
        // Converter saldo para formato correto
        $saldo = intval($saldo); // Garantir que seja inteiro
        $saldo = $saldo / 100; // Converter para centavos

        // Iterar sobre os usuários e buscar pelo email
        foreach ($usersData as &$user) {
            if ($user['email'] == $email) {
                // Adicionar o saldo ao valor existente
                $user['saldo'] += $saldo;

                // Salvar os dados de volta no arquivo JSON
                file_put_contents($usersFile, json_encode($usersData, JSON_PRETTY_PRINT));
                echo "Saldo atualizado com sucesso!";
                exit;
            }
        }
        
        // Caso não encontre o usuário
        echo "Usuário não encontrado!";
    } else {
        echo "Arquivo 'users.json' não encontrado!";
    }
} else {
    echo "Parâmetros 'email' e 'saldo' são obrigatórios!";
}
?>
