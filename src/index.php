<?php
include_once __DIR__ . '/backend/model/sessao.php';

// Verifica sessão e redireciona para painel se estiver ativa
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
<body class="bg-gradient-to-r from-blue-200 to-green-200 min-h-screen flex items-center justify-center">

  <div class="w-full max-w-4xl bg-white rounded-3xl shadow-xl p-10 flex flex-col md:flex-row items-center justify-center gap-12">

    <!-- Imagem/Ilustração -->
    <div class="hidden md:block w-1/2">
      <img src="https://cdn.pixabay.com/photo/2017/05/10/21/25/business-2300053_1280.png" alt="Ilustração" class="rounded-2xl shadow-md">
    </div>

    <!-- Área de Formulários -->
    <div class="w-full md:w-1/2 space-y-8">

      <h1 class="text-4xl font-bold text-gray-800 text-center">Bem-vindo ao Feedback Empresas</h1>
      <p class="text-center text-gray-600">Faça login ou cadastre-se para acessar o painel de controle</p>

      <div class="flex flex-col md:flex-row gap-6 justify-center">
        <a href="frontend/views/view_cadastro.php" class="w-full md:w-auto px-8 py-3 bg-blue-600 text-white font-semibold rounded-xl shadow-lg hover:bg-blue-700 transition text-center">
          Cadastrar
        </a>
        <a href="frontend/views/view_login.php" class="w-full md:w-auto px-8 py-3 bg-green-600 text-white font-semibold rounded-xl shadow-lg hover:bg-green-700 transition text-center">
          Entrar
        </a>
      </div>

    </div>

  </div>

</body>
</html>
