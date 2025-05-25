<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$temValue = isset($_GET['value']);
$temId = isset($_GET['id']);

// Primeiro obtém o token
$ch = curl_init("https://api.bspay.co/v2/oauth/token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: application/json",
    "Authorization: Basic MDM1bmV0b180ODkxODEyMzE2OmJiNGEyOTA4ZDcwYzZhMjkzNmJiNmFkMWU0ZDQ3NDBhYjc3ZWIwZjdiMDg2OTQyNGMyM2U0MjUwODg5OWIzZTU="
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'grant_type' => 'client_credentials'
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (!isset($data['access_token'])) {
    echo json_encode("Erro ao obter token");
    exit;
}

$accessToken = $data['access_token'];

// Se não houver value, mas houver id, faz a consulta da transação
if (!$temValue && $temId) {
    $pixId = $_GET['id'];

    $payload = json_encode([
        "pix_Id" => $pixId
    ]);

    $ch = curl_init("https://api.bspay.co/v2/consult-transaction");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $accessToken",
        "Accept: application/json",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
    exit;
}

// Se houver value, gera um novo QR Code
if ($temValue) {
    $amount = floatval($_GET['value']);

    $payload = json_encode([
        "amount" => $amount,
        "postbackUrl" => "https://teste.com",
        "payer" => [
            "name" => "Pushinpay",
            "document" => "31232970000146",
            "email" => "contato@pushinpay.com.br"
        ]
    ]);

    $ch = curl_init("https://api.bspay.co/v2/pix/qrcode");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $accessToken",
        "Accept: application/json",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['qrcode']) && isset($data['transactionId'])) {
        echo json_encode([
            "transactionId" => $data['transactionId'],
            "qrcode" => $data['qrcode']
        ]);
    } else {
        echo json_encode("Erro ao gerar QR Code");
    }

    exit;
}

// Caso nenhum dos dois parâmetros esteja presente
echo json_encode("Parâmetros insuficientes");
