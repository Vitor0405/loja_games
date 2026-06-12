<?php
session_start();
require_once '../config/conexao.php';
require_once '../config/auth.php';
apenasAdmin();
require_once '../models/usuarios.php';

$pageTitle  = 'Cadastrar Usuário';
$pageActive = 'novo-usuario';

$mensagem = '';
$erro     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $tipo  = $_POST['tipo'] ?? 'user';

    if (!$nome || !$email || !$senha) {
        $erro = 'Todos os campos são obrigatórios.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter no mínimo 6 caracteres.';
    } else {
        $usuario = new Usuario();
        if ($usuario->emailExiste($email)) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            $novo = new Usuario([
                'nome'  => $nome,
                'email' => $email,
                'senha' => $senha,
                'tipo'  => $tipo,
            ]);
            if ($novo->cadastrar()) {
                $mensagem = 'Usuário cadastrado com sucesso!';
            } else {
                $erro = 'Erro ao cadastrar usuário.';
            }
        }
    }
}

require_once 'partials/header.php';
?>

<?php if ($mensagem): ?>
    <div class="alert alert-success" data-autohide>✅ <?= htmlspecialchars($mensagem) ?></div>
<?php endif; ?>
<?php if ($erro): ?>
    <div class="alert alert-danger">❌ <?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <div class="card-title">👤 Dados do Usuário</div>
        <a href="ver_usuarios.php" class="btn btn-secondary btn-sm">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Nome *</label>
                    <input
                        type="text"
                        name="nome"
                        class="form-control"
                        placeholder="Nome completo"
                        value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">E-mail *</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="email@exemplo.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Senha *</label>
                    <input
                        type="password"
                        name="senha"
                        class="form-control"
                        placeholder="Mínimo 6 caracteres"
                        required
                    >
                    <div class="form-hint">Mínimo de 6 caracteres.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Perfil</label>
                    <select name="tipo" class="form-control">
                        <option value="user"  <?= ($_POST['tipo'] ?? 'user') === 'user'  ? 'selected' : '' ?>>👤 Usuário</option>
                        <option value="admin" <?= ($_POST['tipo'] ?? '') === 'admin' ? 'selected' : '' ?>>👑 Administrador</option>
                    </select>
                </div>
            </div>

            <div class="form-actions" style="margin-top:8px">
                <button type="submit" class="btn btn-primary btn-lg">✅ Cadastrar Usuário</button>
                <a href="ver_usuarios.php" class="btn btn-secondary btn-lg">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
