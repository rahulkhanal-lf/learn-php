<?php

namespace App\Models;

use App\Database\Connection;
use PDO;

abstract class BaseModel {
    protected PDO $db;
    protected string $table;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function findAll(string $orderBy = 'id'): array {
        return $this->db
            ->query("SELECT * FROM {$this->table} ORDER BY {$orderBy}")
            ->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    protected function lastInsertId(): int {
        return (int) $this->db->lastInsertId();
    }
}
