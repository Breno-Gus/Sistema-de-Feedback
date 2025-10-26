const tipo = document.getElementById('tipoAvaliacao');
const estrelaDiv = document.getElementById('notaEstrela');
const emojiDiv = document.getElementById('notaEmoji');
const emojiInput = document.getElementById('emojiSelecionado');

tipo.addEventListener('change', () => {
  if (tipo.value === 'estrela') {
    estrelaDiv.style.display = 'flex';
    emojiDiv.style.display = 'none';
  } else if (tipo.value === 'emoji') {
    estrelaDiv.style.display = 'none';
    emojiDiv.style.display = 'flex';
  } else {
    estrelaDiv.style.display = 'none';
    emojiDiv.style.display = 'none';
  }
});

// Seleção de emoji
document.querySelectorAll('.emoji-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    emojiInput.value = btn.textContent;
    // destaque visual
    document.querySelectorAll('.emoji-btn').forEach(b => b.classList.remove('bg-gray-200'));
    btn.classList.add('bg-gray-200', 'rounded');
  });
});
