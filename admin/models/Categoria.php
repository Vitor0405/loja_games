<?php
require_once __DIR__ . '/../config/conexao.php';

class Categoria {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = getConexao();
    }

    public function listar(): array {
        $sql = "SELECT c.*, COUNT(g.id) AS total_games
                FROM categorias c
                LEFT JOIN games g ON g.categoria_id = c.id
                GROUP BY c.id
                ORDER BY c.nome ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function buscarPorId(int $id): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM categorias WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function cadastrar(array $dados): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categorias (nome, descricao) VALUES (:nome, :descricao)"
        );
        $stmt->bindValue(':nome',     trim($dados['nome']));
        $stmt->bindValue(':descricao', trim($dados['descricao'] ?? ''));
        return $stmt->execute();
    }

    public function editar(int $id, array $dados): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE categorias SET nome = :nome, descricao = :descricao WHERE id = :id"
        );
        $stmt->bindValue(':nome',      trim($dados['nome']));
        $stmt->bindValue(':descricao', trim($dados['descricao'] ?? ''));
        $stmt->bindValue(':id',        $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deletar(int $id): bool {
        if ($this->temGames($id)) return false;
        $stmt = $this->pdo->prepare("DELETE FROM categorias WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function temGames(int $id): bool {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM games WHERE categoria_id = :id"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function contar(): int {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM categorias")->fetchColumn();
    }
}
