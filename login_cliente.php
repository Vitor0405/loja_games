<?php
require_once __DIR__ . '/admin/config/conexao.php';
require_once __DIR__ . '/admin/models/Cliente.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!empty($_SESSION['cliente_id'])) {
    header('Location: ' . BASE_URL . '/minha_conta.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $cli     = new Cliente();
    $cliente = $cli->autenticar($email, $senha);

    if ($cliente) {
        $_SESSION['cliente_id']   = $cliente['id'];
        $_SESSION['cliente_nome'] = $cliente['nome'];
        $destino = $_SESSION['redirect_apos_login'] ?? BASE_URL . '/minha_conta.php';
        unset($_SESSION['redirect_apos_login']);
        header('Location: ' . $destino);
        exit;
    }
    $erro = 'E-mail ou senha incorretos.';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — uTorrent Azul</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/public/css/style.css">
    <style>
        body { display:flex; justify-content:center; align-items:center; min-height:100vh; }
        .login-card {
            background:var(--bg-card);
            border:1px solid var(--border);
            border-radius:6px;
            padding:40px;
            width:100%;
            max-width:400px;
        }
        .login-card h1 { font-size:22px; color:var(--text); margin:0 0 6px; }
        .login-card p  { font-size:13px; color:var(--text-muted); margin:0 0 28px; }
        .divider { text-align:center; margin:20px 0; color:var(--text-muted); font-size:12px; }
    </style>
</head>
<body>
<div class="login-card">
    <div style="text-align:center;margin-bottom:24px">
        <div style="font-size:36px">🔵</div>
        <h1>uTorrent Azul</h1>
        <p>Entre na sua conta</p>
    </div>

    <?php if (!empty($_GET['registro'])): ?>
    <div class="alert alert-success">Conta criada! Faça login para continuar.</div>
    <?php endif; ?>
    <?php if ($erro): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="form-group">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   placeholder="seu@email.com" required autofocus>
        </div>
        <div class="form-group">
            <label class="form-label">Senha</label>
            <input type="password" name="senha" class="form-control"
                   placeholder="Sua senha" required>
        </div>
        <button type="submit" class="btn btn-primary w-100" style="margin-top:8px">Entrar</button>
    </form>

    <div class="divider">────── ou ──────</div>
    <div style="text-align:center;font-size:13px">
        Não tem conta?
        <a href="<?= BASE_URL ?>/registro.php" style="color:var(--steam-blue)">Criar conta grátis</a>
        <br><br>
        <a href="<?= BASE_URL ?>/index.php" style="color:var(--text-muted)">← Voltar à loja</a>
    </div>
</div>
</body>
</html>
