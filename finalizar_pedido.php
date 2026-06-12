<?php
require_once __DIR__ . '/admin/config/conexao.php';
require_once __DIR__ . '/admin/models/Pedido.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['cliente_id'])) {
    $_SESSION['redirect_apos_login'] = BASE_URL . '/carrinho.php';
    header('Location: ' . BASE_URL . '/login_cliente.php');
    exit;
}

if (empty($_SESSION['carrinho'])) {
    header('Location: ' . BASE_URL . '/carrinho.php');
    exit;
}

$itens = array_values(array_map(fn($i) => [
    'game_id'    => $i['game_id'],
    'quantidade' => $i['quantidade'],
    'preco'      => $i['preco'],
], $_SESSION['carrinho']));

$ped      = new Pedido();
$pedidoId = $ped->criar((int)$_SESSION['cliente_id'], $itens);

if ($pedidoId) {
    $_SESSION['carrinho']        = [];
    $_SESSION['ultimo_pedido_id'] = $pedidoId;
    header('Location: ' . BASE_URL . '/minha_conta.php?pedido=ok');
} else {
    header('Location: ' . BASE_URL . '/carrinho.php?erro=1');
}
exit;
