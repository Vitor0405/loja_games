<?php
session_start();
require_once '../config/conexao.php';
require_once '../config/auth.php';
require_once '../models/games.php';

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header('Location: ' . BASE_URL . '/admin/views/gerenciar_games.php');
    exit;
}

$game = new Game(['id' => $id]);

if ($game->deletarGame()) {
    header('Location: ' . BASE_URL . '/admin/views/gerenciar_games.php?deletado=1');
} else {
    header('Location: ' . BASE_URL . '/admin/views/gerenciar_games.php?erro=1');
}
exit;
