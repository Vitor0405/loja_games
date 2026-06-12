<?php
session_start();
session_destroy();
require_once 'admin/config/conexao.php';
header('Location: ' . BASE_URL . '/login.php');
exit;
