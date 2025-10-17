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
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

      <div>
        <label for="tipo_usuario" class="block text-sm font-medium text-gray-700">Tipo de usuário</label>
        <select id="tipo_usuario" name="tipo_usuario" required
          class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
          <option value="" disabled selected>Selecione</option>
          <?php
          include_once __DIR__ . "/../../backend/database/db.php";
          $stmt = $pdo->query("SELECT id_tipo, nome_tipo FROM tipos_usuario ORDER BY nome_tipo ASC");
          foreach ($stmt as $t) {
              // data-label ajuda caso você queira ler o nome sem depender de textContent
              echo "<option value='{$t['id_tipo']}' data-label='".htmlspecialchars($t['nome_tipo'], ENT_QUOTES)."'>"
                    . htmlspecialchars($t['nome_tipo']) .
                  "</option>";
          }
          ?>
        </select>
      </div>

      <!-- Campo CNPJ (inicia oculto) -->
      <div id="campoCnpj" class="hidden">
        <label for="cnpj" class="block text-sm font-medium text-gray-700">CNPJ</label>
        <input type="text" id="cnpj" name="cnpj"
          class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
          placeholder="00.000.000/0000-00">
      </div>

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
        <p id="senhaError" class="text-red-600 text-sm mt-1 hidden"></p>
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
<script src="../js/cadastro.js"></script>
<script src="errors/errorCadastro .js"></script>

</html>
