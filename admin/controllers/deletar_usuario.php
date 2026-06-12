<?php
session_start();
require_once '../config/conexao.php';
require_once '../config/auth.php';
apenasAdmin();
require_once '../models/usuarios.php';

$id = (int)($_GET['id'] ?? 0);

if (!$id || $id === (int)$_SESSION['usuario_id']) {
    header('Location: ' . BASE_URL . '/admin/views/ver_usuarios.php');
    exit;
}

$usuario = new Usuario(['id' => $id]);

if ($usuario->deletar()) {
    header('Location: ' . BASE_URL . '/admin/views/ver_usuarios.php?deletado=1');
} else {
    header('Location: ' . BASE_URL . '/admin/views/ver_usuarios.php?erro=1');
}
exit;
