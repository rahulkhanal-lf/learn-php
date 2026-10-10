<?php

namespace App\Models;

class Project extends BaseModel {
    protected string $table = 'projects';

    public function findAll(string $orderBy = 'display_order ASC'): array {
        return parent::findAll($orderBy);
    }
}
