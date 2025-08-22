<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Login - Feedback Empresas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
    <h2 class="text-2xl font-bold text-green-600 mb-6">Login</h2>
    <form action="../../backend/model/login.php" method="POST" class="space-y-4">
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
    <p class="mt-4 text-center text-sm text-gray-600">
      Não tem conta? <a href="view_cadastro.php" class="text-blue-600 hover:underline">Cadastrar</a>
    </p>
  </div>
</body>
</html>
