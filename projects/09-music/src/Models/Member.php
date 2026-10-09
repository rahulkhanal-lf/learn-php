<?php

namespace App\Models;

class Member extends BaseModel {
    protected string $table = 'members';

    public function findAll(string $orderBy = 'display_order'): array {
        return $this->db
            ->query("SELECT * FROM members ORDER BY {$orderBy}")
            ->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO members (name, instrument, bio, photo, display_order)
            VALUES (:name, :instrument, :bio, :photo, :display_order)
        ");
        $stmt->execute([
            ':name'          => $data['name'],
            ':instrument'    => $data['instrument'],
            ':bio'           => $data['bio'] ?? null,
            ':photo'         => $data['photo'] ?? null,
            ':display_order' => $data['display_order'] ?? 0,
        ]);
        return $this->lastInsertId();
    }
}
