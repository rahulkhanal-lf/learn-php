# 09 — Music App

A small music library CRUD app built with PHP 8.2, PDO, and `vlucas/phpdotenv`.  
The goal is to demonstrate an **industry-standard PHP project structure** without a full framework.

---

## Architecture

```
09-music/
│
├── .env                   # Real credentials — never committed
├── .env.example           # Template for teammates to copy
├── .gitignore
├── composer.json          # Dependencies + PSR-4 autoloading
├── composer.lock
│
├── config/
│   └── database.php       # Reads $_ENV vars, returns config array
│
├── src/                   # PSR-4 namespace: App\
│   ├── Database/
│   │   └── Connection.php # Singleton PDO — created once, reused everywhere
│   ├── Models/
│   │   ├── BaseModel.php  # Shared findAll / findById / delete
│   │   ├── Song.php       # Songs CRUD + search
│   │   └── Artist.php     # Artists CRUD
│   └── Controllers/
│       └── SongController.php  # Handles HTTP actions, calls Models, loads Views
│
├── public/                # Web root — only this folder is exposed to the browser
│   ├── index.php          # Entry point: loads .env, routes ?action= to Controller
│   ├── setup.php          # One-time DB + table creation + seed data
│   └── views/
│       └── songs/
│           └── index.php  # HTML view (rendered by SongController)
│
├── storage/
│   └── logs/              # Log files (gitignored)
│
├── tests/                 # Unit/integration tests (empty — ready to add)
└── vendor/                # Composer packages (gitignored)
```

### Data Flow

```
Browser Request
      │
      ▼
public/index.php          ← loads .env via phpdotenv
      │                   ← requires vendor/autoload.php (PSR-4)
      ▼
SongController            ← routes ?action= (index / store / delete)
      │
      ├── Song / Artist   ← Models call Connection::getInstance()
      │         │
      │         ▼
      │   Connection.php  ← reads config/database.php (which reads $_ENV)
      │         │
      │         ▼
      │      PDO (MySQL)
      │
      ▼
public/views/songs/index.php   ← plain PHP template
```

### How `.env` flows into the app

```
.env  →  phpdotenv::load()  →  $_ENV[]  →  config/database.php  →  Connection.php  →  PDO
```

This mirrors Next.js's `.env.local → process.env` pattern exactly.

---

## How to Run

### 1. Prerequisites

- LAMPP running (`sudo /opt/lampp/lampp start`)
- PHP 8.1+ at `/opt/lampp/bin/php`

### 2. Install dependencies

```bash
cd /opt/lampp/htdocs/learn-php/projects/09-music

# If composer.phar doesn't exist yet:
/opt/lampp/bin/php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
/opt/lampp/bin/php composer-setup.php --quiet && rm composer-setup.php

# Install packages
/opt/lampp/bin/php composer.phar install
```

### 3. Configure environment

```bash
cp .env.example .env
```

Edit `.env` if your MySQL credentials differ (default is root with no password):

```env
DB_HOST=127.0.0.1
DB_DATABASE=music_app
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Create database, tables, and seed data

Open in browser:

```
http://localhost/learn-php/projects/09-music/public/setup.php
```

### 5. Open the app

```
http://localhost/learn-php/projects/09-music/public/index.php
```

---

## Key Concepts Demonstrated

| Concept | File |
|---------|------|
| `.env` loading with phpdotenv | `public/index.php` |
| Singleton DB connection | `src/Database/Connection.php` |
| PSR-4 autoloading | `composer.json` |
| Base model (reusable CRUD) | `src/Models/BaseModel.php` |
| Model inheritance | `src/Models/Song.php` |
| Controller separating HTTP from DB | `src/Controllers/SongController.php` |
| View as plain PHP template | `public/views/songs/index.php` |
| Prepared statements (SQL injection safe) | `src/Models/Song.php` |
| XSS prevention | `htmlspecialchars()` in all views |
| Config separated from code | `config/database.php` |
