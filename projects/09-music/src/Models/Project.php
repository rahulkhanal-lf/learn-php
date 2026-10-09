<?php

namespace App\Models;

class Project extends BaseModel {
    protected string $table = 'projects';

    public function findAll(): array {
        return parent::findAll('year ASC');
    }
}
