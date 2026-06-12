<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Pedido.php';

$pageTitle  = 'Detalhes do Pedido';
$pageActive = 'pedidos';

$ped    = new Pedido();
$id     = (int)($_GET['id'] ?? 0);
$pedido = $ped->buscarPorId($id);

if (!$pedido) {
    header('Location: ' . BASE_URL . '/admin/views/listar_pedidos.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['status'])) {
    $ped->atualizarStatus($id, $_POST['status']);
    header('Location: ' . BASE_URL . '/admin/views/ver_pedido.php?id=' . $id . '&atualizado=1');
    exit;
}

$cores = ['pendente' => 'badge-yellow', 'aprovado' => 'badge-green', 'cancelado' => 'badge-red'];

require_once __DIR__ . '/partials/header.php';
?>

<?php if (isset($_GET['atualizado'])): ?>
<div class="alert alert-success">Status atualizado com sucesso.</div>
<?php endif; ?>

<div class="d-flex gap-20" style="flex-wrap:wrap;align-items:flex-start">

    <div class="card" style="flex:1;min-width:300px">
        <h3 style="margin:0 0 16px;font-size:15px;color:var(--text-muted)">Informações do Pedido</h3>
        <table class="table">
            <tr><td><strong>Pedido</strong></td><td>#<?= $pedido['id'] ?></td></tr>
            <tr><td><strong>Cliente</strong></td><td><?= htmlspecialchars($pedido['cliente_nome']) ?></td></tr>
            <tr><td><strong>E-mail</strong></td><td><?= htmlspecialchars($pedido['cliente_email']) ?></td></tr>
            <tr>
                <td><strong>Status</strong></td>
                <td><span class="badge <?= $cores[$pedido['status']] ?>">
                    <?= ucfirst($pedido['status']) ?>
                </span></td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td><strong style="color:var(--steam-green)">
                    R$ <?= number_format($pedido['total'], 2, ',', '.') ?>
                </strong></td>
            </tr>
            <tr>
                <td><strong>Data</strong></td>
                <td><?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?></td>
            </tr>
        </table>
    </div>

    <div class="card" style="flex:1;min-width:300px">
        <h3 style="margin:0 0 16px;font-size:15px;color:var(--text-muted)">Alterar Status</h3>
        <form method="POST">
            <div class="form-group">
                <select name="status" class="form-control">
                    <option value="pendente"  <?= $pedido['status'] === 'pendente'  ? 'selected' : '' ?>>Pendente</option>
                    <option value="aprovado"  <?= $pedido['status'] === 'aprovado'  ? 'selected' : '' ?>>Aprovado</option>
                    <option value="cancelado" <?= $pedido['status'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">✅ Atualizar Status</button>
        </form>
    </div>
</div>

<div class="card" style="margin-top:20px">
    <h3 style="margin:0 0 16px;font-size:15px;color:var(--text-muted)">Itens do Pedido</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Jogo</th>
                <th>Quantidade</th>
                <th>Preço Unitário</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($pedido['itens'] as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['titulo']) ?></td>
            <td><?= $item['quantidade'] ?></td>
            <td>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
            <td><strong>R$ <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?></strong></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right"><strong>Total</strong></td>
                <td><strong style="color:var(--steam-green)">
                    R$ <?= number_format($pedido['total'], 2, ',', '.') ?>
                </strong></td>
            </tr>
        </tfoot>
    </table>
</div>

<div style="margin-top:16px">
    <a href="<?= BASE_URL ?>/admin/views/listar_pedidos.php" class="btn btn-secondary">← Voltar</a>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
