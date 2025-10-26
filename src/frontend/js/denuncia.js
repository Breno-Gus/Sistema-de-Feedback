async function denunciarAvaliacao(idAvaliacao) {
  const { value: motivo } = await Swal.fire({
    title: "Denunciar avaliação",
    input: "textarea",
    inputLabel: "Descreva o motivo da denúncia",
    inputPlaceholder: "Exemplo: linguagem ofensiva, conteúdo falso, etc.",
    inputAttributes: { 'aria-label': 'Motivo da denúncia' },
    showCancelButton: true,
    confirmButtonText: "Enviar denúncia",
    cancelButtonText: "Cancelar",
    preConfirm: (value) => {
      if (!value.trim()) {
        Swal.showValidationMessage("Por favor, informe o motivo da denúncia.");
      }
      return value;
    }
  });

  if (!motivo) return; // se cancelou

  try {
    const resposta = await fetch("../../backend/model/denuncia.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id_avaliacao: idAvaliacao,
        motivo: motivo
      })
    });

    const resultado = await resposta.json();

    Swal.fire({
      icon: resultado.codigo === "SUCESSO" ? "success" : "warning",
      title: resultado. codigo === "SUCESSO" ? "Denúncia enviada!" : "Ops...",
      text: resultado.mensagem
    });
  } catch (erro) {
    console.error(erro);
    Swal.fire({
      icon: "error",
      title: "Erro",
      text: "Não foi possível enviar a denúncia."
    });
  }
}
