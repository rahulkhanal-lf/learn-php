<?php

namespace App\Controllers;

use App\Models\Song;
use App\Models\Artist;

class SongController {
    private Song $songModel;
    private Artist $artistModel;

    public function __construct() {
        $this->songModel   = new Song();
        $this->artistModel = new Artist();
    }

    public function index(): void {
        $search  = trim($_GET['q'] ?? '');
        $songs   = $search
            ? $this->songModel->search($search)
            : $this->songModel->findAll();
        $artists = $this->artistModel->allForSelect();

        require __DIR__ . '/../../public/views/songs/index.php';
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            return;
        }

        $errors = $this->validate($_POST);

        if (empty($errors)) {
            $this->songModel->create([
                'title'            => trim($_POST['title']),
                'artist_id'        => (int) $_POST['artist_id'],
                'album'            => trim($_POST['album'] ?? ''),
                'genre'            => trim($_POST['genre'] ?? ''),
                'duration_seconds' => (int) $_POST['duration_seconds'] ?: null,
                'release_year'     => (int) $_POST['release_year'] ?: null,
            ]);
            header('Location: index.php?msg=created');
            return;
        }

        $songs   = $this->songModel->findAll();
        $artists = $this->artistModel->allForSelect();
        require __DIR__ . '/../../public/views/songs/index.php';
    }

    public function delete(): void {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id) $this->songModel->delete($id);
        header('Location: index.php?msg=deleted');
    }

    private function validate(array $data): array {
        $errors = [];
        if (empty(trim($data['title'] ?? '')))   $errors[] = 'Title is required.';
        if (empty($data['artist_id']))            $errors[] = 'Artist is required.';
        return $errors;
    }
}
