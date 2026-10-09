<?php

namespace App\Models;

class Event extends BaseModel {
    protected string $table = 'events';

    public function findUpcoming(): array {
        $stmt = $this->db->prepare("
            SELECT * FROM events WHERE is_upcoming = 1 ORDER BY display_order
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findYears(): array {
        $stmt = $this->db->query("
            SELECT DISTINCT year FROM events WHERE is_upcoming = 0 ORDER BY year DESC
        ");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function findByYear(int $year): array {
        $stmt = $this->db->prepare("
            SELECT * FROM events WHERE year = ? AND is_upcoming = 0 ORDER BY display_order
        ");
        $stmt->execute([$year]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO events (title, year, date_text, location, image, is_upcoming, display_order)
            VALUES (:title, :year, :date_text, :location, :image, :is_upcoming, :display_order)
        ");
        $stmt->execute([
            ':title'         => $data['title'],
            ':year'          => $data['year'],
            ':date_text'     => $data['date_text'] ?? null,
            ':location'      => $data['location'] ?? null,
            ':image'         => $data['image'] ?? null,
            ':is_upcoming'   => $data['is_upcoming'] ?? 0,
            ':display_order' => $data['display_order'] ?? 0,
        ]);
        return $this->lastInsertId();
    }
}
