<?php
require_once __DIR__ . '/../config/conexao.php';

class Cliente {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = getConexao();
    }

    public function listar(): array {
        return $this->pdo->query(
            "SELECT * FROM clientes ORDER BY nome ASC"
        )->fetchAll();
    }

    public function buscarPorId(int $id): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM clientes WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function buscarPorEmail(string $email): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM clientes WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function autenticar(string $email, string $senha): array|false {
        $cliente = $this->buscarPorEmail($email);
        if (!$cliente || !$cliente['ativo']) return false;

        if (password_verify($senha, $cliente['senha'])) return $cliente;

        $hash = hash('sha256', $senha);
        if (hash_equals($cliente['senha'], $hash)) return $cliente;

        return false;
    }

    public function cadastrar(array $dados): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO clientes (nome, email, telefone, senha)
             VALUES (:nome, :email, :telefone, :senha)"
        );
        $stmt->bindValue(':nome',     trim($dados['nome']));
        $stmt->bindValue(':email',    trim($dados['email']));
        $stmt->bindValue(':telefone', trim($dados['telefone'] ?? ''));
        $stmt->bindValue(':senha',    password_hash($dados['senha'], PASSWORD_BCRYPT));
        return $stmt->execute();
    }

    public function editar(int $id, array $dados, bool $alterarSenha = false): bool {
        if ($alterarSenha && !empty($dados['senha'])) {
            $stmt = $this->pdo->prepare(
                "UPDATE clientes SET nome = :nome, email = :email, telefone = :telefone,
                 ativo = :ativo, senha = :senha WHERE id = :id"
            );
            $stmt->bindValue(':senha', password_hash($dados['senha'], PASSWORD_BCRYPT));
        } else {
            $stmt = $this->pdo->prepare(
                "UPDATE clientes SET nome = :nome, email = :email, telefone = :telefone,
                 ativo = :ativo WHERE id = :id"
            );
        }
        $stmt->bindValue(':nome',     trim($dados['nome']));
        $stmt->bindValue(':email',    trim($dados['email']));
        $stmt->bindValue(':telefone', trim($dados['telefone'] ?? ''));
        $stmt->bindValue(':ativo',    isset($dados['ativo']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':id',       $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deletar(int $id): bool {
        if ($this->temPedidos($id)) return false;
        $stmt = $this->pdo->prepare("DELETE FROM clientes WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function temPedidos(int $id): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM pedidos WHERE cliente_id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function emailExiste(string $email, int $ignorarId = 0): bool {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM clientes WHERE email = :email AND id != :id"
        );
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':id',    $ignorarId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function contar(): int {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
    }
}
