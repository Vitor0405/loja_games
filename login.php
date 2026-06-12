<?php
session_start();
require_once 'admin/config/conexao.php';
require_once 'admin/models/usuarios.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/admin/views/gerenciar_games.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if ($email && $senha) {
        $usuario = new Usuario();
        $dados = $usuario->autenticar($email, $senha);

        if (!$dados) {
            $dadosLegado = $usuario->buscarPorEmail($email);
            if ($dadosLegado && $dadosLegado['senha'] === hash('sha256', $senha)) {
                $dados = $dadosLegado;
            }
        }

        if ($dados) {
            $_SESSION['usuario_id']   = $dados['id'];
            $_SESSION['usuario_nome'] = $dados['nome'];
            $_SESSION['usuario_tipo'] = $dados['tipo'];
            header('Location: ' . BASE_URL . '/admin/views/gerenciar_games.php');
            exit;
        } else {
            $erro = 'E-mail ou senha incorretos.';
        }
    } else {
        $erro = 'Preencha todos os campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar — uTorrent Azul</title>
    <style>
        :root {
            --steam-bg:     #1b2838;
            --steam-dark:   #171a21;
            --steam-card:   #16202d;
            --steam-mid:    #2a475e;
            --steam-blue:   #1a9fff;
            --steam-blue-l: #66c0f4;
            --steam-text:   #c6d4df;
            --steam-muted:  #7a9bb5;
            --steam-border: #2a475e;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: var(--steam-bg);
            color: var(--steam-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ─── NAVBAR ─── */
        .navbar {
            background: var(--steam-dark);
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-icon {
            width: 30px; height: 30px;
            background: linear-gradient(135deg, var(--steam-blue), #0a5fb4);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
        }

        .brand-name {
            font-size: 17px;
            font-weight: 800;
            color: #fff;
        }

        .brand-name span { color: var(--steam-blue-l); }

        /* ─── BG HERO ─── */
        .login-bg {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
            background:
                radial-gradient(ellipse 80% 60% at 50% 0%, rgba(26,159,255,0.08) 0%, transparent 70%),
                radial-gradient(ellipse 60% 50% at 20% 80%, rgba(26,159,255,0.05) 0%, transparent 70%);
        }

        /* ─── CARD ─── */
        .login-card {
            width: 100%;
            max-width: 400px;
            background: var(--steam-card);
            border: 1px solid var(--steam-border);
            border-radius: 4px;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(to bottom, #2a475e, #1b3a52);
            padding: 18px 24px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .card-header h1 {
            font-size: 16px;
            font-weight: 700;
            color: var(--steam-blue-l);
            letter-spacing: 0.3px;
        }

        .card-header p {
            font-size: 12px;
            color: var(--steam-muted);
            margin-top: 4px;
        }

        .card-body { padding: 24px; }

        .form-group { margin-bottom: 16px; }

        label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: var(--steam-muted);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            background: var(--steam-mid);
            border: 1px solid var(--steam-border);
            border-radius: 3px;
            padding: 10px 12px;
            color: var(--steam-text);
            font-size: 14px;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        input:focus {
            outline: none;
            border-color: var(--steam-blue);
            box-shadow: 0 0 0 2px rgba(26,159,255,0.2);
        }

        input::placeholder { color: var(--steam-muted); }

        .btn-signin {
            width: 100%;
            background: linear-gradient(to bottom, #4b8ac0, #2c6e9e);
            border: none;
            border-radius: 3px;
            padding: 11px;
            color: #c6e3fa;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
            margin-top: 6px;
            letter-spacing: 0.3px;
        }

        .btn-signin:hover {
            background: linear-gradient(to bottom, #5a9fd4, #3a80b4);
            color: #fff;
        }

        .error-box {
            background: rgba(180,50,50,0.15);
            border: 1px solid rgba(200,50,50,0.4);
            color: #ff8a8a;
            border-radius: 3px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .divider {
            border: none;
            border-top: 1px solid var(--steam-border);
            margin: 18px 0;
        }

        .test-hint {
            background: rgba(26,159,255,0.07);
            border: 1px solid rgba(26,159,255,0.2);
            border-radius: 3px;
            padding: 10px 14px;
            font-size: 12px;
            color: var(--steam-muted);
            line-height: 1.6;
        }

        .test-hint strong { color: var(--steam-blue-l); }

        .card-footer {
            background: rgba(0,0,0,0.15);
            border-top: 1px solid rgba(255,255,255,0.04);
            padding: 14px 24px;
            text-align: center;
            font-size: 12px;
            color: var(--steam-muted);
        }

        .card-footer a {
            color: var(--steam-blue-l);
            text-decoration: none;
        }

        .card-footer a:hover { text-decoration: underline; }

        footer {
            background: var(--steam-dark);
            border-top: 1px solid rgba(255,255,255,0.05);
            padding: 16px;
            text-align: center;
            font-size: 11px;
            color: var(--steam-muted);
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="brand">
        <div class="brand-icon">🔵</div>
        <div class="brand-name">uTorrent <span>Azul</span></div>
    </div>
</nav>

<div class="login-bg">
    <div class="login-card">
        <div class="card-header">
            <h1>ENTRAR NA CONTA</h1>
            <p>Acesse o painel de administração</p>
        </div>

        <div class="card-body">
            <?php if ($erro): ?>
                <div class="error-box">⚠️ <?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="email">Endereço de e-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="seu@email.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required
                        autocomplete="email"
                    >
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    >
                </div>

                <button type="submit" class="btn-signin">ENTRAR</button>
            </form>

            <hr class="divider">

            <div class="test-hint">
                🧪 <strong>Contas de teste:</strong><br>
                Admin: <strong>admin@gamestock.com</strong> / <strong>admin123</strong><br>
                Usuário: <strong>pedro@gmail.com</strong> / <strong>user123</strong>
            </div>
        </div>

        <div class="card-footer">
            <a href="<?= BASE_URL ?>/index.php">← Voltar para a loja</a>
        </div>
    </div>
</div>

<footer>© 2025 uTorrent Azul — Sistema de Gestão para Loja de Jogos</footer>

</body>
</html>
