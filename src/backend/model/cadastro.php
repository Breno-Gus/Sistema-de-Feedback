<?php
include_once __DIR__ . '/../../backend/database/db.php';
include_once __DIR__ . '/sessao.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    // 🔹 Validações
    if (empty($nome) || empty($email) || empty($senha)) {
        echo json_encode([
            "codigo" => "CAMPOS_VAZIOS",
            "mensagem" => "Preencha todos os campos."
        ]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "codigo" => "EMAIL_INVALIDO",
            "mensagem" => "E-mail inválido."
        ]);
        exit;
    }

    if (strlen($senha) < 6) {
        echo json_encode([
            "codigo" => "SENHA_FRACA",
            "mensagem" => "A senha deve ter pelo menos 6 caracteres."
        ]);
        exit;
    }

    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

    // 🔹 Insere usuário
    executarConsulta("INSERT INTO usuarios (nome, email) VALUES (?, ?)", [$nome, $email]);

    global $pdo;
    $idUsuario = $pdo->lastInsertId();

    // 🔹 Insere credenciais
    executarConsulta("INSERT INTO credenciais (id_usuario, senha_hash) VALUES (?, ?)", [$idUsuario, $senhaHash]);

    // 🔹 Cria sessão
    criarSessao($idUsuario, $nome, $email);

    echo json_encode([
        "codigo" => "SUCESSO",
        "mensagem" => "Usuário cadastrado com sucesso!"
    ]);
    exit;

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode([
            "codigo" => "EMAIL_DUPLICADO",
            "mensagem" => "Este e-mail já está cadastrado."
        ]);
    } else {
        error_log("Erro PDO: " . $e->getMessage());
        echo json_encode([
            "codigo" => "ERRO_SERVIDOR",
            "mensagem" => "Erro interno no servidor."
        ]);
    }
} catch (Exception $e) {
    error_log("Erro geral: " . $e->getMessage());
    echo json_encode([
        "codigo" => "ERRO_INESPERADO",
        "mensagem" => "Erro inesperado."
    ]);
}
