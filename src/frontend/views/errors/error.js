async function enviarFormulario(formId, urlDestino) {
    const form = document.getElementById(formId);
    if (!form) return; // 🔹 evita erro se o form não existir

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        try {
            const resposta = await fetch(urlDestino, {
                method: "POST",
                body: formData
            });

            const texto = await resposta.text();
            console.log("Resposta bruta do servidor:", texto);

            let resultado;
            try {
                resultado = JSON.parse(texto);
            } catch (e) {
                Swal.fire({
                    icon: "error",
                    title: "Erro inesperado",
                    text: "O servidor não retornou JSON válido. Veja o console para mais detalhes."
                });
                return;
            }

            // 🔹 trata caso venha só "erro" do backend
            if (resultado.erro) {
                Swal.fire({
                    icon: "warning",
                    title: "Atenção",
                    text: resultado.erro
                });
                return;
            }

            switch (resultado.codigo) {
                case "CAMPOS_VAZIOS":
                case "LOGIN_INVALIDO":
                case "EMAIL_INVALIDO":
                case "SENHA_FRACA":
                case "EMAIL_DUPLICADO":
                    Swal.fire({
                        icon: "warning",
                        title: "Atenção",
                        text: resultado.mensagem
                    });
                    break;

                case "SUCESSO":
                    Swal.fire({
                        icon: "success",
                        title: "Sucesso!",
                        text: resultado.mensagem,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "../../frontend/views/painel.php";
                    });
                    break;

                case "ERRO_SERVIDOR":
                case "ERRO_INESPERADO":
                    // 🔹 força logout / exclusão de token
                    document.cookie.split(";").forEach(c => {
                        document.cookie = c.trim().split("=")[0] + "=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;";
                    });
                    sessionStorage.clear();
                    localStorage.clear();

                    Swal.fire({
                        icon: "error",
                        title: "Erro grave",
                        text: "Ocorreu um erro no servidor. Você foi desconectado por segurança."
                    });
                    break;
                case "EMAIL_DUPLICADO":
                    Swal.fire({
                        icon: "warning",
                        title: "Atenção",
                        text: resultado.mensagem
                    });
                    break;
                default:
                    Swal.fire({
                        icon: "error",
                        title: "Erro inesperado",
                        text: "Algo deu errado. Tente novamente."
                    });
            }
        } catch (erro) {
            Swal.fire({
                icon: "error",
                title: "Falha de conexão",
                text: "Não foi possível enviar os dados. Verifique sua conexão."
            });
            console.error("Erro no fetch:", erro);
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    enviarFormulario("formLogin", "../../backend/model/login.php");
    enviarFormulario("formRegistro", "../../backend/model/cadastro.php");
});
