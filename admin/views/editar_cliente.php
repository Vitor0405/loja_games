<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/auth.php';
apenasAdmin();
require_once __DIR__ . '/../models/Cliente.php';

$pageTitle  = 'Editar Cliente';
$pageActive = 'clientes';

$cli    = new Cliente();
$id     = (int)($_GET['id'] ?? 0);
$cliente = $cli->buscarPorId($id);
$erro   = '';

if (!$cliente) {
    header('Location: ' . BASE_URL . '/admin/views/listar_clientes.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'nome'     => trim($_POST['nome'] ?? ''),
        'email'    => trim($_POST['email'] ?? ''),
        'telefone' => trim($_POST['telefone'] ?? ''),
        'ativo'    => isset($_POST['ativo']) ? 1 : 0,
        'senha'    => trim($_POST['senha'] ?? ''),
    ];
    $alterarSenha = !empty($dados['senha']);

    if (empty($dados['nome']) || empty($dados['email'])) {
        $erro = 'Nome e e-mail são obrigatórios.';
    } elseif ($cli->emailExiste($dados['email'], $id)) {
        $erro = 'Este e-mail já está em uso por outro cliente.';
    } else {
        if ($cli->editar($id, $dados, $alterarSenha)) {
            header('Location: ' . BASE_URL . '/admin/views/listar_clientes.php?salvo=1');
            exit;
        }
        $erro = 'Erro ao atualizar o cliente.';
    }
    $cliente = array_merge($cliente, $dados);
}

require_once __DIR__ . '/partials/header.php';
?>

<?php if ($erro): ?>
<div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div class="card" style="max-width:560px">
    <form method="POST" novalidate>
        <div class="form-group">
            <label class="form-label">Nome *</label>
            <input type="text" name="nome" class="form-control"
                   value="<?= htmlspecialchars($cliente['nome']) ?>" required>
        </div>
        <div class="form-group">
            <label class="form-label">E-mail *</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($cliente['email']) ?>" required>
        </div>
        <div class="form-group">
            <label class="form-label">Telefone</label>
            <input type="text" name="telefone" class="form-control"
                   value="<?= htmlspecialchars($cliente['telefone'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label">Nova senha <span class="text-muted fs-12">(deixe em branco para manter a atual)</span></label>
            <input type="password" name="senha" class="form-control"
                   placeholder="Nova senha (opcional)" autocomplete="new-password">
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                <input type="checkbox" name="ativo" value="1"
                       <?= $cliente['ativo'] ? 'checked' : '' ?>>
                Cliente ativo
            </label>
        </div>
        <div class="d-flex gap-10">
            <button type="submit" class="btn btn-primary">💾 Salvar</button>
            <a href="<?= BASE_URL ?>/admin/views/listar_clientes.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
