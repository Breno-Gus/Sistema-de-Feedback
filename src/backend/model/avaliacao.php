<?php
session_start();
include_once __DIR__ . '/../database/db.php';
include_once __DIR__ . '/sessao.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $tipo = $_POST['tipo_avaliacao'] ?? null; // novo
    $nota = $tipo === 'estrela' ? $_POST['nota'] ?? null : null;
    $emoji = $tipo === 'emoji' ? $_POST['emoji'] ?? null : null; // novo
    $comentario = trim($_POST['conteudo'] ?? '');
    $id_categoria = $_POST['id_categoria'] ?? null;

    // Usuário logado ou anônimo
    $usuarioId = verificarSessao() ? $_SESSION['usuario']['id'] : null;
    $anonima = $usuarioId ? 0 : 1;

    // sempre gerar hash do IP, nunca salvar o IP real
    $ipUsuario = $_SERVER['REMOTE_ADDR'] ?? '';
    $ipHash = hash('sha256', $ipUsuario);

    // Validações
    if (!$id_categoria || !is_numeric($id_categoria)) {
        echo json_encode([
            "codigo" => "CATEGORIA_INVALIDA",
            "mensagem" => "Selecione uma categoria válida."
        ]);
        exit;
    }

    if ($tipo === 'estrela' && !in_array($nota, ['1','2','3','4','5'])) {
        echo json_encode([
            "codigo" => "NOTA_INVALIDA",
            "mensagem" => "Escolha uma nota válida de 1 a 5."
        ]);
        exit;
    }

    if ($tipo === 'emoji' && empty($emoji)) {
        echo json_encode([
            "codigo" => "EMOJI_INVALIDO",
            "mensagem" => "Escolha um emoji válido."
        ]);
        exit;
    }

    if (empty($comentario)) {
        $comentario = 'Sem comentário';
    }

    // Limite 1 avaliação por hash/IP/dia apenas para anônimos
    if ($anonima) {
        $hoje = date('Y-m-d');
        $verifica = executarConsulta(
            "SELECT COUNT(*) FROM avaliacoes WHERE ip_usuario = ? AND DATE(data_avaliacao) = ?",
            [$ipHash, $hoje]
        )->fetchColumn();

        if ($verifica > 0) {
            echo json_encode([
                "codigo" => "AVALIACAO_DUPLICADA",
                "mensagem" => "Você só pode enviar uma avaliação por dia."
            ]);
            exit;
        }
    }

    // Inserir avaliação (agora com emoji)
    executarConsulta("
        INSERT INTO avaliacoes (id_usuario, id_categoria, conteudo, nota, emoji, anonima, ip_usuario, data_avaliacao)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ", [$usuarioId, $id_categoria, $comentario, $nota, $emoji, $anonima, $ipHash]);

    echo json_encode([
        "codigo" => "SUCESSO",
        "mensagem" => $anonima ? "Avaliação enviada!" : "Sua avaliação foi registrada com sucesso!"
    ]);

} catch (PDOException $e) {
    error_log("Erro PDO (avaliacao): " . $e->getMessage());
    echo json_encode([
        "codigo" => "ERRO_SERVIDOR",
        "mensagem" => "Erro interno no servidor."
    ]);
} catch (Exception $e) {
    error_log("Erro geral (avaliacao): " . $e->getMessage());
    echo json_encode([
        "codigo" => "ERRO_INESPERADO",
        "mensagem" => "Erro inesperado."
    ]);
}
