<?php

namespace App\Models;

class Instrument extends BaseModel {
    protected string $table = 'instruments';

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO instruments (name, description, photo, display_order)
            VALUES (:name, :description, :photo, :display_order)
        ");
        $stmt->execute([
            ':name'          => $data['name'],
            ':description'   => $data['description'],
            ':photo'         => $data['photo'] ?? null,
            ':display_order' => $data['display_order'] ?? 0,
        ]);
        return $this->lastInsertId();
    }
}
