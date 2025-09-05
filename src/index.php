<?php
session_start(); // 🔹 sempre antes de qualquer verificação
include_once __DIR__ . '/backend/model/sessao.php';

// Se já existe sessão válida, manda pro painel
if (verificarSessao()) {
    header("Location: frontend/views/view_painel.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Feedback Empresas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-200 via-green-200 to-yellow-100 min-h-screen flex items-center justify-center p-6">

  <div class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl p-10 flex flex-col md:flex-row items-center justify-center gap-12">

    <!-- Imagem/Ilustração -->
    <div class="hidden md:block w-1/2">
      <img src="https://cdn.pixabay.com/photo/2017/05/10/21/25/business-2300053_1280.png" 
           alt="Ilustração" 
           class="rounded-2xl shadow-lg transform hover:scale-105 transition duration-500">
    </div>

    <!-- Área de Ações -->
    <div class="w-full md:w-1/2 space-y-8 text-center">

      <h1 class="text-5xl font-extrabold text-gray-800">Bem-vindo ao Feedback Empresas</h1>
      <p class="text-gray-600 text-lg">Faça login, cadastre-se ou envie sua avaliação de forma anônima.</p>

      <div class="flex flex-col md:flex-row gap-6 justify-center mt-6">
        <a href="frontend/views/view_cadastro.php" 
           class="w-full md:w-auto px-8 py-3 bg-blue-600 text-white font-semibold rounded-xl shadow-lg hover:bg-blue-700 transition">
          Cadastrar
        </a>
        <a href="frontend/views/view_login.php" 
           class="w-full md:w-auto px-8 py-3 bg-green-600 text-white font-semibold rounded-xl shadow-lg hover:bg-green-700 transition">
          Entrar
        </a>
        <a href="frontend/views/view_painel.php" 
           class="w-full md:w-auto px-8 py-3 bg-yellow-500 text-white font-semibold rounded-xl shadow-lg hover:bg-yellow-600 transition">
          Avaliação Anônima
        </a>
      </div>

      <p class="text-gray-500 text-sm mt-4">
        Todas as avaliações são confidenciais. Usuários logados terão nome registrado.
      </p>
    </div>

  </div>

</body>
</html>
