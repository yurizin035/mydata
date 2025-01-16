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

$jsonData = file_get_contents("users.json");
echo $jsonData;
?>
