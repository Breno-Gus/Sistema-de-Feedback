<?php
include_once __DIR__ . '/../database/db.php';
include_once __DIR__ . '/../model/sessao.php';

// Se quiser filtrar por usuário logado
$logado = verificarSessao();
$idUsuario = $logado ? $_SESSION['usuario']['id'] : null;

// Forçar download do arquivo CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=avaliacoes.csv');

// Abrir saída
$output = fopen('php://output', 'w');

// Cabeçalho do CSV
fputcsv($output, ['ID', 'Categoria', 'Nota', 'Emoji', 'Comentário', 'Usuário', 'Data']);

// Buscar avaliações do banco
$stmt = $pdo->query("
    SELECT a.id_avaliacao, c.nome_categoria, a.nota, a.emoji, a.conteudo, u.nome AS usuario, a.data_avaliacao
    FROM avaliacoes a
    JOIN categorias_avaliacao c ON a.id_categoria = c.id_categoria
    LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
    ORDER BY a.data_avaliacao DESC
");
$avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Escrever cada linha
foreach ($avaliacoes as $av) {
    fputcsv($output, [
        $av['id_avaliacao'],
        $av['nome_categoria'],
        $av['nota'],
        $av['emoji'],
        $av['conteudo'],
        $av['usuario'] ?? 'Anônimo',
        $av['data_avaliacao']
    ]);
}

fclose($output);
exit;
