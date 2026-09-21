<?php

namespace App\Models;

class Artist extends BaseModel {
    protected string $table = 'artists';

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO artists (name, country, genre)
            VALUES (:name, :country, :genre)
        ");
        $stmt->execute([
            ':name'    => $data['name'],
            ':country' => $data['country'] ?? null,
            ':genre'   => $data['genre']   ?? null,
        ]);
        return $this->lastInsertId();
    }

    public function allForSelect(): array {
        return $this->db
            ->query("SELECT id, name FROM artists ORDER BY name")
            ->fetchAll();
    }
}
