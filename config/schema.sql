-- F1 Fantasy - Esquema de base de datos
-- Ejecutar: mysql -u root -p < config/schema.sql

CREATE DATABASE IF NOT EXISTS f1fantasy
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE f1fantasy;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(60) NOT NULL UNIQUE,
    email       VARCHAR(120) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Equipos Fantasy
CREATE TABLE IF NOT EXISTS fantasy_teams (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED NOT NULL,
    team_name    VARCHAR(100) NOT NULL,
    total_points DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    budget_used  DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uq_user_team (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Selecciones del equipo (pilotos y constructores)
CREATE TABLE IF NOT EXISTS fantasy_selections (
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    team_id  INT UNSIGNED NOT NULL,
    type     ENUM('driver','constructor') NOT NULL,
    item_id  VARCHAR(60) NOT NULL,   -- ID de la API Ergast
    price    DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (team_id) REFERENCES fantasy_teams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ligas
CREATE TABLE IF NOT EXISTS leagues (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(100) NOT NULL,
    description  VARCHAR(255) NOT NULL DEFAULT '',
    creator_id   INT UNSIGNED NOT NULL,
    is_public    TINYINT(1) NOT NULL DEFAULT 1,
    invite_code  VARCHAR(12) NOT NULL UNIQUE,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Miembros de liga
CREATE TABLE IF NOT EXISTS league_members (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    league_id  INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED NOT NULL,
    joined_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (league_id) REFERENCES leagues(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
    UNIQUE KEY uq_league_user (league_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Índices adicionales
CREATE INDEX idx_fantasy_teams_user ON fantasy_teams(user_id);
CREATE INDEX idx_fantasy_selections_team ON fantasy_selections(team_id);
CREATE INDEX idx_leagues_creator ON leagues(creator_id);
CREATE INDEX idx_league_members_user ON league_members(user_id);
