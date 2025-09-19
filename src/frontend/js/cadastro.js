document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("formRegistro");
  const senhaInput = document.getElementById("senhaCadastro");

  // Cria campo Confirmar Senha
  let confirmarInput = document.getElementById("confirmarSenha");
  if (!confirmarInput) {
    const div = document.createElement("div");
    div.innerHTML = `
      <label for="confirmarSenha" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
      <input type="password" id="confirmarSenha" name="confirmarSenha" required
        class="mt-1 w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
      <p id="confirmaError" class="text-red-600 text-sm mt-1 hidden"></p>
    `;
    senhaInput.parentNode.insertAdjacentElement("afterend", div);
    confirmarInput = document.getElementById("confirmarSenha");
  }

  const senhaError = document.getElementById("senhaError");
  const confirmaError = document.getElementById("confirmaError");
  const cnpjInput = document.getElementById("cnpj");
  const cnpjWrap = document.getElementById("campoCnpj");
  const tipoSelect = document.getElementById("tipo_usuario");

  // Mostrar/ocultar campo CNPJ
  function atualizarCnpj() {
    const label = tipoSelect.options[tipoSelect.selectedIndex]?.getAttribute("data-label")?.toLowerCase() || "";
    if (label === "empresa") {
      cnpjWrap.classList.remove("hidden");
    } else {
      cnpjWrap.classList.add("hidden");
      if (cnpjInput) cnpjInput.value = "";
    }
  }

  if (tipoSelect) {
    tipoSelect.addEventListener("change", atualizarCnpj);
    atualizarCnpj(); // inicializa
  }

  // Toggle show/hide senha
  function adicionarToggleSenha(input) {
    const wrapper = document.createElement("div");
    wrapper.classList.add("relative");
    input.parentNode.insertBefore(wrapper, input);
    wrapper.appendChild(input);

    const btn = document.createElement("button");
    btn.type = "button";
    btn.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
      </svg>
    `;
    btn.classList.add("absolute","right-2","top-1/2","-translate-y-1/2","text-gray-500","hover:text-gray-700");
    wrapper.appendChild(btn);

    let mostrando = false;
    btn.addEventListener("click", () => {
      mostrando = !mostrando;
      input.type = mostrando ? "text" : "password";
      btn.innerHTML = mostrando ? `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.05 10.05 0 012.042-3.424m3.042-2.034A9.966 9.966 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.97 9.97 0 01-1.898 3.169M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/>
        </svg>
      ` : `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
      `;
    });
  }

  adicionarToggleSenha(senhaInput);
  adicionarToggleSenha(confirmarInput);

// Validação senha
senhaInput.addEventListener("input", () => {
  const valor = senhaInput.value;
  const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/;

  if (!regex.test(valor)) {
    senhaError.textContent = "A senha precisa ter pelo menos 8 caracteres, uma letra maiúscula, um número e um símbolo.";
    senhaError.classList.remove("hidden");
    senhaInput.classList.add("border-red-500");
  } else {
    senhaError.textContent = "";
    senhaError.classList.add("hidden");
    senhaInput.classList.remove("border-red-500");
  }

  // Atualiza validação do confirmar senha também
  if (confirmarInput.value && confirmarInput.value !== valor) {
    confirmaError.textContent = "As senhas não coincidem.";
    confirmaError.classList.remove("hidden");
    confirmarInput.classList.add("border-red-500");
  } else {
    confirmaError.textContent = "";
    confirmaError.classList.add("hidden");
    confirmarInput.classList.remove("border-red-500");
  }
});

// Validação confirmar senha
confirmarInput.addEventListener("input", () => {
  if (confirmarInput.value !== senhaInput.value) {
    confirmaError.textContent = "As senhas não coincidem.";
    confirmaError.classList.remove("hidden");
    confirmarInput.classList.add("border-red-500");
  } else {
    confirmaError.textContent = "";
    confirmaError.classList.add("hidden");
    confirmarInput.classList.remove("border-red-500");
  }
});


  // Máscara CNPJ
  if (cnpjInput) {
    cnpjInput.addEventListener("input", () => {
      let v = cnpjInput.value.replace(/\D/g,"").slice(0,14);
      if (v.length >= 3)  v = v.replace(/^(\d{2})(\d)/, "$1.$2");
      if (v.length >= 7)  v = v.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
      if (v.length >= 11) v = v.replace(/^(\d{2})\.(\d{3})\.(\d{3})(\d)/, "$1.$2.$3/$4");
      if (v.length >= 16) v = v.replace(/^(\d{2})\.(\d{3})\.(\d{3})\/(\d{4})(\d)/, "$1.$2.$3/$4-$5");
      cnpjInput.value = v;
    });
  }
});
