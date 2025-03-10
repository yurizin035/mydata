<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if (!isset($_GET['key']) || $_GET['key'] !== "Jm15$") {
    header("HTTP/1.1 403 Forbidden");
    echo json_encode(["erro" => "Acesso não permitido."]);
    exit;
}

if (!isset($_GET['email']) || !isset($_GET['valor'])) {
    echo json_encode(["erro" => "Parâmetros 'email' e 'valor' são obrigatórios."]);
    exit;
}

$email = $_GET['email'];
$valor = intval($_GET['valor']) / 100;

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

        if ($usuario['saldo'] < $valor) {
            echo json_encode(["erro" => "Saldo insuficiente."]);
            exit;
        }

        $usuario['saldo'] -= $valor;

        file_put_contents($arquivo, json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo json_encode(["mensagem" => "Saque realizado com sucesso."]);
        exit;
    }
}

echo json_encode(["erro" => "Usuário não encontrado."]);
?>
