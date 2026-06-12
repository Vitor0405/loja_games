<?php
if (session_status() === PHP_SESSION_NONE) session_start();
unset($_SESSION['cliente_id'], $_SESSION['cliente_nome'], $_SESSION['carrinho']);
require_once __DIR__ . '/admin/config/conexao.php';
header('Location: ' . BASE_URL . '/index.php');
exit;
