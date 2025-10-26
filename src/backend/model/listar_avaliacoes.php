<?php
session_start();
include_once __DIR__ . '/../db.php';

$logado = isset($_SESSION['usuario']);
$idUsuario = $logado ? $_SESSION['usuario']['id'] : null;

$limite = 100; // pegar até 100 avaliações
$offset = 0;

// Condição base — inclui avaliações anônimas e exclui apenas do usuário logado
$condicaoUsuario = $logado ? "WHERE (a.id_usuario IS NULL OR a.id_usuario <> :idUsuario)" : "";

$sql = "
    SELECT a.id_avaliacao,
           c.nome_categoria,
           a.conteudo,
           a.nota,
           a.emoji,
           a.data_avaliacao,
           u.nome AS nome_usuario
    FROM avaliacoes a
    JOIN categorias_avaliacao c ON a.id_categoria = c.id_categoria
    LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
    $condicaoUsuario
    ORDER BY a.data_avaliacao DESC
    LIMIT :limite OFFSET :offset
";

$stmt = $pdo->prepare($sql);
if ($logado) $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
$stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($avaliacoes);
 