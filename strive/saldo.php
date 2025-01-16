<?php
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

if (!isset($_GET['email']) || !isset($_GET['saldo'])) {
    echo json_encode(["erro" => "Parâmetros 'email' e 'saldo' são obrigatórios."]);
    exit;
}

$email = $_GET['email'];
$saldo = intval($_GET['saldo']) / 100;
$timezone = new DateTimeZone('America/Sao_Paulo');
$dataAtual = new DateTime('now', $timezone);

$arquivo = 'users.json';
if (!file_exists($arquivo)) {
    echo json_encode(["erro" => "Arquivo users.json não encontrado."]);
    exit;
}

$usuarios = json_decode(file_get_contents($arquivo), true);

$usuarioEncontrado = false;
foreach ($usuarios as &$usuario) {
    if ($usuario['email'] === $email) {
        $usuarioEncontrado = true;

        $usuario['receita'] += $saldo;

        $hora = intval($dataAtual->format('H'));
        if ($hora >= 0 && $hora < 6) $periodo = 'madrugada';
        elseif ($hora >= 6 && $hora < 12) $periodo = 'manhã';
        elseif ($hora >= 12 && $hora < 18) $periodo = 'tarde';
        else $periodo = 'noite';

        $usuario['faturamento'][0]['hoje'][$periodo] += $saldo;

        $diaSemana = strtolower($dataAtual->format('l'));
        $diasSemana = [
            'monday' => 'segunda',
            'tuesday' => 'terça',
            'wednesday' => 'quarta',
            'thursday' => 'quinta',
            'friday' => 'sexta',
            'saturday' => 'sabado',
            'sunday' => 'domingo'
        ];
        $usuario['faturamento'][0]['7dias'][$diasSemana[$diaSemana]] += $saldo;

        $dias30 = [
            '0-4' => range(0, 4),
            '4-8' => range(5, 8),
            '8-16' => range(9, 16),
            '16-20' => range(17, 20),
            '20-24' => range(21, 24),
            '24-28' => range(25, 28),
            '30' => [30]
        ];
        foreach ($dias30 as $chave => $intervalo) {
            if (in_array(intval($dataAtual->format('d')), $intervalo)) {
                $usuario['faturamento'][0]['30dias'][$chave] += $saldo;
            }
        }

        $diaAno = intval($dataAtual->format('z')) + 1;
        if ($diaAno <= 30) $usuario['faturamento'][0]['90dias']['0-30'] += $saldo;
        elseif ($diaAno <= 60) $usuario['faturamento'][0]['90dias']['31-60'] += $saldo;
        else $usuario['faturamento'][0]['90dias']['61-90'] += $saldo;

        $mesAtual = strtolower($dataAtual->format('M'));
        $meses = [
            'jan', 'fev', 'mar', 'abr', 'mai', 'jun',
            'jul', 'ago', 'set', 'out', 'nov', 'dez'
        ];
        $usuario['faturamento'][0]['periodotodo'][$mesAtual] += $saldo;

        break;
    }
}

if ($usuarioEncontrado) {
    file_put_contents($arquivo, json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode(["mensagem" => "Saldo atualizado com sucesso."]);
} else {
    echo json_encode(["erro" => "Usuário não encontrado."]);
}
?>
