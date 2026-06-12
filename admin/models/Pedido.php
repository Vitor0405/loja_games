<?php
require_once __DIR__ . '/../config/conexao.php';

class Pedido {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = getConexao();
    }

    public function listar(int $limite = 0): array {
        $sql = "SELECT p.*, c.nome AS cliente_nome
                FROM pedidos p
                JOIN clientes c ON c.id = p.cliente_id
                ORDER BY p.created_at DESC";
        if ($limite > 0) $sql .= " LIMIT $limite";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function buscarPorId(int $id): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, c.nome AS cliente_nome, c.email AS cliente_email
             FROM pedidos p
             JOIN clientes c ON c.id = p.cliente_id
             WHERE p.id = :id"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $pedido = $stmt->fetch();
        if ($pedido) {
            $pedido['itens'] = $this->buscarItens($id);
        }
        return $pedido;
    }

    public function buscarItens(int $pedidoId): array {
        $stmt = $this->pdo->prepare(
            "SELECT ip.*, g.titulo
             FROM itens_pedido ip
             JOIN games g ON g.id = ip.game_id
             WHERE ip.pedido_id = :id"
        );
        $stmt->bindValue(':id', $pedidoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function buscarPorCliente(int $clienteId): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM pedidos WHERE cliente_id = :id ORDER BY created_at DESC"
        );
        $stmt->bindValue(':id', $clienteId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function criar(int $clienteId, array $itens): int|false {
        $this->pdo->beginTransaction();
        try {
            $total = array_sum(array_map(fn($i) => $i['preco'] * $i['quantidade'], $itens));

            $stmt = $this->pdo->prepare(
                "INSERT INTO pedidos (cliente_id, status, total) VALUES (:cliente_id, 'pendente', :total)"
            );
            $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
            $stmt->bindValue(':total',      $total);
            $stmt->execute();
            $pedidoId = (int) $this->pdo->lastInsertId();

            $stmtItem = $this->pdo->prepare(
                "INSERT INTO itens_pedido (pedido_id, game_id, quantidade, preco_unitario)
                 VALUES (:pedido_id, :game_id, :quantidade, :preco)"
            );
            foreach ($itens as $item) {
                $stmtItem->bindValue(':pedido_id',  $pedidoId, PDO::PARAM_INT);
                $stmtItem->bindValue(':game_id',    (int)$item['game_id'], PDO::PARAM_INT);
                $stmtItem->bindValue(':quantidade', (int)$item['quantidade'], PDO::PARAM_INT);
                $stmtItem->bindValue(':preco',      $item['preco']);
                $stmtItem->execute();
            }

            $this->pdo->commit();
            return $pedidoId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function atualizarStatus(int $id, string $status): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE pedidos SET status = :status WHERE id = :id"
        );
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id',     $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deletar(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM pedidos WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function contar(): int {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
    }

    public function contarPorStatus(): array {
        $rows = $this->pdo->query(
            "SELECT status, COUNT(*) AS total FROM pedidos GROUP BY status"
        )->fetchAll();
        $result = ['pendente' => 0, 'aprovado' => 0, 'cancelado' => 0];
        foreach ($rows as $r) $result[$r['status']] = $r['total'];
        return $result;
    }

    public function somarTotal(): float {
        return (float) $this->pdo->query(
            "SELECT COALESCE(SUM(total),0) FROM pedidos WHERE status = 'aprovado'"
        )->fetchColumn();
    }
}
