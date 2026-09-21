<?php

namespace App\Models;

class Song extends BaseModel {
    protected string $table = 'songs';

    public function findAll(string $orderBy = 'title'): array {
        $stmt = $this->db->query("
            SELECT s.*, a.name AS artist_name
            FROM songs s
            LEFT JOIN artists a ON s.artist_id = a.id
            ORDER BY s.{$orderBy}
        ");
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO songs (title, artist_id, album, genre, duration_seconds, release_year)
            VALUES (:title, :artist_id, :album, :genre, :duration_seconds, :release_year)
        ");
        $stmt->execute([
            ':title'            => $data['title'],
            ':artist_id'        => $data['artist_id'],
            ':album'            => $data['album'] ?? null,
            ':genre'            => $data['genre'] ?? null,
            ':duration_seconds' => $data['duration_seconds'] ?? null,
            ':release_year'     => $data['release_year'] ?? null,
        ]);
        return $this->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE songs
            SET title=:title, artist_id=:artist_id, album=:album,
                genre=:genre, duration_seconds=:duration_seconds, release_year=:release_year
            WHERE id=:id
        ");
        $stmt->execute([
            ':title'            => $data['title'],
            ':artist_id'        => $data['artist_id'],
            ':album'            => $data['album'] ?? null,
            ':genre'            => $data['genre'] ?? null,
            ':duration_seconds' => $data['duration_seconds'] ?? null,
            ':release_year'     => $data['release_year'] ?? null,
            ':id'               => $id,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function search(string $query): array {
        $like = '%' . $query . '%';
        $stmt = $this->db->prepare("
            SELECT s.*, a.name AS artist_name
            FROM songs s
            LEFT JOIN artists a ON s.artist_id = a.id
            WHERE s.title LIKE ? OR s.album LIKE ? OR s.genre LIKE ? OR a.name LIKE ?
            ORDER BY s.title
        ");
        $stmt->execute([$like, $like, $like, $like]);
        return $stmt->fetchAll();
    }
}
