<?php
include_once __DIR__ . '/../../backend/database/db.php';
include_once __DIR__ . '/sessao.php';

try {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        echo json_encode(["erro" => "Preencha todos os campos"]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["erro" => "E-mail inválido"]);
        exit;
    }

    if (strlen($senha) < 6) {
        echo json_encode(["erro" => "A senha deve ter pelo menos 6 caracteres"]);
        exit;
    }

    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

    executarConsulta("INSERT INTO usuarios (nome, email) VALUES (?, ?)", [$nome, $email]);

    global $pdo;
    $idUsuario = $pdo->lastInsertId();

    executarConsulta("INSERT INTO credenciais (id_usuario, senha_hash) VALUES (?, ?)", [$idUsuario, $senhaHash]);

    criarSessao($idUsuario, $nome, $email);

    header("Location: ../../index.php");
    exit;

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(["erro" => "Email já cadastrado"]);
    } else {
        error_log("Erro PDO: " . $e->getMessage());
        echo json_encode(["erro" => "Erro interno no servidor"]);
    }
} catch (Exception $e) {
    error_log("Erro geral: " . $e->getMessage());
    echo json_encode(["erro" => "Erro inesperado"]);
}
