// =====================================================
// GameStock 2025 — JS Principal
// =====================================================

// Modal de confirmação de exclusão
function confirmarExclusao(titulo, url) {
    document.getElementById('modal-titulo').textContent = titulo;
    document.getElementById('modal-confirm-btn').setAttribute('href', url);
    document.getElementById('modal-excluir').classList.add('active');
}

function fecharModal(id) {
    document.getElementById(id).classList.remove('active');
}

// Preview de imagem ao selecionar arquivo
function previewImagem(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) return;
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        };
        reader.readAsDataURL(file);
    }
}

// Auto-hide alerts após 4s
document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert[data-autohide]');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    // Fechar modal clicando fora
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', e => {
            if (e.target === overlay) overlay.classList.remove('active');
        });
    });
});
