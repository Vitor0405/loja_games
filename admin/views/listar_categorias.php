<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Categoria.php';

$pageTitle  = 'Categorias';
$pageActive = 'categorias';

$cat  = new Categoria();
$lista = $cat->listar();

require_once __DIR__ . '/partials/header.php';
?>

<?php if (isset($_GET['deletado'])): ?>
<div class="alert alert-success">Categoria excluída com sucesso.</div>
<?php elseif (isset($_GET['erro'])): ?>
<div class="alert alert-danger">
    <?= $_GET['erro'] === 'tem_games'
        ? 'Não é possível excluir uma categoria que possui jogos vinculados.'
        : 'Erro ao excluir a categoria.' ?>
</div>
<?php elseif (isset($_GET['salvo'])): ?>
<div class="alert alert-success">Categoria salva com sucesso.</div>
<?php endif; ?>

<div class="d-flex justify-between align-center mb-20">
    <div></div>
    <a href="<?= BASE_URL ?>/admin/views/cadastrar_categoria.php" class="btn btn-primary">
        ➕ Nova Categoria
    </a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Jogos</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($lista)): ?>
            <tr><td colspan="5" class="text-muted text-center">Nenhuma categoria cadastrada.</td></tr>
        <?php else: ?>
            <?php foreach ($lista as $c): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><strong><?= htmlspecialchars($c['nome']) ?></strong></td>
                <td class="text-muted"><?= htmlspecialchars($c['descricao'] ?: '—') ?></td>
                <td>
                    <span class="badge badge-blue"><?= $c['total_games'] ?> jogo(s)</span>
                </td>
                <td>
                    <a href="<?= BASE_URL ?>/admin/views/editar_categoria.php?id=<?= $c['id'] ?>"
                       class="btn btn-sm btn-secondary">✏️ Editar</a>
                    <?php if ($c['total_games'] == 0): ?>
                    <button class="btn btn-sm btn-danger"
                            onclick="confirmarExclusao(<?= $c['id'] ?>, '<?= htmlspecialchars($c['nome']) ?>')">
                        🗑️ Excluir
                    </button>
                    <?php else: ?>
                    <span class="text-muted fs-12" title="Remova os jogos desta categoria primeiro">🔒</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<form id="form-excluir" method="POST"
      action="<?= BASE_URL ?>/admin/controllers/deletar_categoria.php" style="display:none">
    <input type="hidden" name="id" id="excluir-id">
</form>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
