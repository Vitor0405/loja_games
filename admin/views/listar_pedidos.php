<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Pedido.php';

$pageTitle  = 'Pedidos';
$pageActive = 'pedidos';

$ped   = new Pedido();
$lista = $ped->listar();

$cores = ['pendente' => 'badge-yellow', 'aprovado' => 'badge-green', 'cancelado' => 'badge-red'];

require_once __DIR__ . '/partials/header.php';
?>

<?php if (isset($_GET['deletado'])): ?>
<div class="alert alert-success">Pedido excluído com sucesso.</div>
<?php elseif (isset($_GET['erro'])): ?>
<div class="alert alert-danger">Erro ao excluir o pedido.</div>
<?php elseif (isset($_GET['atualizado'])): ?>
<div class="alert alert-success">Status do pedido atualizado.</div>
<?php endif; ?>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Status</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($lista)): ?>
            <tr><td colspan="6" class="text-muted text-center">Nenhum pedido registrado.</td></tr>
        <?php else: ?>
            <?php foreach ($lista as $p): ?>
            <tr>
                <td>#<?= $p['id'] ?></td>
                <td><strong><?= htmlspecialchars($p['cliente_nome']) ?></strong></td>
                <td>R$ <?= number_format($p['total'], 2, ',', '.') ?></td>
                <td>
                    <span class="badge <?= $cores[$p['status']] ?? 'badge-blue' ?>">
                        <?= ucfirst($p['status']) ?>
                    </span>
                </td>
                <td class="text-muted fs-12">
                    <?= date('d/m/Y H:i', strtotime($p['created_at'])) ?>
                </td>
                <td>
                    <a href="<?= BASE_URL ?>/admin/views/ver_pedido.php?id=<?= $p['id'] ?>"
                       class="btn btn-sm btn-secondary">👁️ Ver</a>
                    <button class="btn btn-sm btn-danger"
                            onclick="confirmarExclusao(<?= $p['id'] ?>, 'Pedido #<?= $p['id'] ?>')">
                        🗑️ Excluir
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<form id="form-excluir" method="POST"
      action="<?= BASE_URL ?>/admin/controllers/deletar_pedido.php" style="display:none">
    <input type="hidden" name="id" id="excluir-id">
</form>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
