<?php
include_once __DIR__ . '/../database/db.php';
date_default_timezone_set('America/Sao_Paulo');
/**
 * Cria uma sessão para o usuário no PHP e no banco
 */
function criarSessao($usuarioId, $nome, $email) {
    session_regenerate_id(true);
    $token = bin2hex(random_bytes(32));
    $expiracao = date("Y-m-d H:i:s", strtotime("+30 minutes"));

    // Inserir sessão no banco
    executarConsulta("INSERT INTO sessoes (id_usuario, token, expiracao) VALUES (?, ?, ?)", [
        $usuarioId, $token, $expiracao
    ]);

    // Criar sessão PHP
    $_SESSION['usuario'] = [
        "id" => $usuarioId,
        "nome" => $nome,
        "email" => $email
    ];
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['expira'] = time() + (30 * 60);
    $_SESSION['token'] = $token;
}

/**
 * Verifica se a sessão está ativa e válida
 */
function verificarSessao() {
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
        return false;
    }

    $stmt = executarConsulta("
        SELECT id_sessao, expiracao
        FROM sessoes
        WHERE token = ? AND id_usuario = ?
    ", [$_SESSION['token'], $_SESSION['usuario']['id']]);

    $sessao = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$sessao || strtotime($sessao['expiracao']) < time()) {
        session_unset();
        session_destroy();
        return false;
    }

    // Atualiza expiração
    $novaExpiracao = date("Y-m-d H:i:s", strtotime("+30 minutes"));
    executarConsulta("UPDATE sessoes SET expiracao = ? WHERE id_sessao = ?", [$novaExpiracao, $sessao['id_sessao']]);
    $_SESSION['expira'] = time() + (30 * 60);
    return true;
}

/**
 * Encerra a sessão do usuário
 */
function encerrarSessao() {
    if (isset($_SESSION['token'])) {
        executarConsulta("DELETE FROM sessoes WHERE token = ?", [$_SESSION['token']]);
    }
    session_unset();
    session_destroy();
}

function gerarTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verificarTokenCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

