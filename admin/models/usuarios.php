<?php
require_once __DIR__ . '/../config/conexao.php';

class Usuario {
    public ?int   $id    = null;
    public string $nome  = '';
    public string $email = '';
    public string $senha = '';
    public string $tipo  = 'user';
    private PDO   $pdo;

    public function __construct(array $atrib = []) {
        global $pdo;
        $this->pdo = $pdo;

        if (!empty($atrib)) {
            $this->id    = $atrib['id']    ?? null;
            $this->nome  = $atrib['nome']  ?? '';
            $this->email = $atrib['email'] ?? '';
            $this->senha = $atrib['senha'] ?? '';
            $this->tipo  = $atrib['tipo']  ?? 'user';
        }
    }

    public function listarUsuarios(): array {
        $sth = $this->pdo->query("SELECT id, nome, email, tipo, created_at FROM usuarios ORDER BY nome ASC");
        return $sth->fetchAll();
    }

    public function buscarPorId(int $id): array|false {
        $sth = $this->pdo->prepare("SELECT id, nome, email, tipo FROM usuarios WHERE id = :id");
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetch();
    }

    public function buscarPorEmail(string $email): array|false {
        $sth = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $sth->bindValue(':email', $email, PDO::PARAM_STR);
        $sth->execute();
        return $sth->fetch();
    }

    public function autenticar(string $email, string $senha): array|false {
        $usuario = $this->buscarPorEmail($email);
        if ($usuario && $usuario['senha'] === hash('sha256', $senha)) {
            return $usuario;
        }
        return false;
    }

    public function cadastrar(): bool {
        $sth = $this->pdo->prepare(
            "INSERT INTO usuarios (nome, email, senha, tipo, created_at, updated_at)
             VALUES (:nome, :email, :senha, :tipo, NOW(), NOW())"
        );
        $sth->bindValue(':nome',  $this->nome,             PDO::PARAM_STR);
        $sth->bindValue(':email', $this->email,            PDO::PARAM_STR);
        $sth->bindValue(':senha', password_hash($this->senha, PASSWORD_BCRYPT), PDO::PARAM_STR);
        $sth->bindValue(':tipo',  $this->tipo,             PDO::PARAM_STR);
        return $sth->execute();
    }

    public function editar(bool $alterarSenha = false): bool {
        if ($alterarSenha) {
            $sth = $this->pdo->prepare(
                "UPDATE usuarios SET nome = :nome, email = :email, senha = :senha, tipo = :tipo, updated_at = NOW() WHERE id = :id"
            );
            $sth->bindValue(':senha', password_hash($this->senha, PASSWORD_BCRYPT), PDO::PARAM_STR);
        } else {
            $sth = $this->pdo->prepare(
                "UPDATE usuarios SET nome = :nome, email = :email, tipo = :tipo, updated_at = NOW() WHERE id = :id"
            );
        }
        $sth->bindValue(':id',    $this->id,    PDO::PARAM_INT);
        $sth->bindValue(':nome',  $this->nome,  PDO::PARAM_STR);
        $sth->bindValue(':email', $this->email, PDO::PARAM_STR);
        $sth->bindValue(':tipo',  $this->tipo,  PDO::PARAM_STR);
        return $sth->execute();
    }

    public function deletar(): bool {
        $sth = $this->pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $sth->bindValue(':id', $this->id, PDO::PARAM_INT);
        return $sth->execute();
    }

    public function contarUsuarios(): int {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    }

    public function emailExiste(string $email, ?int $ignorarId = null): bool {
        if ($ignorarId) {
            $sth = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = :email AND id != :id");
            $sth->bindValue(':id', $ignorarId, PDO::PARAM_INT);
        } else {
            $sth = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        }
        $sth->bindValue(':email', $email, PDO::PARAM_STR);
        $sth->execute();
        return (bool) $sth->fetch();
    }
}
