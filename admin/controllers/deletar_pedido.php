<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Pedido.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/views/listar_pedidos.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: ' . BASE_URL . '/admin/views/listar_pedidos.php?erro=1');
    exit;
}

$ped = new Pedido();
if ($ped->deletar($id)) {
    header('Location: ' . BASE_URL . '/admin/views/listar_pedidos.php?deletado=1');
} else {
    header('Location: ' . BASE_URL . '/admin/views/listar_pedidos.php?erro=1');
}
exit;
