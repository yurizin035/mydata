<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (!isset($_GET['value'])) {
    echo json_encode("Erro");
    exit;
}

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
    "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiIyMmUyOTRjOWJiYjUzMjdkZDFjZGU1YjRjNWM2YzUwMCIsInN1YiI6IjBmMDc1Yzc5OWRhOWFjOTczZTFkYmM4ZWEzYmNjYTE1IiwiaWF0IjoxNzQ2MDk3MzE0LCJleHAiOjE3NDYwOTkxMTR9.cl6_k_LCvvuwKcT92N69Lqi8HuSKnMExzK2Z4WW0MTU",
    "Accept: application/json",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (isset($data['qrcode'])) {
    echo json_encode($data['qrcode']);
} else {
    echo json_encode("Erro");
}
