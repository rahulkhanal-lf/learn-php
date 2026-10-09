<?php

namespace App\Models;

class Initiative extends BaseModel {
    protected string $table = 'initiatives';

    public function findAll(string $orderBy = 'year ASC'): array {
        return $this->db
            ->query("SELECT * FROM initiatives ORDER BY {$orderBy}")
            ->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO initiatives (year, title, date_text, locations)
            VALUES (:year, :title, :date_text, :locations)
        ");
        $stmt->execute([
            ':year'      => $data['year'],
            ':title'     => $data['title'],
            ':date_text' => $data['date_text'] ?? null,
            ':locations' => $data['locations'] ?? null,
        ]);
        return $this->lastInsertId();
    }
}
