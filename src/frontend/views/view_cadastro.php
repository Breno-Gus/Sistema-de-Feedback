<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Cadastro - Feedback Empresas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
    <h2 class="text-2xl font-bold text-blue-600 mb-6">Cadastro</h2>
    <form id="formRegistro" action="../../backend/model/cadastro.php" method="POST" class="space-y-4">
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
    <p class="mt-4 text-center text-sm text-gray-600">
      Já tem conta? <a href="view_login.php" class="text-green-600 hover:underline">Entrar</a>
    </p>
  </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="errors/error.js"></script>
</html>
