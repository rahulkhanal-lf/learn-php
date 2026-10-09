<?php

namespace App\Models;

class Album extends BaseModel {
    protected string $table = 'albums';

    public function findAll(string $orderBy = 'year DESC'): array {
        return $this->db
            ->query("SELECT * FROM albums ORDER BY {$orderBy}")
            ->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO albums (title, year, cover_image, description)
            VALUES (:title, :year, :cover_image, :description)
        ");
        $stmt->execute([
            ':title'       => $data['title'],
            ':year'        => $data['year'],
            ':cover_image' => $data['cover_image'] ?? null,
            ':description' => $data['description'] ?? null,
        ]);
        return $this->lastInsertId();
    }
}
