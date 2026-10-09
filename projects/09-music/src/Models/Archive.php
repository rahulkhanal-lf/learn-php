<?php

namespace App\Models;

use App\Database\Connection;

class Archive extends BaseModel {
    protected string $table = 'archives';

    public function findAll(): array {
        $pdo = Connection::getInstance();
        $stmt = $pdo->query("SELECT * FROM archives ORDER BY year ASC");
        return $stmt->fetchAll();
    }

    public function findEntriesByArchiveId(int $archiveId): array {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM archive_entries WHERE archive_id = ? ORDER BY id ASC");
        $stmt->execute([$archiveId]);
        return $stmt->fetchAll();
    }

    // Returns all archives with their entries nested
    public function findAllWithEntries(): array {
        $pdo = Connection::getInstance();
        $archives = $pdo->query("SELECT * FROM archives ORDER BY year ASC")->fetchAll();
        foreach ($archives as &$arch) {
            $stmt = $pdo->prepare("SELECT * FROM archive_entries WHERE archive_id = ? ORDER BY id ASC");
            $stmt->execute([$arch['id']]);
            $arch['entries'] = $stmt->fetchAll();
        }
        return $archives;
    }
}
