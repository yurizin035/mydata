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
$ehWebhook = isset($_POST['transactionId']); // webhook da BSPay envia isso

// === OBTÉM O TOKEN ===
$ch = curl_init("https://api.bspay.co/v2/oauth/token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: application/json",
    "Authorization: Basic MDM1bmV0b184MzYyMjI1NDgxOjVkMDU0YTI1MmE0ZTU3ZWNmNzQ2ZjI4NTU5ZDc0YTZlODM0NWUwMzdmZjIyM2NjZTg5OTgxYmUzYzQ2Yzc2MTE="
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'grant_type' => 'client_credentials'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);

if (!isset($data['access_token'])) {
    echo json_encode([
        "erro" => "Erro ao obter token",
        "http_code" => $httpCode,
        "resposta" => $response
    ]);
    exit;
}

$accessToken = $data['access_token'];

// === INICIALIZA STORAGE LOCAL DE STATUS (simples) ===
$storagePath = __DIR__ . '/pagamentos.json';
$pagamentos = file_exists($storagePath) ? json_decode(file_get_contents($storagePath), true) : [];

// === WEBHOOK BSPAY ===
if ($ehWebhook) {
    $txId = $_POST['transactionId'];
    $pagamentos[$txId] = "paid";
    file_put_contents($storagePath, json_encode($pagamentos));
    echo json_encode(["status" => "ok"]);
    exit;
}

// === CONSULTA ===
if (!$temValue && $temId) {
    $pixId = $_GET['id'];
    $status = isset($pagamentos[$pixId]) ? $pagamentos[$pixId] : "create";
    echo json_encode(["status" => $status]);
    exit;
}

// === GERAR QR CODE ===
if ($temValue) {
    $amount = floatval($_GET['value']);
    $externalId = uniqid("tx_");

    $payload = json_encode([
        "amount" => $amount,
        "external_id" => $externalId,
        "payerQuestion" => "",
        "payer" => [
            "name" => "Pushinpay",
            "document" => "31232970000146",
            "email" => "contato@pushinpay.com.br"
        ],
        "postbackUrl" => "https://SEU_DOMINIO/esse_arquivo.php"
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
        // Salva como "create" inicialmente
        $pagamentos[$data['transactionId']] = "create";
        file_put_contents($storagePath, json_encode($pagamentos));

        echo json_encode([
            "transactionId" => $data['transactionId'],
            "qrcode" => $data['qrcode']
        ]);
    } else {
        echo json_encode([
            "erro" => "Erro ao gerar QR Code",
            "resposta" => $response
        ]);
    }

    exit;
}

echo json_encode("Parâmetros insuficientes");
