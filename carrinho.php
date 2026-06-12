<?php
require_once __DIR__ . '/admin/config/conexao.php';
require_once __DIR__ . '/admin/models/games.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['carrinho'])) $_SESSION['carrinho'] = [];

$acao    = $_GET['acao']    ?? '';
$gameId  = (int)($_GET['id'] ?? 0);

if ($acao === 'adicionar' && $gameId > 0) {
    if (isset($_SESSION['carrinho'][$gameId])) {
        $_SESSION['carrinho'][$gameId]['quantidade']++;
    } else {
        $g    = new Game();
        $game = $g->buscarGamePorId($gameId);
        if ($game) {
            $_SESSION['carrinho'][$gameId] = [
                'game_id'    => $game['id'],
                'titulo'     => $game['titulo'],
                'preco'      => $game['preco'],
                'imagem'     => $game['imagem'],
                'quantidade' => 1,
            ];
        }
    }
    header('Location: ' . BASE_URL . '/carrinho.php');
    exit;
}

if ($acao === 'remover' && $gameId > 0) {
    unset($_SESSION['carrinho'][$gameId]);
    header('Location: ' . BASE_URL . '/carrinho.php');
    exit;
}

if ($acao === 'limpar') {
    $_SESSION['carrinho'] = [];
    header('Location: ' . BASE_URL . '/carrinho.php');
    exit;
}

$itens = $_SESSION['carrinho'];
$total = array_sum(array_map(fn($i) => $i['preco'] * $i['quantidade'], $itens));
$qtdTotal = array_sum(array_column($itens, 'quantidade'));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho — uTorrent Azul</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/public/css/style.css">
    <style>
        .carrinho-wrap { max-width:900px; margin:0 auto; padding:30px 20px; }
        .item-card {
            display:flex; align-items:center; gap:16px;
            background:var(--bg-card); border:1px solid var(--border);
            border-radius:6px; padding:16px; margin-bottom:12px;
        }
        .item-img { width:70px; height:70px; object-fit:cover; border-radius:4px; background:var(--bg-body); }
        .item-info { flex:1 }
        .item-info h3 { margin:0 0 4px; font-size:15px; color:var(--text) }
        .item-preco  { font-size:16px; font-weight:700; color:var(--steam-green) }
        .resumo-card {
            background:var(--bg-card); border:1px solid var(--border);
            border-radius:6px; padding:24px; position:sticky; top:20px;
        }
        @media(min-width:700px){ .layout { display:flex; gap:24px; align-items:flex-start; }
            .lista-itens { flex:1 } .resumo-card { width:280px; } }
    </style>
</head>
<body style="background:var(--bg-body)">

<nav style="background:var(--bg-nav);border-bottom:1px solid var(--border);padding:0 24px;display:flex;align-items:center;gap:16px;height:52px">
    <a href="<?= BASE_URL ?>/index.php" style="font-weight:700;color:var(--text);text-decoration:none">🔵 uTorrent Azul</a>
    <span style="color:var(--border)">|</span>
    <span style="color:var(--text-muted)">Meu Carrinho</span>
    <span style="flex:1"></span>
    <?php if (!empty($_SESSION['cliente_id'])): ?>
    <a href="<?= BASE_URL ?>/minha_conta.php" style="color:var(--steam-blue);font-size:13px">
        👤 <?= htmlspecialchars($_SESSION['cliente_nome']) ?>
    </a>
    <?php else: ?>
    <a href="<?= BASE_URL ?>/login_cliente.php" style="color:var(--steam-blue);font-size:13px">Entrar</a>
    <?php endif; ?>
</nav>

<div class="carrinho-wrap">
    <h2 style="color:var(--text);margin:0 0 20px">🛒 Carrinho (<?= $qtdTotal ?> item<?= $qtdTotal !== 1 ? 's' : '' ?>)</h2>

    <?php if (empty($itens)): ?>
    <div style="text-align:center;padding:60px 20px;color:var(--text-muted)">
        <div style="font-size:48px;margin-bottom:16px">🛒</div>
        <p>Seu carrinho está vazio.</p>
        <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary" style="margin-top:8px">Ver jogos</a>
    </div>
    <?php else: ?>
    <div class="layout">
        <div class="lista-itens">
            <?php foreach ($itens as $item): ?>
            <div class="item-card">
                <?php if ($item['imagem']): ?>
                <img src="<?= BASE_URL ?>/admin/public/imagens/<?= htmlspecialchars($item['imagem']) ?>"
                     alt="" class="item-img">
                <?php else: ?>
                <div class="item-img" style="display:flex;align-items:center;justify-content:center;font-size:24px">🎮</div>
                <?php endif; ?>
                <div class="item-info">
                    <h3><?= htmlspecialchars($item['titulo']) ?></h3>
                    <div class="item-preco">R$ <?= number_format($item['preco'], 2, ',', '.') ?></div>
                    <div style="font-size:12px;color:var(--text-muted)">Qtd: <?= $item['quantidade'] ?></div>
                </div>
                <a href="<?= BASE_URL ?>/carrinho.php?acao=remover&id=<?= $item['game_id'] ?>"
                   style="color:var(--danger);font-size:20px;text-decoration:none"
                   title="Remover">✕</a>
            </div>
            <?php endforeach; ?>
            <a href="<?= BASE_URL ?>/carrinho.php?acao=limpar"
               style="font-size:12px;color:var(--text-muted)">Limpar carrinho</a>
        </div>

        <div class="resumo-card">
            <h3 style="margin:0 0 16px;font-size:16px;color:var(--text)">Resumo</h3>
            <?php foreach ($itens as $item): ?>
            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:8px;color:var(--text-muted)">
                <span><?= htmlspecialchars(mb_strimwidth($item['titulo'], 0, 22, '…')) ?></span>
                <span>R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></span>
            </div>
            <?php endforeach; ?>
            <hr style="border-color:var(--border);margin:16px 0">
            <div style="display:flex;justify-content:space-between;font-weight:700;font-size:17px;margin-bottom:20px">
                <span style="color:var(--text)">Total</span>
                <span style="color:var(--steam-green)">R$ <?= number_format($total, 2, ',', '.') ?></span>
            </div>
            <?php if (!empty($_SESSION['cliente_id'])): ?>
            <a href="<?= BASE_URL ?>/finalizar_pedido.php" class="btn btn-primary w-100">
                Finalizar Pedido →
            </a>
            <?php else: ?>
            <a href="<?= BASE_URL ?>/login_cliente.php" class="btn btn-primary w-100"
               onclick="sessionStorage.setItem('redirect','carrinho')">
                Entrar para finalizar
            </a>
            <div style="font-size:11px;color:var(--text-muted);text-align:center;margin-top:8px">
                ou <a href="<?= BASE_URL ?>/registro.php" style="color:var(--steam-blue)">criar conta</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
