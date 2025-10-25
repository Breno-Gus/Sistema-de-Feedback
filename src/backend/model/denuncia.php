<?php
session_start();
include_once __DIR__ . '/../../backend/database/db.php';
include_once __DIR__ . '/sessao.php';


header('Content-Type: application/json');

if (!isset($_SESSION['usuario']['id'])) {
    echo json_encode([
        'codigo' => 'ERRO',
        'mensagem' => 'É necessário estar logado para denunciar uma avaliação.'
    ]);
    exit;
}

$idDenunciante = $_SESSION['usuario']['id'];
$dados = json_decode(file_get_contents('php://input'), true);

$idAvaliacao = $dados['id_avaliacao'] ?? null;
$motivo = trim($dados['motivo'] ?? '');

if (!$idAvaliacao || $motivo === '') {
    echo json_encode([
        'codigo' => 'ERRO',
        'mensagem' => 'Dados inválidos para denúncia.'
    ]);
    exit;
}

try {
    // Verificar se o usuário já denunciou essa avaliação
    $check = executarConsulta(
        "SELECT id_denuncia FROM denuncias WHERE id_avaliacao = ? AND id_denunciante = ?",
        [$idAvaliacao, $idDenunciante]
    );

    if ($check->rowCount() > 0) {
        echo json_encode([
            'codigo' => 'ERRO',
            'mensagem' => 'Você já denunciou esta avaliação.'
        ]);
        exit;
    }

    // Inserir denúncia
    executarConsulta(
        "INSERT INTO denuncias (id_avaliacao, id_denunciante, motivo, data_denuncia, status) 
         VALUES (?, ?, ?, NOW(), 'pendente')",
        [$idAvaliacao, $idDenunciante, $motivo]
    );

    echo json_encode([
        'codigo' => 'SUCESSO',
        'mensagem' => 'Denúncia enviada com sucesso!'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'codigo' => 'ERRO',
        'mensagem' => 'Erro ao registrar denúncia: ' . $e->getMessage()
    ]);
}