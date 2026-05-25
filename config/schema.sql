-- F1 Fantasy - Esquema de base de datos
-- Ejecutar: mysql -u root -p < config/schema.sql

CREATE DATABASE IF NOT EXISTS f1fantasy
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE f1fantasy;

-- 1. Usuarios (sin dependencias)
CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(60) NOT NULL UNIQUE,
    email       VARCHAR(120) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('user','admin') NOT NULL DEFAULT 'user',
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Ligas (depende de users)
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

-- 3. Miembros de liga (depende de leagues, users)
CREATE TABLE IF NOT EXISTS league_members (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    league_id  INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED NOT NULL,
    joined_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (league_id) REFERENCES leagues(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
    UNIQUE KEY uq_league_user (league_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Equipos Fantasy (depende de users, leagues)
CREATE TABLE IF NOT EXISTS fantasy_teams (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED NOT NULL,
    league_id    INT UNSIGNED NOT NULL,
    team_name    VARCHAR(100) NOT NULL,
    total_points DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    budget_used  DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
    FOREIGN KEY (league_id) REFERENCES leagues(id)  ON DELETE CASCADE,
    UNIQUE KEY uq_user_league (user_id, league_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Selecciones del equipo (pilotos y constructores)
CREATE TABLE IF NOT EXISTS fantasy_selections (
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    team_id  INT UNSIGNED NOT NULL,
    type     ENUM('driver','constructor') NOT NULL,
    item_id  VARCHAR(60) NOT NULL,
    price    DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (team_id) REFERENCES fantasy_teams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Puntos por piloto/constructor dentro de cada equipo
CREATE TABLE IF NOT EXISTS fantasy_driver_points (
    id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    team_id INT UNSIGNED NOT NULL,
    item_id VARCHAR(60) NOT NULL,
    type    ENUM('driver','constructor') NOT NULL,
    points  DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (team_id) REFERENCES fantasy_teams(id) ON DELETE CASCADE,
    UNIQUE KEY uq_team_item (team_id, item_id, type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Control de rondas ya puntuadas (evita doble puntuacion)
CREATE TABLE IF NOT EXISTS scored_rounds (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    season    VARCHAR(10) NOT NULL,
    round     INT UNSIGNED NOT NULL,
    type      VARCHAR(20) NOT NULL,
    scored_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_round (season, round, type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Indices adicionales
CREATE INDEX idx_fantasy_teams_user     ON fantasy_teams(user_id);
CREATE INDEX idx_fantasy_teams_league   ON fantasy_teams(league_id);
CREATE INDEX idx_fantasy_selections_team ON fantasy_selections(team_id);
CREATE INDEX idx_leagues_creator        ON leagues(creator_id);
CREATE INDEX idx_league_members_user    ON league_members(user_id);
