<?php

// Definir os cabeçalhos CORS para permitir qualquer origem
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

function saque() {
    // Verifica se o valor foi passado via GET ou POST
    $valor = isset($_GET['valor']) ? $_GET['valor'] : (isset($_POST['valor']) ? $_POST['valor'] : null);

    if (!$valor) {
        echo 'Valor não fornecido.';
        return;
    }

    $url = 'https://api.pushinpay.com.br/api/pix/cashOut';
    $token = '4256|fuAL7AgoeQd2Ik5OW8b8cYz8qaMCPmwAudqhWxdk29b956d1';  // Substitua com seu token de autorização
    $pixKey = '02217659642';  // Chave Pix definida

    // Remove as vírgulas do valor
    $valor = str_replace(',', '', $valor);

    // Dados a serem enviados na requisição
    $dados = [
        'value' => $valor,
        'pix_key_type' => 'national_registration',
        'pix_key' => $pixKey,
        'webhook_url' => 'https://seusite.com/'
    ];

    // Inicia a requisição cURL
    $ch = curl_init($url);
    
    // Configurações cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json',
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));

    // Executa a requisição e armazena a resposta
    $resposta = curl_exec($ch);

    // Verifica se houve erro na requisição
    if(curl_errno($ch)) {
        echo 'Erro na requisição: ' . curl_error($ch);
    } else {
        // Converte a resposta para um array associativo
        $respostaJson = json_decode($resposta, true);

        // Processa a resposta, você pode adicionar mais lógica aqui se necessário
        // Exemplo: echo "Chave: " . $respostaJson['pix_key'];
    }

    // Fecha a conexão cURL
    curl_close($ch);
}

saque();  // Chama a função para realizar o saque

?>
