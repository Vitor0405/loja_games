<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/auth.php';
apenasAdmin();
require_once __DIR__ . '/../models/Cliente.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/views/listar_clientes.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: ' . BASE_URL . '/admin/views/listar_clientes.php?erro=1');
    exit;
}

$cli = new Cliente();

if ($cli->temPedidos($id)) {
    header('Location: ' . BASE_URL . '/admin/views/listar_clientes.php?erro=tem_pedidos');
    exit;
}

if ($cli->deletar($id)) {
    header('Location: ' . BASE_URL . '/admin/views/listar_clientes.php?deletado=1');
} else {
    header('Location: ' . BASE_URL . '/admin/views/listar_clientes.php?erro=1');
}
exit;
