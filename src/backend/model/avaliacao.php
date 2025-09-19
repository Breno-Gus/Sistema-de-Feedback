<?php
session_start();
include_once __DIR__ . '/../database/db.php';
include_once __DIR__ . '/sessao.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $nota = $_POST['nota'] ?? '';
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

    $categoriaExistente = executarConsulta(
        "SELECT COUNT(*) FROM categorias_avaliacao WHERE id_categoria = ?",
        [$id_categoria]
    )->fetchColumn();

    if (!$categoriaExistente) {
        echo json_encode([
            "codigo" => "CATEGORIA_INVALIDA",
            "mensagem" => "Categoria selecionada não existe."
        ]);
        exit;
    }

    if (!in_array($nota, ['1','2','3','4','5'])) {
        echo json_encode([
            "codigo" => "NOTA_INVALIDA",
            "mensagem" => "Escolha uma nota válida de 1 a 5."
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

    // Inserir avaliação
    executarConsulta("
        INSERT INTO avaliacoes (id_usuario, id_categoria, conteudo, nota, anonima, ip_usuario, data_avaliacao)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ", [$usuarioId, $id_categoria, $comentario, $nota, $anonima, $ipHash]);

    // Mensagem diferenciada para anônimos
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
