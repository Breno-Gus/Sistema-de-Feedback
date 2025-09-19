// error.js - versão corrigida e funcional

async function enviarFormulario(formId, urlDestino) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener("submit", async function(e) {
        e.preventDefault();

        // --------------------
        // Campos principais
        // --------------------
        const senhaInput = form.elements['senha'] || form.querySelector("#senhaCadastro");
        const confirmarInput = form.elements['confirmarSenha'] || form.querySelector("#confirmarSenha");
        const senhaError = document.getElementById("senhaError");
        const confirmaError = document.getElementById("confirmaError");

        // --------------------
        // Validação de senha
        // --------------------
        const senha = senhaInput.value;
        let senhaValida = true;
        let msgSenha = "";

        if (senha.length < 8) {
            senhaValida = false;
            msgSenha = "A senha deve ter pelo menos 8 caracteres.";
        } else if (!/[A-Z]/.test(senha)) {
            senhaValida = false;
            msgSenha = "A senha deve conter ao menos uma letra maiúscula.";
        } else if (!/[0-9]/.test(senha)) {
            senhaValida = false;
            msgSenha = "A senha deve conter ao menos um número.";
        } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(senha)) {
            senhaValida = false;
            msgSenha = "A senha deve conter ao menos um símbolo.";
        }

        if (!senhaValida) {
            senhaError.textContent = msgSenha;
            senhaError.classList.remove("hidden");
            senhaInput.classList.add("border-red-500");
            return;
        } else {
            senhaError.textContent = "";
            senhaError.classList.add("hidden");
            senhaInput.classList.remove("border-red-500");
        }

        // --------------------
        // Validação confirmar senha
        // --------------------
        if (senha !== confirmarInput.value) {
            confirmaError.textContent = "As senhas não coincidem.";
            confirmaError.classList.remove("hidden");
            confirmarInput.classList.add("border-red-500");
            return;
        } else {
            confirmaError.textContent = "";
            confirmaError.classList.add("hidden");
            confirmarInput.classList.remove("border-red-500");
        }

        // --------------------
        // Envio via fetch
        // --------------------
        const formData = new FormData(form);

        try {
            const resposta = await fetch(urlDestino, { method: "POST", body: formData });
            const texto = await resposta.text();
            let resultado;

            try {
                resultado = JSON.parse(texto);
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Erro inesperado",
                    text: "O servidor não retornou JSON válido."
                });
                return;
            }

            if (resultado.erro) {
                Swal.fire({ icon: "warning", title: "Atenção", text: resultado.erro });
                return;
            }

            switch (resultado.codigo) {
                case "CAMPOS_VAZIOS":
                case "EMAIL_INVALIDO":
                case "SENHA_FRACA":
                case "EMAIL_DUPLICADO":
                    Swal.fire({ icon: "warning", title: "Atenção", text: resultado.mensagem });
                    break;
                case "SUCESSO":
                    Swal.fire({
                        icon: "success",
                        title: "Sucesso!",
                        text: resultado.mensagem,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => window.location.href = "../../frontend/views/view_painel.php");
                    break;
                case "ERRO_SERVIDOR":
                case "ERRO_INESPERADO":
                    document.cookie.split(";").forEach(c => {
                        document.cookie = c.trim().split("=")[0] + "=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;";
                    });
                    sessionStorage.clear();
                    localStorage.clear();
                    Swal.fire({ icon: "error", title: "Erro grave", text: "Você foi desconectado por segurança." });
                    break;
                default:
                    Swal.fire({ icon: "error", title: "Erro inesperado", text: "Algo deu errado. Tente novamente." });
            }
        } catch (erro) {
            Swal.fire({ icon: "error", title: "Falha de conexão", text: "Não foi possível enviar os dados." });
            console.error("Erro no fetch:", erro);
        }
    });
}

// --------------------
// Inicialização
// --------------------
document.addEventListener("DOMContentLoaded", () => {
    enviarFormulario("formRegistro", "../../backend/model/cadastro.php");
    enviarFormulario("formLogin", "../../backend/model/login.php");
});
