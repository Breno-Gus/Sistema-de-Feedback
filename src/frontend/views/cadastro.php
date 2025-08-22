<?php
include_once '../backend/database/db.php';

try {
    $nome  = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        echo json_encode(["erro" => "Preencha todos os campos"]);
        exit;
    }

    // Hash da senha
    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

    // Inserir usuário
    executarConsulta("INSERT INTO usuarios (nome, email) VALUES (?, ?)", [$nome, $email]);
    $idUsuario = $pdo->lastInsertId();

    // Inserir credenciais
    executarConsulta("INSERT INTO credenciais (id_usuario, senha_hash) VALUES (?, ?)", [$idUsuario, $senhaHash]);

    echo json_encode(["sucesso" => true, "id_usuario" => $idUsuario]);

} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(["erro" => "Email já cadastrado"]);
    } else {
        echo json_encode(["erro" => $e->getMessage()]);
    }
}
