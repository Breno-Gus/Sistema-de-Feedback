<?php
include_once __DIR__ . '/../../backend/model/sessao.php';

// Verifica sessão
if (!verificarSessao()) {
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>Acesso Negado</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gradient-to-r from-red-200 to-red-400 min-h-screen flex items-center justify-center">
        <div class="bg-white rounded-3xl shadow-xl p-10 max-w-lg text-center">
            <h1 class="text-3xl font-bold text-red-600 mb-4">Acesso Negado 🚫</h1>
            <p class="text-gray-700 mb-6">Você precisa estar logado para acessar esta página.</p>
            <a href="index.php" class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                Voltar para a Página Inicial
            </a>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Painel - Feedback Empresas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">

  <!-- Sidebar -->
  <aside class="w-64 bg-white shadow-md p-6 hidden md:block">
    <h2 class="text-2xl font-bold text-blue-600 mb-8">Painel</h2>
    <nav class="space-y-4">
      <a href="painel.php" class="block text-gray-700 hover:text-blue-600 transition">Dashboard</a>
      <a href="#" class="block text-gray-700 hover:text-blue-600 transition">Usuários</a>
      <a href="#" class="block text-gray-700 hover:text-blue-600 transition">Relatórios</a>
      <a href="logout.php" class="block text-red-600 hover:text-red-700 transition">Sair</a>
    </nav>
  </aside>

  <!-- Main content -->
  <main class="flex-1 p-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']['nome']) ?> 👋</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
        <h3 class="text-xl font-semibold text-gray-700">Usuários Cadastrados</h3>
        <p class="mt-2 text-gray-500">Visualize e gerencie todos os usuários do sistema.</p>
      </div>
      <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
        <h3 class="text-xl font-semibold text-gray-700">Relatórios</h3>
        <p class="mt-2 text-gray-500">Acompanhe métricas e estatísticas do sistema.</p>
      </div>
      <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
        <h3 class="text-xl font-semibold text-gray-700">Configurações</h3>
        <p class="mt-2 text-gray-500">Atualize suas informações e preferências.</p>
      </div>
    </div>
  </main>

</body>
</html>
