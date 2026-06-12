<?php
require_once __DIR__ . '/admin/config/conexao.php';
require_once __DIR__ . '/admin/models/Cliente.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!empty($_SESSION['cliente_id'])) {
    header('Location: ' . BASE_URL . '/minha_conta.php');
    exit;
}

$erro  = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha    = $_POST['senha'] ?? '';
    $confirma = $_POST['confirmar_senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== $confirma) {
        $erro = 'As senhas não coincidem.';
    } else {
        $cli = new Cliente();
        if ($cli->emailExiste($email)) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            if ($cli->cadastrar(['nome' => $nome, 'email' => $email, 'telefone' => $telefone, 'senha' => $senha])) {
                header('Location: ' . BASE_URL . '/login_cliente.php?registro=1');
                exit;
            } else {
                $erro = 'Erro ao realizar o cadastro. Tente novamente.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta — uTorrent Azul</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/public/css/style.css">
    <style>
        body { display:flex; justify-content:center; align-items:center; min-height:100vh; padding:20px; }
        .register-card {
            background:var(--bg-card);
            border:1px solid var(--border);
            border-radius:6px;
            padding:40px;
            width:100%;
            max-width:460px;
        }
        .register-card h1 { font-size:22px; color:var(--text); margin:0 0 6px; }
        .register-card p  { font-size:13px; color:var(--text-muted); margin:0 0 28px; }
        .divider { text-align:center; margin:20px 0; color:var(--text-muted); font-size:12px; }
    </style>
</head>
<body>
<div class="register-card">
    <div style="text-align:center;margin-bottom:24px">
        <div style="font-size:36px">🔵</div>
        <h1>uTorrent Azul</h1>
        <p>Crie sua conta e comece a comprar</p>
    </div>

    <?php if ($erro): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
    <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?>
        <br><a href="<?= BASE_URL ?>/login_cliente.php" style="color:var(--steam-blue)">Ir para o login →</a>
    </div>
    <?php endif; ?>

    <?php if (!$sucesso): ?>
    <form method="POST" novalidate id="form-registro">
        <div class="form-group">
            <label class="form-label">Nome completo *</label>
            <input type="text" name="nome" class="form-control"
                   value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                   placeholder="Seu nome" required>
        </div>
        <div class="form-group">
            <label class="form-label">E-mail *</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   placeholder="seu@email.com" required>
        </div>
        <div class="form-group">
            <label class="form-label">Telefone</label>
            <input type="text" name="telefone" class="form-control"
                   value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>"
                   placeholder="(XX) XXXXX-XXXX">
        </div>
        <div class="form-group">
            <label class="form-label">Senha *</label>
            <input type="password" name="senha" class="form-control"
                   placeholder="Mínimo 6 caracteres" required id="senha">
        </div>
        <div class="form-group">
            <label class="form-label">Confirmar senha *</label>
            <input type="password" name="confirmar_senha" class="form-control"
                   placeholder="Repita a senha" required id="confirmar">
            <small id="msg-senha" style="color:var(--danger);font-size:12px;display:none">As senhas não coincidem.</small>
        </div>
        <button type="submit" class="btn btn-primary w-100" style="margin-top:8px">Criar conta</button>
    </form>
    <?php endif; ?>

    <div class="divider">────── ou ──────</div>
    <div style="text-align:center;font-size:13px">
        Já tem conta?
        <a href="<?= BASE_URL ?>/login_cliente.php" style="color:var(--steam-blue)">Fazer login</a>
        &nbsp;·&nbsp;
        <a href="<?= BASE_URL ?>/index.php" style="color:var(--text-muted)">Voltar à loja</a>
    </div>
</div>

<script>
document.getElementById('confirmar')?.addEventListener('input', function () {
    const msg = document.getElementById('msg-senha');
    msg.style.display = this.value && this.value !== document.getElementById('senha').value ? 'block' : 'none';
});
document.getElementById('form-registro')?.addEventListener('submit', function (e) {
    const s = document.getElementById('senha').value;
    const c = document.getElementById('confirmar').value;
    if (s !== c) { e.preventDefault(); document.getElementById('msg-senha').style.display = 'block'; }
});
</script>
</body>
</html>
