<?php
session_start();
include_once __DIR__ . '/../../backend/model/sessao.php';
include_once __DIR__ . '/../../backend/database/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$logado = verificarSessao();
$idUsuario = $logado ? $_SESSION['usuario']['id'] : null;
$nomeUsuario = $logado ? $_SESSION['usuario']['nome'] : 'Anônimo';

// Buscar categorias exceto 'Outros'
$categorias = executarConsulta("
    SELECT id_categoria, nome_categoria 
    FROM categorias_avaliacao 
    WHERE nome_categoria <> 'Outros' 
    ORDER BY nome_categoria ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Buscar categoria 'Outros'
$outrosCategoria = executarConsulta("
    SELECT id_categoria, nome_categoria 
    FROM categorias_avaliacao 
    WHERE nome_categoria = 'Outros'
")->fetch(PDO::FETCH_ASSOC);

// Buscar avaliações do usuário (se logado)
$avaliacoesUsuario = [];
if ($logado) {
    $avaliacoesUsuario = executarConsulta("
        SELECT a.id_avaliacao, c.nome_categoria, a.conteudo, a.nota, a.data_avaliacao
        FROM avaliacoes a
        JOIN categorias_avaliacao c ON a.id_categoria = c.id_categoria
        WHERE a.id_usuario = ?
        ORDER BY a.data_avaliacao DESC
    ", [$idUsuario])->fetchAll(PDO::FETCH_ASSOC);
}

// Pegar página atual
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$limite = 4;
$offset = ($pagina - 1) * $limite;

// Condição base — inclui avaliações anônimas (id_usuario IS NULL) e exclui somente as do usuário logado
$condicaoUsuario = $logado ? "WHERE (a.id_usuario IS NULL OR a.id_usuario <> :idUsuario)" : "";

// Contagem total de avaliações (para paginação)
$sqlCount = "SELECT COUNT(*) AS total FROM avaliacoes a $condicaoUsuario";
$stmtCount = $pdo->prepare($sqlCount);
if ($logado) {
    $stmtCount->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
}
$stmtCount->execute();
$totalAvaliacoes = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

$totalPaginas = ceil($totalAvaliacoes / $limite);

// Buscar as avaliações (mesma condição)
$sqlAvaliacoes = "
    SELECT a.id_avaliacao,
           c.nome_categoria,
           a.conteudo,
           a.nota,
           a.data_avaliacao,
           u.nome AS nome_usuario
    FROM avaliacoes a
    JOIN categorias_avaliacao c ON a.id_categoria = c.id_categoria
    LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
    $condicaoUsuario
    ORDER BY a.data_avaliacao DESC
    LIMIT :limite OFFSET :offset
";
$stmtAval = $pdo->prepare($sqlAvaliacoes);
if ($logado) {
    $stmtAval->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
}
$stmtAval->bindParam(':limite', $limite, PDO::PARAM_INT);
$stmtAval->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmtAval->execute();

$todasAvaliacoes = $stmtAval->fetchAll(PDO::FETCH_ASSOC);



$denunciasUsuario = [];
if ($logado) {
    $stmt = executarConsulta(
        "SELECT id_avaliacao FROM denuncias WHERE id_denunciante = ?",
        [$idUsuario]
    );
    $denunciasUsuario = $stmt->fetchAll(PDO::FETCH_COLUMN); // só pega os IDs
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Avaliação - Feedback Empresas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-to-br from-blue-100 to-green-100 min-h-screen flex flex-col md:flex-row items-center justify-center p-6 gap-8">


<div class="max-w-3xl w-full bg-white rounded-3xl shadow-2xl p-10 space-y-6 border border-gray-200">

  <!-- Título -->
  <h1 class="text-4xl font-extrabold text-gray-800 text-center mb-2 drop-shadow-sm">
    Avaliação <?= $logado ? '' : '(Anônima)' ?>
  </h1>

  <!-- Painel login/logout -->
  <div class="flex justify-end items-center mb-4 space-x-3 text-sm">
    <?php if ($logado): ?>
      <span class="text-gray-700 font-medium">Logado como <strong><?= htmlspecialchars($nomeUsuario) ?></strong></span>
      <a href="logout.php" class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-shadow shadow-sm">Sair</a>
    <?php else: ?>
      <a href="view_login.php" class="px-3 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-shadow shadow-sm">Login</a>
    <?php endif; ?>
  </div>

  <p class="text-gray-600 text-center mb-6">
    Obrigado por participar! Sua opinião é muito importante.
  </p>

  <!-- Formulário de Avaliação -->
  <form id="formAvaliacao" class="space-y-5">
    <!-- Categoria -->
    <div class="flex flex-col">
      <label class="text-gray-700 font-semibold mb-2">Categoria da avaliação</label>
      <select name="id_categoria" required class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
        <option value="">Selecione a categoria</option>
        <?php foreach($categorias as $cat): ?>
          <option value="<?= $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nome_categoria']) ?></option>
        <?php endforeach; ?>
        <?php if ($outrosCategoria): ?>
          <option value="<?= $outrosCategoria['id_categoria'] ?>"><?= htmlspecialchars($outrosCategoria['nome_categoria']) ?></option>
        <?php endif; ?>
      </select>
    </div>

    <!-- Nota -->
    <div class="flex flex-col">
      <label class="text-gray-700 font-semibold mb-2">Nota do serviço (1 a 5)</label>
      <select name="nota" required class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-green-400 focus:border-transparent transition">
        <option value="">Selecione</option>
        <?php for($i=1; $i<=5; $i++): ?>
          <option value="<?= $i ?>"><?= $i ?> - <?php 
            echo match($i) {
              1 => 'Péssimo',
              2 => 'Ruim',
              3 => 'Regular',
              4 => 'Bom',
              5 => 'Excelente',
            };
          ?></option>
        <?php endfor; ?>
      </select>
    </div>

    <!-- Comentário -->
    <div class="flex flex-col">
      <label class="text-gray-700 font-semibold mb-2">Comentário (opcional)</label>
      <textarea name="conteudo" rows="5" class="w-full border border-gray-300 rounded-xl p-3 resize-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition" placeholder="Deixe sua opinião..."></textarea>
    </div>

    <!-- Botão -->
    <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-green-400 text-white font-bold py-3 rounded-2xl shadow-lg hover:scale-105 transform transition-all">
      Enviar Avaliação
    </button>
  </form>

<?php if ($logado): ?>
  <!-- Dropdown Minhas Avaliações -->
  <h1 id="toggleAvaliacoes" class="flex justify-between items-center cursor-pointer text-2xl font-bold text-black mt-6 hover:text-gray-800 transition">
      Minhas Avaliações
      <span id="iconSeta" class="text-xl">▼</span>
  </h1>

  <div id="listaAvaliacoes" class="mt-4 hidden space-y-3">
      <?php if (empty($avaliacoesUsuario)): ?>
          <p class="text-gray-600">Você ainda não possui avaliações.</p>
      <?php else: ?>
          <?php foreach($avaliacoesUsuario as $av): ?>
              <div class="border border-gray-300 rounded-xl p-4 flex justify-between items-start bg-gray-50">
                  <div>
                      <p class="font-semibold"><?= htmlspecialchars($av['nome_categoria']) ?> - Nota: <?= $av['nota'] ?></p>
                      <p class="text-gray-700"><?= htmlspecialchars($av['conteudo']) ?></p>
                      <p class="text-gray-400 text-sm"><?= date('d/m/Y H:i', strtotime($av['data_avaliacao'])) ?></p>
                  </div>
                  <div class="flex flex-col gap-2 ml-4">
                      <button class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" onclick="editarAvaliacao(<?= $av['id_avaliacao'] ?>)">Editar</button>
                      <button class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition" onclick="removerAvaliacao(<?= $av['id_avaliacao'] ?>)">Remover</button>
                  </div>
              </div>
          <?php endforeach; ?>
      <?php endif; ?>
  </div>
<?php endif; ?>


</div>

<!-- BLOCO QUE MOSTRA AS AVALIAÇÕES DO SITE-->
<div class="max-w-3xl w-full bg-white rounded-3xl shadow-2xl p-10 space-y-6 border border-gray-200">
  <h2 class="text-3xl font-extrabold text-gray-800 text-center mb-4 drop-shadow-sm">
    Todas as Avaliações
  </h2>

  <div id="todasAvaliacoes" class="space-y-4 h-[550px] overflow-y-auto pr-2">
    <?php if (empty($todasAvaliacoes)): ?>
      <p class="text-gray-600 text-center">Nenhuma avaliação encontrada.</p>
    <?php else: ?>

        <?php foreach ($todasAvaliacoes as $av): ?>
            <?php 
                $jaDenunciada = $logado && in_array($av['id_avaliacao'], $denunciasUsuario);
                $classeFundo = $jaDenunciada ? 'bg-red-100 border-red-500' : 'bg-gray-50 border-gray-300';
            ?>
            <div class="border rounded-xl p-4 <?= $classeFundo ?> shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold text-lg text-gray-800">
                            <?= htmlspecialchars($av['nome_categoria']) ?> - 
                            <span class="text-yellow-500">★ <?= $av['nota'] ?></span>
                        </p>
                        <p class="text-gray-700 mt-1"><?= htmlspecialchars($av['conteudo']) ?></p>
                        <p class="text-gray-500 text-sm mt-1">
                            Por: <?= htmlspecialchars($av['nome_usuario'] ?? 'Anônimo') ?> |
                            <?= date('d/m/Y H:i', strtotime($av['data_avaliacao'])) ?>
                        </p>
                    </div>

                    <?php if ($logado && !$jaDenunciada): ?>
                        <button 
                            class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
                            onclick="denunciarAvaliacao(<?= $av['id_avaliacao'] ?>)"
                        >
                            Denunciar
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
  </div>
<?php if ($totalPaginas > 1): ?>
<div class="flex justify-center items-center mt-4 space-x-2">
    <!-- Ir para primeira página -->
    <a href="?pagina=1" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300"><<</a>

    <!-- seta voltar -->
    <a href="?pagina=<?= max(1, $pagina-1) ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">&lt;</a>

    <?php
    $botaoMax = 4; // máximo de botões visíveis
    $inicio = max(1, $pagina - 1); // começar um antes da página atual
    $fim = min($totalPaginas, $inicio + $botaoMax - 1); // até 4 botões

    // Ajusta se faltar botões no fim
    if ($fim - $inicio + 1 < $botaoMax) {
        $inicio = max(1, $fim - $botaoMax + 1);
    }

    for ($p = $inicio; $p <= $fim; $p++):
        $classe = $p == $pagina ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300';
    ?>
        <a href="?pagina=<?= $p ?>" class="px-3 py-1 rounded <?= $classe ?>"><?= $p ?></a>
    <?php endfor; ?>

    <!-- seta avançar -->
    <a href="?pagina=<?= min($totalPaginas, $pagina+1) ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">&gt;</a>

    <!-- Ir para última página -->
    <a href="?pagina=<?= $totalPaginas ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">>></a>
</div>
<?php endif; ?>

  <!-- quero adicionar aqui uma paginação, entao seria tipo uma seta o numero de paginas e outra seta < 1 2 ... 10 > tipo assim-->
</div>

<script src="../js/denuncia.js"></script>

<script>
const form = document.getElementById('formAvaliacao');
form.addEventListener('submit', async (e) => {
  e.preventDefault();
  const formData = new FormData(form);

  <?php if ($logado): ?>
  formData.append('id_usuario', '<?= $idUsuario ?>');
  <?php endif; ?>

  try {
    const resposta = await fetch('../../backend/model/avaliacao.php', {
      method: 'POST',
      body: formData
    });
    const resultado = await resposta.json();

    if (resultado.codigo === 'SUCESSO') {
      Swal.fire({
        icon: 'success',
        title: 'Obrigado!',
        text: resultado.mensagem,
        timer: 2000,
        showConfirmButton: false
      }).then(() => form.reset());
    } else {
      Swal.fire({
        icon: 'warning',
        title: 'Ops...',
        text: resultado.mensagem
      });
    }
  } catch (erro) {
    Swal.fire({
      icon: 'error',
      title: 'Erro',
      text: 'Não foi possível enviar a avaliação.'
    });
    console.error(erro);
  }
});

// Dropdown Avaliações
const toggle = document.getElementById('toggleAvaliacoes');
const lista = document.getElementById('listaAvaliacoes');
const seta = document.getElementById('iconSeta');

toggle.addEventListener('click', () => {
    lista.classList.toggle('hidden');
    seta.textContent = lista.classList.contains('hidden') ? '▼' : '▲';
});

// Funções exemplo para editar/remover (implemente AJAX real)
function editarAvaliacao(id) {
    alert("Função de editar avaliação #" + id);
}
function removerAvaliacao(id) {
    if(confirm("Deseja realmente remover esta avaliação?")) {
        alert("Função de remover avaliação #" + id);
    }
}
</script>
<?php if ($logado && isset($_SESSION['expira'])): ?>
<script>
  const expiraEm = <?= json_encode($_SESSION['expira']) ?>; // timestamp em segundos
  const agora = Math.floor(Date.now() / 1000);
  const tempoRestante = expiraEm - agora;

  if (tempoRestante > 0) {
    setTimeout(() => {
      Swal.fire({
        icon: "warning",
        title: "Sessão expirada",
        text: "Sua sessão expirou. Você será redirecionado para a home.",
        timer: 4000,
        showConfirmButton: false
      }).then(() => {
        window.location.href = "/Sistema-de-Feedback/src";
      });
    }, tempoRestante * 1000);
  }
</script>
<?php endif; ?>
</body>
</html>
