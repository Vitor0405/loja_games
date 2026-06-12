<?php
require_once __DIR__ . '/admin/config/conexao.php';
require_once __DIR__ . '/admin/models/Cliente.php';
require_once __DIR__ . '/admin/models/Pedido.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['cliente_id'])) {
    header('Location: ' . BASE_URL . '/login_cliente.php');
    exit;
}

$cli     = new Cliente();
$ped     = new Pedido();
$cliente = $cli->buscarPorId((int)$_SESSION['cliente_id']);
$pedidos = $ped->buscarPorCliente((int)$_SESSION['cliente_id']);

$cores = ['pendente' => '#e3a000', 'aprovado' => '#4caf50', 'cancelado' => '#e74c3c'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta — uTorrent Azul</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/public/css/style.css">
    <style>
        .conta-wrap { max-width:860px; margin:0 auto; padding:30px 20px; }
        .perfil-card {
            background:var(--bg-card); border:1px solid var(--border);
            border-radius:6px; padding:24px; margin-bottom:24px;
            display:flex; align-items:center; gap:20px;
        }
        .avatar {
            width:60px; height:60px; border-radius:50%;
            background:var(--steam-blue); display:flex; align-items:center;
            justify-content:center; font-size:24px; font-weight:700;
            color:#fff; flex-shrink:0;
        }
        .pedido-row {
            background:var(--bg-card); border:1px solid var(--border);
            border-radius:6px; padding:16px; margin-bottom:10px;
            display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;
        }
    </style>
</head>
<body style="background:var(--bg-body)">

<nav style="background:var(--bg-nav);border-bottom:1px solid var(--border);padding:0 24px;display:flex;align-items:center;gap:16px;height:52px">
    <a href="<?= BASE_URL ?>/index.php" style="font-weight:700;color:var(--text);text-decoration:none">🔵 uTorrent Azul</a>
    <span style="color:var(--border)">|</span>
    <span style="color:var(--text-muted)">Minha Conta</span>
    <span style="flex:1"></span>
    <a href="<?= BASE_URL ?>/logout_cliente.php" style="color:var(--danger);font-size:13px">Sair</a>
</nav>

<div class="conta-wrap">

    <?php if (isset($_GET['pedido'])): ?>
    <div class="alert alert-success">✅ Pedido realizado com sucesso! Aguarde a confirmação.</div>
    <?php endif; ?>

    <div class="perfil-card">
        <div class="avatar"><?= strtoupper($cliente['nome'][0]) ?></div>
        <div>
            <h2 style="margin:0;font-size:18px;color:var(--text)"><?= htmlspecialchars($cliente['nome']) ?></h2>
            <div style="color:var(--text-muted);font-size:13px"><?= htmlspecialchars($cliente['email']) ?></div>
            <?php if ($cliente['telefone']): ?>
            <div style="color:var(--text-muted);font-size:13px"><?= htmlspecialchars($cliente['telefone']) ?></div>
            <?php endif; ?>
        </div>
        <a href="<?= BASE_URL ?>/index.php" class="btn btn-secondary" style="margin-left:auto">
            🔵 Ver loja
        </a>
    </div>

    <h3 style="color:var(--text);margin:0 0 16px">Meus Pedidos (<?= count($pedidos) ?>)</h3>

    <?php if (empty($pedidos)): ?>
    <div style="text-align:center;padding:40px;color:var(--text-muted);background:var(--bg-card);border:1px solid var(--border);border-radius:6px">
        <p>Você ainda não fez nenhum pedido.</p>
        <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary">Explorar jogos</a>
    </div>
    <?php else: ?>
    <?php foreach ($pedidos as $p): ?>
    <div class="pedido-row">
        <div>
            <div style="font-weight:700;color:var(--text)">Pedido #<?= $p['id'] ?></div>
            <div style="font-size:12px;color:var(--text-muted)">
                <?= date('d/m/Y \à\s H:i', strtotime($p['created_at'])) ?>
            </div>
        </div>
        <div style="text-align:right">
            <div style="font-size:16px;font-weight:700;color:var(--steam-green)">
                R$ <?= number_format($p['total'], 2, ',', '.') ?>
            </div>
            <span style="font-size:12px;font-weight:600;color:<?= $cores[$p['status']] ?? '#888' ?>">
                ● <?= ucfirst($p['status']) ?>
            </span>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
