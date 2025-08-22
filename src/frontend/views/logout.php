<?php
include_once __DIR__ . '/../../backend/model/sessao.php';

try {
    encerrarSessao();
} catch (Exception $e) {
    // Log do erro opcional: error_log($e->getMessage());
    // Podemos mostrar uma mensagem ou apenas redirecionar mesmo
}

header("Location: ../../index.php");
exit;
