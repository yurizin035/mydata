<?php
header("Access-Control-Allow-Origin: *"); // Permite qualquer origem
header("Access-Control-Allow-Methods: GET, OPTIONS"); // Permite GET e OPTIONS
header("Access-Control-Allow-Headers: Content-Type"); // Permite o cabeçalho Content-Type

$jsonData = file_get_contents("users.json");
echo $jsonData;
?>
