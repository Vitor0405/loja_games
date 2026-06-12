<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Categoria.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/views/listar_categorias.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: ' . BASE_URL . '/admin/views/listar_categorias.php?erro=1');
    exit;
}

$cat = new Categoria();

if ($cat->temGames($id)) {
    header('Location: ' . BASE_URL . '/admin/views/listar_categorias.php?erro=tem_games');
    exit;
}

if ($cat->deletar($id)) {
    header('Location: ' . BASE_URL . '/admin/views/listar_categorias.php?deletado=1');
} else {
    header('Location: ' . BASE_URL . '/admin/views/listar_categorias.php?erro=1');
}
exit;
