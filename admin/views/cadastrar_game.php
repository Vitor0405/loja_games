<?php
session_start();
require_once '../config/conexao.php';
require_once '../config/auth.php';
require_once '../models/games.php';

$pageTitle  = 'Cadastrar Novo Jogo';
$pageActive = 'novo-game';

$g_model   = new Game();
$cats      = $g_model->listarCategorias();
$mensagem  = '';
$erro      = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo       = trim($_POST['titulo']       ?? '');
    $descricao    = trim($_POST['descricao']    ?? '');
    $preco        = trim($_POST['preco']        ?? '');
    $estoque      = (int)($_POST['estoque']     ?? 0);
    $categoria_id = (int)($_POST['categoria_id'] ?? 0) ?: null;
    $imagemNome   = '';

    if (!$titulo || !$preco) {
        $erro = 'Título e preço são obrigatórios.';
    } else {
        if (!empty($_FILES['imagem']['name'])) {
            $ext     = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (!in_array($ext, $allowed)) {
                $erro = 'Tipo de imagem inválido. Use JPG, PNG, GIF ou WEBP.';
            } else {
                $imagemNome = uniqid('game_') . '.' . $ext;
                $destino    = '../public/imagens/' . $imagemNome;
                if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                    $erro = 'Falha ao fazer upload da imagem.';
                }
            }
        }

        if (!$erro) {
            $novo = new Game([
                'titulo'       => $titulo,
                'descricao'    => $descricao,
                'preco'        => $preco,
                'estoque'      => $estoque,
                'categoria_id' => $categoria_id,
                'imagem'       => $imagemNome,
            ]);
            if ($novo->cadastrarGame()) {
                $mensagem = 'Jogo cadastrado com sucesso!';
            } else {
                $erro = 'Erro ao cadastrar no banco de dados.';
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
        <div class="card-title">🕹️ Dados do Jogo</div>
        <a href="gerenciar_games.php" class="btn btn-secondary btn-sm">← Voltar</a>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group form-col-full">
                    <label class="form-label">Título *</label>
                    <input type="text" name="titulo" class="form-control"
                           placeholder="Ex: The Legend of Zelda: Breath of the Wild"
                           value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>" required>
                </div>

                <div class="form-group form-col-full">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao" class="form-control"
                              placeholder="Descreva o jogo..."><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Preço (R$) *</label>
                    <input type="number" name="preco" class="form-control"
                           placeholder="Ex: 299.99" step="0.01" min="0"
                           value="<?= htmlspecialchars($_POST['preco'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Estoque</label>
                    <input type="number" name="estoque" class="form-control"
                           placeholder="Quantidade disponível" min="0"
                           value="<?= htmlspecialchars($_POST['estoque'] ?? '0') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Categoria</label>
                    <select name="categoria_id" class="form-control">
                        <option value="">— Sem categoria —</option>
                        <?php foreach ($cats as $c): ?>
                        <option value="<?= $c['id'] ?>"
                            <?= (int)($_POST['categoria_id'] ?? 0) === (int)$c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nome']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Imagem do Jogo</label>
                    <div class="d-flex align-center gap-12" style="margin-bottom:8px">
                        <div class="img-preview-box" id="preview-box"><span>🖼️</span></div>
                        <div>
                            <input type="file" name="imagem" class="form-control" accept="image/*"
                                   onchange="previewImagem(this, 'preview-box')" style="width:auto">
                            <div class="form-hint mt-4">JPG, PNG, GIF ou WEBP. Max 5MB.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top:8px">
                <button type="submit" class="btn btn-primary btn-lg">✅ Cadastrar Jogo</button>
                <a href="gerenciar_games.php" class="btn btn-secondary btn-lg">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
