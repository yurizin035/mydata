<?php
header("Access-Control-Allow-Origin: https://strivepay.web.app"); // Permite apenas origem do site strivepay.web.app
header("Access-Control-Allow-Methods: GET, OPTIONS"); // Permite GET e OPTIONS
header("Access-Control-Allow-Headers: Content-Type"); // Permite o cabeçalho Content-Type

$jsonData = file_get_contents("users.json");
echo $jsonData;
?>
