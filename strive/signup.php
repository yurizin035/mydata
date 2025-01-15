<?php
// Permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Caminho para o arquivo JSON
$file_path = 'users.json';

// Verifica se o arquivo existe
if (file_exists($file_path)) {
    // Lê o conteúdo do arquivo
    $users = json_decode(file_get_contents($file_path), true);
} else {
    // Se o arquivo não existir, cria um array vazio
    $users = [];
}

// Verifica se foi feito um POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtém os dados enviados via POST
    $nome = isset($_POST['nome']) ? $_POST['nome'] : '';
    $whatsapp = isset($_POST['whatsapp']) ? $_POST['whatsapp'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';

    // Se os dados necessários não foram enviados, retorna erro
    if (empty($nome) || empty($whatsapp) || empty($email) || empty($senha)) {
        echo json_encode(['error' => 'Dados incompletos']);
        exit;
    }

    // Verifica se o whatsapp ou o email já estão em uso
    foreach ($users as $user) {
        if ($user['whatsapp'] == $whatsapp) {
            echo json_encode(['error' => 'O WhatsApp já está em uso']);
            exit;
        }

        if ($user['email'] == $email) {
            echo json_encode(['error' => 'O Email já está em uso']);
            exit;
        }
    }

    // Determina o próximo ID com base no último usuário
    $next_id = 1;
    if (!empty($users)) {
        $last_user = end($users);
        $next_id = $last_user['id'] + 1;
    }

    // Dados do novo usuário
    $new_user = [
        'id' => $next_id,
        'nome' => $nome,
        'whatsapp' => $whatsapp,
        'email' => $email,
        'senha' => $senha,
        'receita' => 0,
        'retido' => 0,
        'pendente' => 0,
        'saques' => 0,
        'vendas' => 0,
        'conversao' => 0,
        'reembolso' => 0,
        'faturamento' => [
            'hoje' => ['madrugada' => 0, 'manhã' => 0, 'tarde' => 0, 'noite' => 0],
            'ontem' => ['madrugada' => 0, 'manhã' => 0, 'tarde' => 0, 'noite' => 0],
            '7dias' => ['segunda' => 0, 'terça' => 0, 'quarta' => 0, 'quinta' => 0, 'sexta' => 0, 'sabado' => 0, 'domingo' => 0],
            '30dias' => ['0-4' => 0, '4-8' => 0, '8-16' => 0, '16-20' => 0, '20-24' => 0, '24-28' => 0, '30' => 0],
            '90dias' => ['0-30' => 0, '31-60' => 0, '61-90' => 0],
            'periodotodo' => ['jan' => 0, 'fev' => 0, 'mar' => 0, 'abr' => 0, 'mai' => 0, 'jun' => 0, 'jul' => 0, 'ago' => 0, 'set' => 0, 'out' => 0, 'nov' => 0, 'dez' => 0]
        ],
        'produtos' => [],
        'clientes' => [],
        'transacoes' => []
    ];

    // Adiciona o novo usuário ao array de usuários
    $users[] = $new_user;

    // Salva os dados de volta no arquivo JSON
    if (file_put_contents($file_path, json_encode($users, JSON_PRETTY_PRINT))) {
        echo json_encode(['success' => 'Usuário criado com sucesso']);
    } else {
        echo json_encode(['error' => 'Erro ao salvar os dados']);
    }
}
?>