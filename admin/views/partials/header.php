<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — uTorrent Azul</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/public/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔵</text></svg>">
</head>
<body>
<div class="admin-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">🔵</div>
            <div>
                <h1>uTorrent <span>Azul</span></h1>
                <small>Painel Admin</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Principal</div>
            <a href="<?= BASE_URL ?>/admin/views/gerenciar_games.php"
               class="nav-link <?= ($pageActive ?? '') === 'dashboard' ? 'active' : '' ?>">
                <span class="icon">📊</span> Dashboard
            </a>

            <div class="nav-section-label">Catálogo</div>
            <a href="<?= BASE_URL ?>/admin/views/gerenciar_games.php"
               class="nav-link <?= ($pageActive ?? '') === 'games' ? 'active' : '' ?>">
                <span class="icon">🕹️</span> Gerenciar Jogos
            </a>
            <a href="<?= BASE_URL ?>/admin/views/cadastrar_game.php"
               class="nav-link <?= ($pageActive ?? '') === 'novo-game' ? 'active' : '' ?>">
                <span class="icon">➕</span> Novo Jogo
            </a>
            <a href="<?= BASE_URL ?>/admin/views/listar_categorias.php"
               class="nav-link <?= ($pageActive ?? '') === 'categorias' ? 'active' : '' ?>">
                <span class="icon">🏷️</span> Categorias
            </a>

            <div class="nav-section-label">Clientes & Vendas</div>
            <a href="<?= BASE_URL ?>/admin/views/listar_clientes.php"
               class="nav-link <?= ($pageActive ?? '') === 'clientes' ? 'active' : '' ?>">
                <span class="icon">👤</span> Clientes
            </a>
            <a href="<?= BASE_URL ?>/admin/views/listar_pedidos.php"
               class="nav-link <?= ($pageActive ?? '') === 'pedidos' ? 'active' : '' ?>">
                <span class="icon">📦</span> Pedidos
            </a>

            <?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin'): ?>
            <div class="nav-section-label">Usuários Admin</div>
            <a href="<?= BASE_URL ?>/admin/views/ver_usuarios.php"
               class="nav-link <?= ($pageActive ?? '') === 'usuarios' ? 'active' : '' ?>">
                <span class="icon">👥</span> Gerenciar Usuários
            </a>
            <a href="<?= BASE_URL ?>/admin/views/cadastrar_usuario.php"
               class="nav-link <?= ($pageActive ?? '') === 'novo-usuario' ? 'active' : '' ?>">
                <span class="icon">➕</span> Novo Usuário
            </a>
            <?php endif; ?>

            <div class="nav-section-label">Loja</div>
            <a href="<?= BASE_URL ?>/index.php" class="nav-link" target="_blank">
                <span class="icon">🏪</span> Ver Vitrine
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">
                    <?= strtoupper(substr($_SESSION['usuario_nome'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="sidebar-user-info">
                    <div class="user-name"><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?></div>
                    <div class="user-role">
                        <?= ($_SESSION['usuario_tipo'] ?? '') === 'admin' ? '👑 Admin' : '👤 Usuário' ?>
                    </div>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/logout.php" class="btn btn-secondary w-100">🚪 Sair</a>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main-content">
        <div class="topbar">
            <h2><?= htmlspecialchars($pageTitle ?? 'Painel') ?></h2>
            <div class="topbar-actions">
                <span class="text-muted fs-12">
                    Olá, <strong style="color:var(--text)"><?= htmlspecialchars($_SESSION['usuario_nome'] ?? '') ?></strong>
                </span>
            </div>
        </div>
        <div class="page-body">
