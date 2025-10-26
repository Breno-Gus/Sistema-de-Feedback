<?php
require_once __DIR__ . '/../../../vendor/autoload.php'; // Ajuste se não usar composer

include_once __DIR__ . '/../database/db.php';
include_once __DIR__ . '/../model/sessao.php';

// Se quiser filtrar por usuário logado
$logado = verificarSessao();
$idUsuario = $logado ? $_SESSION['usuario']['id'] : null;

// Buscar avaliações
$sql = "SELECT a.id_avaliacao, c.nome_categoria, a.conteudo, a.nota, a.emoji, a.data_avaliacao, u.nome AS nome_usuario
        FROM avaliacoes a
        JOIN categorias_avaliacao c ON a.id_categoria = c.id_categoria
        LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Criar PDF
$pdf = new TCPDF();
$pdf->SetCreator('Sistema de Feedback');
$pdf->SetAuthor('Sistema de Feedback');
$pdf->SetTitle('Relatório de Avaliações');

// Margens e fonte
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

// Cabeçalho manual
$html = '<h1>Relatório de Avaliações</h1>';
$html .= '<table border="1" cellpadding="5">
            <thead>
              <tr>
                <th>ID</th>
                <th>Categoria</th>
                <th>Nota/Emoji</th>
                <th>Comentário</th>
                <th>Usuário</th>
                <th>Data</th>
              </tr>
            </thead>
            <tbody>';

foreach ($avaliacoes as $av) {
    $nota = $av['nota'] ? str_repeat('★', $av['nota']) : ($av['emoji'] ?? '');
    $conteudo = htmlspecialchars($av['conteudo'] ?? '');
    $nomeUsuario = htmlspecialchars($av['nome_usuario'] ?? 'Anônimo');
    $data = date('d/m/Y H:i', strtotime($av['data_avaliacao']));
    
    $html .= "<tr>
                <td>{$av['id_avaliacao']}</td>
                <td>{$av['nome_categoria']}</td>
                <td>{$nota}</td>
                <td>{$conteudo}</td>
                <td>{$nomeUsuario}</td>
                <td>{$data}</td>
              </tr>";
}

$html .= '</tbody></table>';

// Adicionar HTML ao PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Forçar download
$pdf->Output('avaliacoes.pdf', 'D');
