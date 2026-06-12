<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Categoria.php';

$pageTitle  = 'Editar Categoria';
$pageActive = 'categorias';

$cat      = new Categoria();
$id       = (int)($_GET['id'] ?? 0);
$categoria = $cat->buscarPorId($id);
$erro     = '';

if (!$categoria) {
    header('Location: ' . BASE_URL . '/admin/views/listar_categorias.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'nome'      => trim($_POST['nome'] ?? ''),
        'descricao' => trim($_POST['descricao'] ?? ''),
    ];
    if (empty($dados['nome'])) {
        $erro = 'O nome da categoria é obrigatório.';
    } else {
        if ($cat->editar($id, $dados)) {
            header('Location: ' . BASE_URL . '/admin/views/listar_categorias.php?salvo=1');
            exit;
        }
        $erro = 'Erro ao atualizar a categoria.';
    }
    $categoria = array_merge($categoria, $dados);
}

require_once __DIR__ . '/partials/header.php';
?>

<?php if ($erro): ?>
<div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="card" style="max-width:560px">
    <form method="POST" novalidate>
        <div class="form-group">
            <label class="form-label">Nome *</label>
            <input type="text" name="nome" class="form-control"
                   value="<?= htmlspecialchars($categoria['nome']) ?>" required>
        </div>
        <div class="form-group">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="3"><?= htmlspecialchars($categoria['descricao'] ?? '') ?></textarea>
        </div>
        <div class="d-flex gap-10">
            <button type="submit" class="btn btn-primary">💾 Salvar</button>
            <a href="<?= BASE_URL ?>/admin/views/listar_categorias.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
