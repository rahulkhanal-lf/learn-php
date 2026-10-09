<?php

namespace App\Models;

class Contact extends BaseModel {
    protected string $table = 'contacts';

    public function save(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO contacts (name, email, message)
            VALUES (:name, :email, :message)
        ");
        $stmt->execute([
            ':name'    => $data['name'],
            ':email'   => $data['email'],
            ':message' => $data['message'],
        ]);
        return $this->lastInsertId();
    }
}
