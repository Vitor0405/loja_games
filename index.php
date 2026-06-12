<?php
require_once 'admin/config/conexao.php';
require_once 'admin/models/games.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$game       = new Game();
$categorias = $game->listarCategorias();

$catId = (int)($_GET['cat'] ?? 0);
$busca = trim($_GET['buscar'] ?? '');

if ($busca) {
    $games = $game->buscarPorNome($busca);
} elseif ($catId) {
    $games = $game->buscarPorCategoria($catId);
} else {
    $games = $game->listarGames();
}

$todosGames  = $game->listarGames();
$destaque    = $todosGames[array_rand($todosGames)] ?? null;
$qtdCarrinho = array_sum(array_column($_SESSION['carrinho'] ?? [], 'quantidade'));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>uTorrent Azul — Loja de Jogos</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Motiva+Sans:wght@400;700&family=Inter:wght@300;400;500;600;700;800&display=swap');

        :root {
            --steam-bg:       #1b2838;
            --steam-dark:     #171a21;
            --steam-card:     #16202d;
            --steam-mid:      #2a475e;
            --steam-blue:     #1a9fff;
            --steam-blue-l:   #66c0f4;
            --steam-blue-g:   rgba(102,192,244,0.15);
            --steam-green:    #4c6b22;
            --steam-green-l:  #a4d007;
            --steam-text:     #c6d4df;
            --steam-muted:    #7a9bb5;
            --steam-border:   #2a475e;
            --steam-nav:      #171a21;
            --steam-hover:    #263c50;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: var(--steam-bg);
            color: var(--steam-text);
            min-height: 100vh;
        }

        a { text-decoration: none; color: inherit; }

        /* ─── NAVBAR ─── */
        .navbar {
            background: var(--steam-dark);
            height: 52px;
            display: flex;
            align-items: center;
            padding: 0 20px;
            position: sticky; top: 0; z-index: 200;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-right: 24px;
            flex-shrink: 0;
        }

        .brand-icon {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--steam-blue), #0a5fb4);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
        }

        .brand-name {
            font-size: 17px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.3px;
        }

        .brand-name span { color: var(--steam-blue-l); }

        .nav-links {
            display: flex;
            gap: 2px;
            flex: 1;
        }

        .nav-link {
            padding: 6px 14px;
            font-size: 13px;
            color: var(--steam-text);
            border-radius: 3px;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap;
        }

        .nav-link:hover { background: var(--steam-mid); color: #fff; }
        .nav-link.active { background: rgba(102,192,244,0.15); color: var(--steam-blue-l); }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .search-wrap {
            display: flex;
        }

        .search-wrap input {
            background: var(--steam-card);
            border: 1px solid var(--steam-border);
            border-right: none;
            border-radius: 3px 0 0 3px;
            padding: 6px 12px;
            color: var(--steam-text);
            font-size: 13px;
            width: 180px;
        }

        .search-wrap input:focus { outline: none; border-color: var(--steam-blue); }
        .search-wrap input::placeholder { color: var(--steam-muted); }

        .search-wrap button {
            background: var(--steam-blue);
            border: none;
            border-radius: 0 3px 3px 0;
            padding: 6px 12px;
            color: #fff;
            cursor: pointer;
            font-size: 13px;
            transition: background 0.15s;
        }

        .search-wrap button:hover { background: var(--steam-blue-l); color: #1b2838; }

        .btn-login {
            background: linear-gradient(to bottom, #4b8ac0, #2c6e9e);
            border: none;
            border-radius: 3px;
            padding: 6px 16px;
            color: #c6e3fa;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .btn-login:hover { background: linear-gradient(to bottom, #5a9fd4, #3a80b4); color: #fff; }

        /* ─── SUB NAV / CATEGORIAS ─── */
        .sub-nav {
            background: rgba(0,0,0,0.3);
            border-bottom: 1px solid rgba(255,255,255,0.04);
            padding: 0 20px;
        }

        .sub-nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            gap: 0;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .sub-nav-inner::-webkit-scrollbar { display: none; }

        .cat-link {
            padding: 10px 16px;
            font-size: 12px;
            color: var(--steam-muted);
            white-space: nowrap;
            transition: color 0.15s;
            border-bottom: 2px solid transparent;
        }

        .cat-link:hover { color: var(--steam-text); }
        .cat-link.active { color: var(--steam-blue-l); border-bottom-color: var(--steam-blue-l); }

        /* ─── FEATURED / DESTAQUE ─── */
        .featured-section {
            max-width: 1200px;
            margin: 24px auto 0;
            padding: 0 20px;
        }

        .section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--steam-muted);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--steam-border);
        }

        .featured-card {
            position: relative;
            border-radius: 6px;
            overflow: hidden;
            height: 340px;
            cursor: pointer;
            display: flex;
            align-items: flex-end;
            background: var(--steam-card);
        }

        .featured-bg {
            position: absolute; inset: 0;
            background-size: cover;
            background-position: center top;
            transition: transform 6s ease;
        }

        .featured-card:hover .featured-bg { transform: scale(1.03); }

        .featured-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(
                to right,
                rgba(22,32,45,0.98) 0%,
                rgba(22,32,45,0.85) 35%,
                rgba(22,32,45,0.1) 70%,
                transparent 100%
            );
        }

        .featured-info {
            position: relative;
            padding: 28px 32px;
            max-width: 440px;
        }

        .featured-tag {
            display: inline-block;
            background: var(--steam-blue);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 3px 8px;
            border-radius: 2px;
            margin-bottom: 10px;
        }

        .featured-title {
            font-size: 28px;
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 10px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.6);
        }

        .featured-desc {
            font-size: 13px;
            color: var(--steam-text);
            line-height: 1.55;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            opacity: 0.85;
        }

        .featured-price-row {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .featured-price {
            background: var(--steam-green);
            border-radius: 3px;
            padding: 8px 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .featured-price .price-label { font-size: 10px; color: #a4d007; letter-spacing: 0.5px; }
        .featured-price .price-value { font-size: 18px; font-weight: 800; color: #a4d007; }

        .btn-featured {
            background: linear-gradient(to bottom, #4b8ac0, #2c6e9e);
            border: none;
            border-radius: 3px;
            padding: 9px 20px;
            color: #c6e3fa;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-featured:hover { background: linear-gradient(to bottom, #5a9fd4, #3a80b4); color: #fff; }

        /* ─── THUMBNAIL STRIP (mini featured list) ─── */
        .featured-thumbs {
            display: flex;
            flex-direction: column;
            gap: 4px;
            position: absolute;
            right: 0; top: 0; bottom: 0;
            width: 200px;
            padding: 6px;
            background: rgba(22,32,45,0.6);
        }

        .thumb-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px;
            border-radius: 3px;
            cursor: pointer;
            transition: background 0.15s;
            border: 1px solid transparent;
        }

        .thumb-item.active { background: var(--steam-mid); border-color: var(--steam-blue); }
        .thumb-item:hover:not(.active) { background: rgba(255,255,255,0.05); }

        .thumb-img {
            width: 60px; height: 36px;
            border-radius: 2px;
            object-fit: cover;
            background: var(--steam-card);
            flex-shrink: 0;
        }

        .thumb-title {
            font-size: 11px;
            color: var(--steam-text);
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ─── GAMES GRID ─── */
        .games-section {
            max-width: 1200px;
            margin: 32px auto 0;
            padding: 0 20px 60px;
        }

        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
        }

        .game-card {
            background: var(--steam-card);
            border-radius: 4px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            position: relative;
        }

        .game-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.5), 0 0 0 1px var(--steam-blue);
        }

        .game-card-img {
            width: 100%;
            aspect-ratio: 16/9;
            object-fit: cover;
            display: block;
            background: var(--steam-mid);
        }

        .game-card-img-placeholder {
            width: 100%;
            aspect-ratio: 16/9;
            background: linear-gradient(135deg, #1a2c3d, #0d1b2a);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
        }

        .game-card-body {
            padding: 10px 12px 12px;
        }

        .game-card-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--steam-text);
            line-height: 1.3;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .game-card-tags {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .game-tag {
            background: rgba(102,192,244,0.1);
            color: var(--steam-blue-l);
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 2px;
            border: 1px solid rgba(102,192,244,0.2);
        }

        .game-card-price-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .game-price-box {
            background: var(--steam-green);
            border-radius: 3px;
            padding: 4px 8px;
        }

        .game-price-box .price {
            font-size: 14px;
            font-weight: 800;
            color: #a4d007;
        }

        .game-stock-info {
            font-size: 10px;
            color: var(--steam-muted);
        }

        .stock-ok   { color: #90ba3c; }
        .stock-low  { color: #e8bc34; }
        .stock-zero { color: #ff6b6b; }

        /* ─── EMPTY ─── */
        .empty {
            text-align: center;
            padding: 80px 20px;
            color: var(--steam-muted);
            grid-column: 1 / -1;
        }

        .empty-icon { font-size: 56px; margin-bottom: 16px; }
        .empty h3 { font-size: 20px; color: var(--steam-text); margin-bottom: 8px; }

        /* ─── FOOTER ─── */
        footer {
            background: var(--steam-dark);
            border-top: 1px solid rgba(255,255,255,0.05);
            padding: 24px 20px;
            text-align: center;
            color: var(--steam-muted);
            font-size: 12px;
        }

        footer a { color: var(--steam-blue-l); }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 768px) {
            .featured-thumbs { display: none; }
            .featured-card { height: 240px; }
            .featured-title { font-size: 20px; }
            .nav-links { display: none; }
            .games-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 480px) {
            .games-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="nav-inner">
        <div class="brand">
            <div class="brand-icon">🔵</div>
            <div class="brand-name">uTorrent <span>Azul</span></div>
        </div>

        <div class="nav-links">
            <a href="index.php" class="nav-link active">LOJA</a>
            <?php foreach (array_slice($categorias, 0, 5) as $cat): ?>
            <a href="?cat=<?= $cat['id'] ?>" class="nav-link <?= $catId === (int)$cat['id'] ? 'active' : '' ?>">
                <?= strtoupper(htmlspecialchars($cat['nome'])) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="nav-right">
            <form class="search-wrap" method="GET">
                <?php if ($catId): ?>
                    <input type="hidden" name="cat" value="<?= $catId ?>">
                <?php endif; ?>
                <input type="text" name="buscar" placeholder="Buscar jogos..." value="<?= htmlspecialchars($busca) ?>">
                <button type="submit">🔍</button>
            </form>
            <a href="<?= BASE_URL ?>/carrinho.php" class="btn-login" style="position:relative">
                🛒<?php if ($qtdCarrinho): ?> <span style="background:#e74c3c;border-radius:10px;font-size:10px;padding:1px 5px"><?= $qtdCarrinho ?></span><?php endif; ?>
            </a>
            <?php if (!empty($_SESSION['cliente_id'])): ?>
            <a href="<?= BASE_URL ?>/minha_conta.php" class="btn-login">👤 <?= htmlspecialchars($_SESSION['cliente_nome']) ?></a>
            <?php else: ?>
            <a href="<?= BASE_URL ?>/login_cliente.php" class="btn-login">Entrar</a>
            <a href="<?= BASE_URL ?>/registro.php" class="btn-login" style="background:linear-gradient(to bottom,#5c8a3c,#3e6128)">Criar conta</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- SUB NAV CATEGORIAS -->
<div class="sub-nav">
    <div class="sub-nav-inner">
        <a href="<?= BASE_URL ?>/index.php" class="cat-link <?= !$catId && !$busca ? 'active' : '' ?>">Todos os jogos</a>
        <?php foreach ($categorias as $cat): ?>
            <a href="?cat=<?= $cat['id'] ?>" class="cat-link <?= $catId === (int)$cat['id'] ? 'active' : '' ?>">
                <?= htmlspecialchars($cat['nome']) ?>
            </a>
        <?php endforeach; ?>
        <?php if ($busca || $catId): ?>
            <a href="<?= BASE_URL ?>/index.php" class="cat-link" style="color:#ff6b6b">✕ Limpar filtro</a>
        <?php endif; ?>
    </div>
</div>

<!-- DESTAQUE -->
<?php if ($destaque && !$busca && !$catId): ?>
<section class="featured-section">
    <div class="section-label">🔥 Destaque da Semana</div>

    <div class="featured-card" id="featured-card">
        <?php $imgPath = 'admin/public/imagens/' . $destaque['imagem']; ?>
        <div class="featured-bg" id="featured-bg"
            style="background-image: url('<?= BASE_URL ?>/admin/public/imagens/<?= htmlspecialchars($destaque['imagem']) ?>')">
        </div>
        <div class="featured-overlay"></div>

        <div class="featured-info" id="featured-info">
            <div class="featured-tag"><?= htmlspecialchars($destaque['categoria_nome'] ?? 'Destaque') ?></div>
            <div class="featured-title" id="featured-title"><?= htmlspecialchars($destaque['titulo']) ?></div>
            <div class="featured-desc" id="featured-desc"><?= htmlspecialchars($destaque['descricao'] ?? '') ?></div>
            <div class="featured-price-row">
                <div class="featured-price">
                    <span class="price-label">COMPRAR</span>
                    <span class="price-value" id="featured-price">R$ <?= number_format($destaque['preco'], 2, ',', '.') ?></span>
                </div>
                <a href="<?= BASE_URL ?>/carrinho.php?acao=adicionar&id=<?= $destaque['id'] ?>"
                   class="btn-featured">Adicionar ao carrinho →</a>
            </div>
        </div>

        <!-- MINI LISTA LATERAL -->
        <div class="featured-thumbs">
            <?php foreach (array_slice($todosGames, 0, 6) as $i => $g): ?>
            <div class="thumb-item <?= $g['id'] == $destaque['id'] ? 'active' : '' ?>"
                 onclick="trocarDestaque(<?= $g['id'] ?>, '<?= addslashes(htmlspecialchars($g['titulo'])) ?>', '<?= addslashes(htmlspecialchars($g['descricao'] ?? '')) ?>', '<?= BASE_URL ?>/admin/public/imagens/<?= htmlspecialchars($g['imagem']) ?>', 'R$ <?= number_format($g['preco'], 2, ',', '.') ?>', this)"
                 data-id="<?= $g['id'] ?>">
                <?php if ($g['imagem'] && file_exists('admin/public/imagens/' . $g['imagem'])): ?>
                    <img src="<?= BASE_URL ?>/admin/public/imagens/<?= htmlspecialchars($g['imagem']) ?>" class="thumb-img" alt="">
                <?php else: ?>
                    <div class="thumb-img" style="display:flex;align-items:center;justify-content:center;font-size:16px">🎮</div>
                <?php endif; ?>
                <div class="thumb-title"><?= htmlspecialchars($g['titulo']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- GRID DE JOGOS -->
<section class="games-section">
    <div class="section-label">
        <?= ($busca || $catId) ? '🔎 Resultados' : '🕹️ Catálogo Completo' ?>
        &nbsp;<span style="color:var(--steam-blue-l);font-weight:700;text-transform:none;letter-spacing:0"><?= count($games) ?> jogos</span>
    </div>

    <div class="games-grid">
        <?php if (count($games) > 0):
            foreach ($games as $g):
                $est = (int)$g['estoque'];
                $stockClass = $est === 0 ? 'stock-zero' : ($est <= 5 ? 'stock-low' : 'stock-ok');
                $stockText  = $est === 0 ? 'Esgotado' : ($est <= 5 ? "Últimas {$est}" : "{$est} em estoque");
                $imgFile    = 'admin/public/imagens/' . $g['imagem'];
                $catNome = strtolower($g['categoria_nome'] ?? '');
                $emoji = match($catNome) {
                    'ação','acao' => '⚔️', 'aventura' => '🗺️', 'rpg' => '🧙',
                    'sandbox' => '🏗️', 'esportes' => '⚽', default => '🎮',
                };
        ?>
        <div class="game-card">
            <?php if ($g['imagem'] && file_exists($imgFile)): ?>
                <img src="<?= BASE_URL ?>/admin/public/imagens/<?= htmlspecialchars($g['imagem']) ?>"
                     class="game-card-img" alt="<?= htmlspecialchars($g['titulo']) ?>">
            <?php else: ?>
                <div class="game-card-img-placeholder"><?= $emoji ?></div>
            <?php endif; ?>

            <div class="game-card-body">
                <div class="game-card-title"><?= htmlspecialchars($g['titulo']) ?></div>
                <div class="game-card-tags">
                    <span class="game-tag"><?= htmlspecialchars($g['categoria_nome'] ?? 'Geral') ?></span>
                </div>
                <div class="game-card-price-row">
                    <div class="game-price-box">
                        <span class="price">R$ <?= number_format($g['preco'], 2, ',', '.') ?></span>
                    </div>
                    <?php if ($est > 0): ?>
                    <a href="<?= BASE_URL ?>/carrinho.php?acao=adicionar&id=<?= $g['id'] ?>"
                       class="game-stock-info stock-ok" style="cursor:pointer;background:var(--steam-green);color:#fff;padding:3px 10px;border-radius:3px;font-size:11px;text-decoration:none">
                        🛒 Comprar
                    </a>
                    <?php else: ?>
                    <span class="game-stock-info <?= $stockClass ?>"><?= $stockText ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach;
        else: ?>
        <div class="empty">
            <div class="empty-icon">🔍</div>
            <h3>Nenhum jogo encontrado</h3>
            <p>Tente outro termo ou remova os filtros.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<footer>
    <p>© 2025 <strong style="color:#c6d4df">uTorrent Azul</strong> — Sistema de Gestão para Loja de Jogos
       &nbsp;|&nbsp; <a href="<?= BASE_URL ?>/login.php">Área Administrativa</a>
    </p>
</footer>

<script>
function trocarDestaque(id, titulo, desc, imgUrl, preco, el) {
    document.getElementById('featured-bg').style.backgroundImage = `url('${imgUrl}')`;
    document.getElementById('featured-title').textContent = titulo;
    document.getElementById('featured-desc').textContent = desc;
    document.getElementById('featured-price').textContent = preco;
    document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}
</script>
</body>
</html>
