<?php
session_start(); // 🔹 essencial para manipular a sessão
include_once __DIR__ . '/../../backend/model/sessao.php';

try {
    encerrarSessao(); // 🔹 remove do PHP e do banco
} catch (Exception $e) {
    error_log("Erro ao encerrar sessão: " . $e->getMessage());
    // opcional: podemos mostrar uma mensagem, mas aqui só redirecionamos
}

// Redireciona para a página inicial
header("Location: ../../index.php");
exit;
