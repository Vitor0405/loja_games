<?php
session_start();
require_once '../config/conexao.php';
require_once '../config/auth.php';
apenasAdmin();
require_once '../models/usuarios.php';

$pageTitle  = 'Gerenciar Usuários';
$pageActive = 'usuarios';

$usuario = new Usuario();
$usuarios = $usuario->listarUsuarios();

$msgSucesso = $_GET['deletado'] ?? '';

require_once 'partials/header.php';
?>

<?php if ($msgSucesso): ?>
    <div class="alert alert-success" data-autohide>✅ Usuário excluído com sucesso.</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <div class="card-title">👥 Usuários do Sistema</div>
        <div class="d-flex gap-8">
            <span class="badge badge-purple"><?= count($usuarios) ?> usuário(s)</span>
            <a href="cadastrar_usuario.php" class="btn btn-primary btn-sm">➕ Novo Usuário</a>
        </div>
    </div>

    <?php if (count($usuarios) > 0): ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th>Usuário</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th>Cadastrado em</th>
                    <th style="width:120px">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td class="text-muted fs-12"><?= $u['id'] ?></td>
                    <td>
                        <div class="d-flex align-center gap-12">
                            <div style="
                                width:36px;height:36px;border-radius:50%;
                                background:linear-gradient(135deg,var(--purple),var(--cyan));
                                display:flex;align-items:center;justify-content:center;
                                font-weight:700;font-size:14px;color:#fff;flex-shrink:0
                            "><?= strtoupper(substr($u['nome'], 0, 1)) ?></div>
                            <div class="fw-bold"><?= htmlspecialchars($u['nome']) ?></div>
                        </div>
                    </td>
                    <td class="text-muted"><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <?php if ($u['tipo'] === 'admin'): ?>
                            <span class="badge badge-purple">👑 Admin</span>
                        <?php else: ?>
                            <span class="badge badge-cyan">👤 Usuário</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted fs-12"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <div class="td-actions">
                            <a href="editar_usuario.php?id=<?= $u['id'] ?>" class="btn btn-warning btn-sm">✏️</a>
                            <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                            <button
                                class="btn btn-danger btn-sm"
                                onclick="confirmarExclusao('<?= addslashes(htmlspecialchars($u['nome'])) ?>',
                                    '<?= BASE_URL ?>/admin/controllers/deletar_usuario.php?id=<?= $u['id'] ?>')"
                            >🗑️</button>
                            <?php else: ?>
                                <span class="text-muted fs-12" title="Você não pode excluir sua própria conta">🔒</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">👥</div>
            <p>Nenhum usuário cadastrado.</p>
            <a href="cadastrar_usuario.php" class="btn btn-primary" style="margin-top:16px">➕ Cadastrar usuário</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'partials/footer.php'; ?>
