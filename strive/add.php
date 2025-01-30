<?php
$aff = $_REQUEST['aff'] ?? null;
$saldo = $_REQUEST['saldo'] ?? null;

if ($aff && $saldo) {
    $url = "https://databackend.koyeb.app/saldo.php?email=$aff&saldo=$saldo";
    file_get_contents($url);
}
?>
