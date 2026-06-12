        </div><!-- /.page-body -->
    </main><!-- /.main-content -->
</div><!-- /.admin-layout -->

<!-- Modal de Confirmação de Exclusão -->
<div class="modal-overlay" id="modal-excluir">
    <div class="modal">
        <div class="modal-icon">⚠️</div>
        <h3>Confirmar Exclusão</h3>
        <p>Tem certeza que deseja excluir <strong id="modal-titulo"></strong>? Esta ação não pode ser desfeita.</p>
        <div class="modal-actions">
            <button class="btn btn-secondary" onclick="fecharModal('modal-excluir')">Cancelar</button>
            <a href="#" id="modal-confirm-btn" class="btn btn-danger">🗑️ Excluir</a>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/admin/public/js/main.js"></script>
</body>
</html>
