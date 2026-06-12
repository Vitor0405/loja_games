<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'loja_games');
define('BASE_URL', '/loja_games');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;padding:30px;background:#1a1a2e;color:#ef4444;text-align:center;">
        <h2>Erro de Conexão</h2>
        <p>' . $e->getMessage() . '</p>
        <p>Verifique se o XAMPP está rodando e o banco <strong>loja_games</strong> foi criado.</p>
    </div>');
}

function getConexao(): PDO {
    global $pdo;
    return $pdo;
}
