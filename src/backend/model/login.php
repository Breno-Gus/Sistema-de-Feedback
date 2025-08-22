<?php
session_start();
include_once __DIR__ . '/../../backend/database/db.php';
include_once __DIR__ . '/sessao.php';

try {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        echo json_encode(["erro" => "Preencha todos os campos"]);
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
        echo json_encode(["erro" => "Email ou senha inválidos"]);
        exit;
    }

    // Criar sessão imediata
    criarSessao($usuario['id_usuario'], $usuario['nome'], $email);

    // Atualizar último login
    executarConsulta("UPDATE credenciais SET ultimo_login = NOW() WHERE id_usuario = ?", [$usuario['id_usuario']]);

    // Redireciona para index.php
    header("Location: ../../index.php");
    exit;

} catch (PDOException $e) {
    echo json_encode(["erro" => $e->getMessage()]);
}
