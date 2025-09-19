document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("formLogin");
  const senhaInput = document.getElementById("senhaLogin");

  // Função toggle show/hide senha com SVG
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

});