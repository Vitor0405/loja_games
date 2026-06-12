<?php
session_start();
require_once '../config/conexao.php';
require_once '../config/auth.php';
require_once '../models/games.php';

$pageTitle  = 'Editar Jogo';
$pageActive = 'games';

$g_model  = new Game();
$cats     = $g_model->listarCategorias();
$dados    = null;
$mensagem = '';
$erro     = '';

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $dados = $g_model->buscarGamePorId($id);
}

if (!$dados) {
    require_once 'partials/header.php';
    echo '<div class="alert alert-danger">❌ Jogo não encontrado. <a href="gerenciar_games.php">Voltar</a></div>';
    require_once 'partials/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo       = trim($_POST['titulo']       ?? '');
    $descricao    = trim($_POST['descricao']    ?? '');
    $preco        = trim($_POST['preco']        ?? '');
    $estoque      = (int)($_POST['estoque']     ?? 0);
    $categoria_id = (int)($_POST['categoria_id'] ?? 0) ?: null;
    $imagemNome   = $_POST['imagem_atual']      ?? $dados['imagem'];

    if (!$titulo || !$preco) {
        $erro = 'Título e preço são obrigatórios.';
    } else {
        if (!empty($_FILES['imagem']['name'])) {
            $ext     = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (!in_array($ext, $allowed)) {
                $erro = 'Tipo de imagem inválido.';
            } else {
                $novoNome = uniqid('game_') . '.' . $ext;
                $destino  = '../public/imagens/' . $novoNome;
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                    $imagemNome = $novoNome;
                } else {
                    $erro = 'Falha ao fazer upload da imagem.';
                }
            }
        }

        if (!$erro) {
            $editar = new Game([
                'id'           => $id,
                'titulo'       => $titulo,
                'descricao'    => $descricao,
                'preco'        => $preco,
                'estoque'      => $estoque,
                'categoria_id' => $categoria_id,
                'imagem'       => $imagemNome,
            ]);
            if ($editar->editarGame()) {
                $dados    = $g_model->buscarGamePorId($id);
                $mensagem = 'Jogo atualizado com sucesso!';
            } else {
                $erro = 'Erro ao atualizar no banco de dados.';
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
        <div class="card-title">✏️ Editando: <?= htmlspecialchars($dados['titulo']) ?></div>
        <a href="gerenciar_games.php" class="btn btn-secondary btn-sm">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="imagem_atual" value="<?= htmlspecialchars($dados['imagem'] ?? '') ?>">
            <div class="form-grid">
                <div class="form-group form-col-full">
                    <label class="form-label">Título *</label>
                    <input type="text" name="titulo" class="form-control"
                           value="<?= htmlspecialchars($dados['titulo']) ?>" required>
                </div>

                <div class="form-group form-col-full">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao" class="form-control"><?= htmlspecialchars($dados['descricao'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Preço (R$) *</label>
                    <input type="number" name="preco" class="form-control"
                           step="0.01" min="0"
                           value="<?= htmlspecialchars($dados['preco']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Estoque</label>
                    <input type="number" name="estoque" class="form-control"
                           min="0" value="<?= htmlspecialchars($dados['estoque']) ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Categoria</label>
                    <select name="categoria_id" class="form-control">
                        <option value="">— Sem categoria —</option>
                        <?php foreach ($cats as $c): ?>
                        <option value="<?= $c['id'] ?>"
                            <?= (int)$dados['categoria_id'] === (int)$c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nome']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Imagem</label>
                    <?php $imgPath = '../public/imagens/' . ($dados['imagem'] ?? ''); ?>
                    <div class="d-flex align-center gap-12" style="margin-bottom:8px">
                        <div class="img-preview-box" id="preview-box">
                            <?php if ($dados['imagem'] && file_exists($imgPath)): ?>
                                <img src="<?= BASE_URL ?>/admin/public/imagens/<?= htmlspecialchars($dados['imagem']) ?>" alt="">
                            <?php else: ?>
                                <span>🖼️</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <input type="file" name="imagem" class="form-control" accept="image/*"
                                   onchange="previewImagem(this, 'preview-box')" style="width:auto">
                            <?php if ($dados['imagem']): ?>
                                <div class="form-hint mt-4">Atual: <strong><?= htmlspecialchars($dados['imagem']) ?></strong></div>
                            <?php endif; ?>
                            <div class="form-hint mt-4">Deixe em branco para manter a imagem atual.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top:8px">
                <button type="submit" class="btn btn-primary btn-lg">💾 Salvar Alterações</button>
                <a href="gerenciar_games.php" class="btn btn-secondary btn-lg">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
