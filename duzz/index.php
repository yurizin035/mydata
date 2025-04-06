<?php

// Definir os cabeçalhos CORS para permitir qualquer origem
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

function cashIn() {
    // Verifica se o valor foi passado via GET ou POST
    $valor = isset($_GET['valor']) ? $_GET['valor'] : (isset($_POST['valor']) ? $_POST['valor'] : null);

    if (!$valor) {
        echo 'Valor não fornecido.';
        return;
    }

    $url = 'https://api.pushinpay.com.br/api/pix/cashIn'; // URL fictícia baseada no exemplo
    $token = '4256|fuAL7AgoeQd2Ik5OW8b8cYz8qaMCPmwAudqhWxdk29b956d1'; // Token do primeiro exemplo
    $webhookUrl = 'http://teste.com'; // URL de webhook para notificações

    // Remove vírgulas do valor, caso existam
    $valor = str_replace(',', '', $valor);

    // Dados a serem enviados na requisição
    $dados = [
        'value' => $valor,
        'webhook_url' => $webhookUrl
    ];

    // Inicia a requisição cURL
    $ch = curl_init($url);

    // Configurações cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));

    // Executa a requisição e armazena a resposta
    $resposta = curl_exec($ch);

    // Verifica se houve erro na requisição
    if (curl_errno($ch)) {
        echo 'Erro na requisição: ' . curl_error($ch);
    } else {
        // Retorna a resposta bruta da API
        echo $resposta;
    }

    // Fecha a conexão cURL
    curl_close($ch);
}

cashIn(); // Chama a função para realizar o cash in

?>