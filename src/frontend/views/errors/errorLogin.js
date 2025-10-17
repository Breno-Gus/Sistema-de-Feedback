// error_login.js
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formLogin");
    if (!form) return;

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const email = form.querySelector("#emailLogin")?.value.trim();
        const senha = form.querySelector("#senhaLogin")?.value.trim();

        if (!email || !senha) {
            Swal.fire({
                icon: "warning",
                title: "Campos obrigatórios",
                text: "Preencha email e senha para continuar."
            });
            return;
        }

        const formData = new FormData(form);

        try {
            const resposta = await fetch("../../backend/model/login.php", { method: "POST", body: formData });
            const dados = await resposta.json();

            if (dados.erro) {
                Swal.fire({ icon: "warning", title: "Atenção", text: dados.erro });
                return;
            }

            if (dados.codigo === "SUCESSO") {
                Swal.fire({
                    icon: "success",
                    title: "Login realizado",
                    text: dados.mensagem,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "../../frontend/views/view_painel.php";
                });
            } else {
                Swal.fire({ icon: "error", title: "Erro", text: dados.mensagem });
            }
        } catch (err) {
            Swal.fire({ icon: "error", title: "Erro de conexão", text: "Não foi possível enviar os dados." });
            console.error(err);
        }
    });
});
