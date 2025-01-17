<?php

// Configuração de CORS
$allowed_origins = [
    "https://strivepay.web.app",
    "https://api.pushinpay.com.br"
];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
} else {
    header("HTTP/1.1 403 Forbidden");
    echo "Acesso não permitido.";
    exit;
}

// Recebendo os parâmetros via GET
$email = $_GET['email'] ?? null;
$name = $_GET['name'] ?? null;
$ticket = $_GET['ticket'] ?? null;
$url = $_GET['url'] ?? null;

// Validando se todos os parâmetros foram enviados
if (!$email || !$name || !$ticket || !$url) {
    header("HTTP/1.1 400 Bad Request");
    echo "Parâmetros incompletos.";
    exit;
}

// Caminho do arquivo JSON
$jsonFile = 'users.json';

// Verificando se o arquivo JSON existe
if (!file_exists($jsonFile)) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Arquivo JSON não encontrado.";
    exit;
}

// Carregando o conteúdo do arquivo
$data = json_decode(file_get_contents($jsonFile), true);

// Verificando se o JSON foi carregado corretamente
if (!is_array($data)) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Erro ao processar o arquivo JSON.";
    exit;
}

// Procurando o usuário pelo email
$userIndex = array_search($email, array_column($data, 'email'));

if ($userIndex === false) {
    header("HTTP/1.1 404 Not Found");
    echo "Usuário não encontrado.";
    exit;
}

// Adicionando o produto ao usuário encontrado
$product = [
    'name' => $name,
    'ticket' => $ticket,
    'url' => $url
];
$data[$userIndex]['produtos'][] = $product;

// Salvando as alterações no arquivo JSON
if (file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    header("HTTP/1.1 200 OK");
    echo "Produto adicionado com sucesso.";
} else {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Erro ao salvar os dados.";
}
?>
