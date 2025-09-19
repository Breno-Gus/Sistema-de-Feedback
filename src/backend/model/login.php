<?php
session_start();
include_once __DIR__ . '/../../backend/database/db.php';
include_once __DIR__ . '/sessao.php';

header('Content-Type: application/json; charset=utf-8');

if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("CSRF token inválido!");
}

try {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        echo json_encode([
            "codigo" => "CAMPOS_VAZIOS",
            "mensagem" => "Preencha todos os campos."
        ]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "codigo" => "EMAIL_INVALIDO",
            "mensagem" => "Formato de email inválido."
        ]);
        exit;
    }

    $stmt = executarConsulta("
        SELECT u.id_usuario, u.nome, c.senha_hash
        FROM usuarios u
        INNER JOIN credenciais c ON u.id_usuario = c.id_usuario
        WHERE u.email = ?
    ", [$email]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
        echo json_encode([
            "codigo" => "LOGIN_INVALIDO",
            "mensagem" => "Email ou senha inválidos."
        ]);
        exit;
    }

    // 🔹 Cria sessão no PHP e no banco
    criarSessao($usuario['id_usuario'], $usuario['nome'], $email);

    // 🔹 Atualiza último login
    executarConsulta("UPDATE credenciais SET ultimo_login = NOW() WHERE id_usuario = ?", [$usuario['id_usuario']]);

    // 🔹 Retorna JSON de sucesso
    echo json_encode([
        "codigo" => "SUCESSO",
        "mensagem" => "Login realizado com sucesso!"
    ]);
    exit;

} catch (PDOException $e) {
    error_log("Erro PDO (login): " . $e->getMessage());
    echo json_encode([
        "codigo" => "ERRO_SERVIDOR",
        "mensagem" => "Erro interno no servidor."
    ]);
} catch (Exception $e) {
    error_log("Erro geral (login): " . $e->getMessage());
    echo json_encode([
        "codigo" => "ERRO_INESPERADO",
        "mensagem" => "Erro inesperado."
    ]);
}
