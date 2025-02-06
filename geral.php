<?php
// Permite CORS de qualquer origem
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// Obtém os dados via GET ou POST
$user = $_REQUEST['user'] ?? null;
$email = $_REQUEST['email'] ?? null;
$saldo = isset($_REQUEST['saldo']) ? floatval($_REQUEST['saldo']) : null;

if (!$user || !$email || $saldo === null) {
    echo json_encode(["error" => "Parâmetros faltando"]);
    exit;
}

// Ajustando saldo para a terceira requisição (-50)
$saldoAdjusted = $saldo - 50;

// URLs das requisições
$urls = [
    "https://databackend.koyeb.app/saldo.php?email=$user&saldo=$saldo",
    "https://databackend.koyeb.app/strive/saldo.php?email=$email&saldo=$saldo&key=Jm15$",
    "https://databackend.koyeb.app/mysaque.php?valor=$saldoAdjusted"
];

// Função para fazer a requisição
function fetch_url($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// Fazendo as requisições e armazenando as respostas
$responses = [];
foreach ($urls as $index => $url) {
    $responses[] = [
        "url" => $url,
        "response" => fetch_url($url)
    ];
}

// Retorna a resposta em JSON
echo json_encode(["status" => "success", "data" => $responses]);
?>
