<?php
session_start();
require_once '../config/conexao.php';
require_once '../config/auth.php';
require_once '../models/games.php';
require_once '../models/Cliente.php';
require_once '../models/Pedido.php';

$pageTitle  = 'Dashboard';
$pageActive = 'dashboard';

$game     = new Game();
$allGames = $game->listarGames();
$total    = count($allGames);
$valorTotal  = $game->valorTotalEstoque();
$semEstoque  = count(array_filter($allGames, fn($g) => (int)$g['estoque'] === 0));
$estoqueTotal = array_sum(array_column($allGames, 'estoque'));

$cli         = new Cliente();
$ped         = new Pedido();
$totalClientes = $cli->contar();
$totalPedidos  = $ped->contar();
$faturamento   = $ped->somarTotal();
$ultPedidos    = $ped->listar(5);

require_once 'partials/header.php';
?>

<?php if ($_GET['deletado'] ?? ''): ?>
    <div class="alert alert-success" data-autohide>✅ Jogo excluído com sucesso.</div>
<?php elseif ($_GET['erro'] ?? ''): ?>
    <div class="alert alert-danger">❌ Erro ao excluir o jogo.</div>
<?php endif; ?>

<!-- STATS -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple">🕹️</div>
        <div class="stat-info">
            <div class="stat-value"><?= $total ?></div>
            <div class="stat-label">Jogos cadastrados</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cyan">📦</div>
        <div class="stat-info">
            <div class="stat-value"><?= $estoqueTotal ?></div>
            <div class="stat-label">Unidades em estoque</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">💰</div>
        <div class="stat-info">
            <div class="stat-value">R$ <?= number_format($valorTotal, 0, ',', '.') ?></div>
            <div class="stat-label">Valor total em estoque</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">⚠️</div>
        <div class="stat-info">
            <div class="stat-value"><?= $semEstoque ?></div>
            <div class="stat-label">Jogos sem estoque</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cyan">👤</div>
        <div class="stat-info">
            <div class="stat-value"><?= $totalClientes ?></div>
            <div class="stat-label">Clientes cadastrados</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">📦</div>
        <div class="stat-info">
            <div class="stat-value"><?= $totalPedidos ?></div>
            <div class="stat-label">Pedidos realizados</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">💳</div>
        <div class="stat-info">
            <div class="stat-value">R$ <?= number_format($faturamento, 0, ',', '.') ?></div>
            <div class="stat-label">Faturamento (aprovados)</div>
        </div>
    </div>
</div>

<!-- ÚLTIMOS PEDIDOS -->
<?php if (!empty($ultPedidos)): ?>
<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <div class="card-title">📦 Últimos Pedidos</div>
        <a href="<?= BASE_URL ?>/admin/views/listar_pedidos.php" class="btn btn-secondary btn-sm">Ver todos</a>
    </div>
    <table class="table">
        <thead><tr><th>#</th><th>Cliente</th><th>Total</th><th>Status</th><th>Data</th></tr></thead>
        <tbody>
        <?php foreach ($ultPedidos as $p):
            $cor = match($p['status']) { 'aprovado' => 'badge-green', 'cancelado' => 'badge-red', default => 'badge-yellow' };
        ?>
        <tr>
            <td>#<?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['cliente_nome']) ?></td>
            <td>R$ <?= number_format($p['total'], 2, ',', '.') ?></td>
            <td><span class="badge <?= $cor ?>"><?= ucfirst($p['status']) ?></span></td>
            <td class="text-muted fs-12"><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- TABELA RÁPIDA -->
<div class="card">
    <div class="card-header">
        <div class="card-title">🕹️ Catálogo de Jogos</div>
        <div class="d-flex gap-8">
            <a href="gerenciar_games.php" class="btn btn-secondary btn-sm">🔄 Atualizar</a>
            <a href="cadastrar_game.php" class="btn btn-primary btn-sm">➕ Novo Jogo</a>
        </div>
    </div>

    <?php if ($total > 0): ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th>Jogo</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th style="width:120px">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allGames as $g):
                    $est = (int)$g['estoque'];
                    $estClass = $est === 0 ? 'stock-zero' : ($est <= 5 ? 'stock-low' : 'stock-ok');
                    $catNome = strtolower($g['categoria_nome'] ?? '');
                    $emoji = match($catNome) {
                        'ação','acao' => '⚔️', 'aventura' => '🗺️', 'rpg' => '🧙',
                        'sandbox' => '🏗️', 'esportes' => '⚽', default => '🎮',
                    };
                ?>
                <tr>
                    <td class="text-muted fs-12"><?= $g['id'] ?></td>
                    <td>
                        <div class="d-flex align-center gap-12">
                            <?php $imgPath = '../public/imagens/' . $g['imagem'];
                            if ($g['imagem'] && file_exists($imgPath)): ?>
                                <div class="game-img-thumb">
                                    <img src="<?= BASE_URL ?>/admin/public/imagens/<?= htmlspecialchars($g['imagem']) ?>" alt="">
                                </div>
                            <?php else: ?>
                                <div class="game-img-thumb"><?= $emoji ?></div>
                            <?php endif; ?>
                            <div>
                                <div class="fw-bold"><?= htmlspecialchars($g['titulo']) ?></div>
                                <div class="text-muted fs-12"><?= mb_strimwidth(htmlspecialchars($g['descricao'] ?? ''), 0, 55, '...') ?></div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-purple"><?= htmlspecialchars($g['categoria_nome'] ?? '—') ?></span></td>
                    <td class="fw-bold">R$ <?= number_format($g['preco'], 2, ',', '.') ?></td>
                    <td><span class="<?= $estClass ?>"><?= $est ?></span></td>
                    <td>
                        <div class="td-actions">
                            <a href="editar_game.php?id=<?= $g['id'] ?>" class="btn btn-warning btn-sm">✏️</a>
                            <button
                                class="btn btn-danger btn-sm"
                                onclick="confirmarExclusao('<?= addslashes(htmlspecialchars($g['titulo'])) ?>',
                                    '<?= BASE_URL ?>/admin/controllers/deletar_game.php?id=<?= $g['id'] ?>')"
                            >🗑️</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">🕹️</div>
            <p>Nenhum jogo cadastrado ainda.</p>
            <a href="cadastrar_game.php" class="btn btn-primary" style="margin-top:16px">➕ Cadastrar primeiro jogo</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'partials/footer.php'; ?>
