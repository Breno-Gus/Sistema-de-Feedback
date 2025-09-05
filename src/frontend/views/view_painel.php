<?php
session_start();
include_once __DIR__ . '/../../backend/model/sessao.php';
include_once __DIR__ . '/../../backend/database/db.php';

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
<body class="bg-gradient-to-br from-blue-100 to-green-100 min-h-screen flex items-center justify-center p-6">

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

  <!-- Formulário -->
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
</div>

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
</script>
</body>
</html>
