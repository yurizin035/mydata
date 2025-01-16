<?php
$allowed_origin = "https://strivepay.web.app";

if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] === $allowed_origin) {
    header("Access-Control-Allow-Origin: " . $allowed_origin); 
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
} else {
    header("HTTP/1.1 403 Forbidden");
    echo "Acesso não permitido.";
    exit;
}

// Recebe os parâmetros via URL
$email = isset($_GET['email']) ? $_GET['email'] : null;
$saldo = isset($_GET['saldo']) ? floatval($_GET['saldo']) : 0;

// Verifica se os parâmetros estão presentes
if (!$email || $saldo <= 0) {
    echo "Parâmetros inválidos.";
    exit;
}

// Lê o arquivo JSON de usuários
$json = file_get_contents('users.json');
$users = json_decode($json, true);

// Obtém a data e hora atual em horário de Brasília
date_default_timezone_set('America/Sao_Paulo');
$dia_da_semana = date('l'); // Ex: Segunda-feira
$hora_atual = date('G'); // Hora atual (0 a 23)
$mes_atual = strtolower(date('M')); // Mês atual abreviado (jan, fev, mar, etc.)

// Percorre os usuários e encontra o usuário com o email correspondente
foreach ($users as &$user) {
    if ($user['email'] === $email) {
        // Atualiza o saldo de receita
        $user['receita'] += $saldo;

        // Atualiza faturamento de hoje, considerando o período atual (madrugada, manhã, tarde, noite)
        if ($hora_atual >= 0 && $hora_atual < 6) {
            $periodo = 'madrugada';
        } elseif ($hora_atual >= 6 && $hora_atual < 12) {
            $periodo = 'manhã';
        } elseif ($hora_atual >= 12 && $hora_atual < 18) {
            $periodo = 'tarde';
        } else {
            $periodo = 'noite';
        }

        $user['faturamento']['hoje'][$periodo] += $saldo;

        // Atualiza faturamento para os últimos 7 dias
        $user['faturamento']['7dias'][strtolower($dia_da_semana)] += $saldo;

        // Atualiza faturamento para os últimos 30 dias (distribui de acordo com as faixas horárias)
        if ($hora_atual >= 0 && $hora_atual < 4) {
            $user['faturamento']['30dias']['0-4'] += $saldo;
        } elseif ($hora_atual >= 4 && $hora_atual < 8) {
            $user['faturamento']['30dias']['4-8'] += $saldo;
        } elseif ($hora_atual >= 8 && $hora_atual < 16) {
            $user['faturamento']['30dias']['8-16'] += $saldo;
        } elseif ($hora_atual >= 16 && $hora_atual < 20) {
            $user['faturamento']['30dias']['16-20'] += $saldo;
        } elseif ($hora_atual >= 20 && $hora_atual < 24) {
            $user['faturamento']['30dias']['20-24'] += $saldo;
        }

        // Atualiza faturamento para os últimos 90 dias
        if ($hora_atual >= 0 && $hora_atual < 30) {
            $user['faturamento']['90dias']['0-30'] += $saldo;
        } elseif ($hora_atual >= 30 && $hora_atual < 60) {
            $user['faturamento']['90dias']['31-60'] += $saldo;
        } else {
            $user['faturamento']['90dias']['61-90'] += $saldo;
        }

        // Atualiza faturamento para o período todo (mês atual)
        $user['faturamento']['periodotodo'][$mes_atual] += $saldo;

        // Atualiza o arquivo JSON com as mudanças
        file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
        echo "Saldo atualizado com sucesso.";
        exit;
    }
}

echo "Usuário não encontrado.";
exit;
?>
