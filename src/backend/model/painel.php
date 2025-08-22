<?php
include_once __DIR__ . '/sessao.php';

if (!verificarSessao()) {
    header("Location: index.php");
    exit;
}
?>