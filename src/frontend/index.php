<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Feedback Empresas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

  <div class="w-full max-w-4xl bg-white rounded-2xl shadow-lg p-8">

    <?php if (isset($_SESSION['usuario'])): ?>
      <!-- Usuário logado -->
      <div class="text-center space-y-6">
        <h1 class="text-2xl font-bold text-gray-800">Bem-vindo, 
          <span class="text-blue-600"><?= htmlspecialchars($_SESSION['usuario']['nome']) ?></span> 👋
        </h1>
        <p class="text-gray-600">Você está logado com o email 
          <span class="font-semibold"><?= htmlspecialchars($_SESSION['usuario']['email']) ?></span>
        </p>
        <form action="logout.php" method="POST">
          <button type="submit"
            class="mt-4 px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
            Sair
          </button>
        </form>
      </div>

    <?php else: ?>
      <!-- Não logado: mostrar Cadastro e Login -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Cadastro -->
        <div>
          <h2 class="text-2xl font-bold text-blue-600 mb-6">Cadastro</h2>
          <form action="views/cadastro.php" method="POST" class="space-y-4">
            <div>
              <label for="nome" class="block text-sm font-medium text-gray-700">Nome</label>
              <input type="text" id="nome" name="nome" required
                class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
              <label for="emailCadastro" class="block text-sm font-medium text-gray-700">Email</label>
              <input type="email" id="emailCadastro" name="email" required
                class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
              <label for="senhaCadastro" class="block text-sm font-medium text-gray-700">Senha</label>
              <input type="password" id="senhaCadastro" name="senha" required
                class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <button type="submit"
              class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">Cadastrar</button>
          </form>
        </div>

        <!-- Login -->
        <div>
          <h2 class="text-2xl font-bold text-green-600 mb-6">Login</h2>
          <form action="views/login.php" method="POST" class="space-y-4">
            <div>
              <label for="emailLogin" class="block text-sm font-medium text-gray-700">Email</label>
              <input type="email" id="emailLogin" name="email" required
                class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            <div>
              <label for="senhaLogin" class="block text-sm font-medium text-gray-700">Senha</label>
              <input type="password" id="senhaLogin" name="senha" required
                class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            <button type="submit"
              class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition">Entrar</button>
          </form>
        </div>
      </div>
    <?php endif; ?>

  </div>

</body>
</html>
