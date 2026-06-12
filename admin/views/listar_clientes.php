<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/auth.php';
apenasAdmin();
require_once __DIR__ . '/../models/Cliente.php';

$pageTitle  = 'Clientes';
$pageActive = 'clientes';

$cli  = new Cliente();
$lista = $cli->listar();

require_once __DIR__ . '/partials/header.php';
?>

<?php if (isset($_GET['deletado'])): ?>
<div class="alert alert-success">Cliente excluído com sucesso.</div>
<?php elseif (isset($_GET['erro'])): ?>
<div class="alert alert-danger">
    <?= $_GET['erro'] === 'tem_pedidos'
        ? 'Não é possível excluir um cliente que possui pedidos.'
        : 'Erro ao excluir o cliente.' ?>
</div>
<?php elseif (isset($_GET['salvo'])): ?>
<div class="alert alert-success">Cliente atualizado com sucesso.</div>
<?php endif; ?>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Status</th>
                <th>Cadastro</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($lista)): ?>
            <tr><td colspan="7" class="text-muted text-center">Nenhum cliente cadastrado.</td></tr>
        <?php else: ?>
            <?php foreach ($lista as $c): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><strong><?= htmlspecialchars($c['nome']) ?></strong></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars($c['telefone'] ?: '—') ?></td>
                <td>
                    <?php if ($c['ativo']): ?>
                    <span class="badge badge-green">Ativo</span>
                    <?php else: ?>
                    <span class="badge badge-red">Inativo</span>
                    <?php endif; ?>
                </td>
                <td class="text-muted fs-12">
                    <?= date('d/m/Y', strtotime($c['created_at'])) ?>
                </td>
                <td>
                    <a href="<?= BASE_URL ?>/admin/views/editar_cliente.php?id=<?= $c['id'] ?>"
                       class="btn btn-sm btn-secondary">✏️ Editar</a>
                    <?php if (!$cli->temPedidos($c['id'])): ?>
                    <button class="btn btn-sm btn-danger"
                            onclick="confirmarExclusao(<?= $c['id'] ?>, '<?= htmlspecialchars($c['nome']) ?>')">
                        🗑️ Excluir
                    </button>
                    <?php else: ?>
                    <span class="text-muted fs-12" title="Cliente possui pedidos">🔒</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<form id="form-excluir" method="POST"
      action="<?= BASE_URL ?>/admin/controllers/deletar_cliente.php" style="display:none">
    <input type="hidden" name="id" id="excluir-id">
</form>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
