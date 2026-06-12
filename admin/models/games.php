<?php
require_once __DIR__ . '/../config/conexao.php';

class Game {
    public ?int    $id           = null;
    public string  $titulo       = '';
    public string  $descricao    = '';
    public float   $preco        = 0.0;
    public int     $estoque      = 0;
    public ?int    $categoria_id = null;
    public string  $imagem       = '';
    private PDO    $pdo;

    public function __construct(array $atrib = []) {
        global $pdo;
        $this->pdo = $pdo;

        if (!empty($atrib)) {
            $this->id           = isset($atrib['id'])           ? (int)$atrib['id']    : null;
            $this->titulo       = $atrib['titulo']              ?? '';
            $this->descricao    = $atrib['descricao']           ?? '';
            $this->preco        = (float)($atrib['preco']       ?? 0);
            $this->estoque      = (int)($atrib['estoque']       ?? 0);
            $this->categoria_id = isset($atrib['categoria_id']) ? (int)$atrib['categoria_id'] : null;
            $this->imagem       = $atrib['imagem']              ?? '';
        }
    }

    public function listarGames(): array {
        return $this->pdo->query(
            "SELECT g.*, c.nome AS categoria_nome
             FROM games g
             LEFT JOIN categorias c ON c.id = g.categoria_id
             ORDER BY g.titulo ASC"
        )->fetchAll();
    }

    public function buscarPorNome(string $nome): array {
        $sth = $this->pdo->prepare(
            "SELECT g.*, c.nome AS categoria_nome
             FROM games g
             LEFT JOIN categorias c ON c.id = g.categoria_id
             WHERE g.titulo LIKE :nome
             ORDER BY g.titulo ASC"
        );
        $sth->bindValue(':nome', '%' . $nome . '%', PDO::PARAM_STR);
        $sth->execute();
        return $sth->fetchAll();
    }

    public function buscarPorCategoria(int $categoriaId): array {
        $sth = $this->pdo->prepare(
            "SELECT g.*, c.nome AS categoria_nome
             FROM games g
             LEFT JOIN categorias c ON c.id = g.categoria_id
             WHERE g.categoria_id = :cat
             ORDER BY g.titulo ASC"
        );
        $sth->bindValue(':cat', $categoriaId, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchAll();
    }

    public function buscarGamePorId(int $id): array|false {
        $sth = $this->pdo->prepare(
            "SELECT g.*, c.nome AS categoria_nome
             FROM games g
             LEFT JOIN categorias c ON c.id = g.categoria_id
             WHERE g.id = :id"
        );
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetch();
    }

    public function cadastrarGame(): bool {
        $sth = $this->pdo->prepare(
            "INSERT INTO games (titulo, descricao, preco, estoque, categoria_id, imagem)
             VALUES (:titulo, :descricao, :preco, :estoque, :categoria_id, :imagem)"
        );
        $sth->bindValue(':titulo',       $this->titulo,       PDO::PARAM_STR);
        $sth->bindValue(':descricao',    $this->descricao,    PDO::PARAM_STR);
        $sth->bindValue(':preco',        $this->preco,        PDO::PARAM_STR);
        $sth->bindValue(':estoque',      $this->estoque,      PDO::PARAM_INT);
        $sth->bindValue(':categoria_id', $this->categoria_id, $this->categoria_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $sth->bindValue(':imagem',       $this->imagem,       PDO::PARAM_STR);
        return $sth->execute();
    }

    public function editarGame(): bool {
        $sth = $this->pdo->prepare(
            "UPDATE games SET
                titulo       = :titulo,
                descricao    = :descricao,
                preco        = :preco,
                estoque      = :estoque,
                categoria_id = :categoria_id,
                imagem       = :imagem
             WHERE id = :id"
        );
        $sth->bindValue(':id',           $this->id,           PDO::PARAM_INT);
        $sth->bindValue(':titulo',       $this->titulo,       PDO::PARAM_STR);
        $sth->bindValue(':descricao',    $this->descricao,    PDO::PARAM_STR);
        $sth->bindValue(':preco',        $this->preco,        PDO::PARAM_STR);
        $sth->bindValue(':estoque',      $this->estoque,      PDO::PARAM_INT);
        $sth->bindValue(':categoria_id', $this->categoria_id, $this->categoria_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $sth->bindValue(':imagem',       $this->imagem,       PDO::PARAM_STR);
        return $sth->execute();
    }

    public function deletarGame(): bool {
        $sth = $this->pdo->prepare("DELETE FROM games WHERE id = :id");
        $sth->bindValue(':id', $this->id, PDO::PARAM_INT);
        return $sth->execute();
    }

    public function listarCategorias(): array {
        return $this->pdo->query(
            "SELECT * FROM categorias ORDER BY nome ASC"
        )->fetchAll();
    }

    public function contarGames(): int {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM games")->fetchColumn();
    }

    public function valorTotalEstoque(): float {
        return (float) $this->pdo->query("SELECT COALESCE(SUM(preco * estoque),0) FROM games")->fetchColumn();
    }
}
