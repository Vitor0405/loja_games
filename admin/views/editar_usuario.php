<?php
session_start();
require_once '../config/conexao.php';
require_once '../config/auth.php';
apenasAdmin();
require_once '../models/usuarios.php';

$pageTitle  = 'Editar Usuário';
$pageActive = 'usuarios';

$usuario = new Usuario();
$dados   = null;
$mensagem = '';
$erro     = '';

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $dados = $usuario->buscarPorId($id);
}

if (!$dados) {
    require_once 'partials/header.php';
    echo '<div class="alert alert-danger">❌ Usuário não encontrado. <a href="ver_usuarios.php">Voltar</a></div>';
    require_once 'partials/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $tipo  = $_POST['tipo'] ?? 'user';

    if (!$nome || !$email) {
        $erro = 'Nome e e-mail são obrigatórios.';
    } elseif ($senha && strlen($senha) < 6) {
        $erro = 'A senha deve ter no mínimo 6 caracteres.';
    } elseif ($usuario->emailExiste($email, $id)) {
        $erro = 'Este e-mail já está em uso por outro usuário.';
    } else {
        $editar = new Usuario([
            'id'    => $id,
            'nome'  => $nome,
            'email' => $email,
            'senha' => $senha,
            'tipo'  => $tipo,
        ]);
        $alterarSenha = !empty($senha);
        if ($editar->editar($alterarSenha)) {
            $dados = $usuario->buscarPorId($id);
            $mensagem = 'Usuário atualizado com sucesso!';
        } else {
            $erro = 'Erro ao atualizar usuário.';
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
        <div class="card-title">✏️ Editando: <?= htmlspecialchars($dados['nome']) ?></div>
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
                        value="<?= htmlspecialchars($dados['nome']) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">E-mail *</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="<?= htmlspecialchars($dados['email']) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Nova Senha</label>
                    <input
                        type="password"
                        name="senha"
                        class="form-control"
                        placeholder="Deixe em branco para manter a atual"
                    >
                    <div class="form-hint">Preencha apenas se quiser alterar a senha.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Perfil</label>
                    <select name="tipo" class="form-control" <?= $dados['id'] == $_SESSION['usuario_id'] ? 'disabled' : '' ?>>
                        <option value="user"  <?= $dados['tipo'] === 'user'  ? 'selected' : '' ?>>👤 Usuário</option>
                        <option value="admin" <?= $dados['tipo'] === 'admin' ? 'selected' : '' ?>>👑 Administrador</option>
                    </select>
                    <?php if ($dados['id'] == $_SESSION['usuario_id']): ?>
                        <input type="hidden" name="tipo" value="<?= $dados['tipo'] ?>">
                        <div class="form-hint">Você não pode alterar seu próprio perfil.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-actions" style="margin-top:8px">
                <button type="submit" class="btn btn-primary btn-lg">💾 Salvar Alterações</button>
                <a href="ver_usuarios.php" class="btn btn-secondary btn-lg">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
