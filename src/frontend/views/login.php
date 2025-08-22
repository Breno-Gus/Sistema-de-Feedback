<?php
include_once __DIR__ . '/../../backend/database/db.php';

try {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        echo json_encode(["erro" => "Preencha todos os campos"]);
        exit;
    }

    // Buscar usuário
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

    // Criar token
    $token = bin2hex(random_bytes(32));
    $expiracao = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Registrar sessão
    executarConsulta("INSERT INTO sessoes (id_usuario, token, expiracao) VALUES (?, ?, ?)", [
        $usuario['id_usuario'], $token, $expiracao
    ]);

    // Atualizar último login
    executarConsulta("UPDATE credenciais SET ultimo_login = NOW() WHERE id_usuario = ?", [
        $usuario['id_usuario']
    ]);

    echo json_encode([
        "sucesso" => true,
        "usuario" => [
            "id" => $usuario['id_usuario'],
            "nome" => $usuario['nome']
        ],
        "token" => $token,
        "expira_em" => $expiracao
    ]);

} catch (PDOException $e) {
    echo json_encode(["erro" => $e->getMessage()]);
}

?>