-- F1 Fantasy Manager Database Schema
-- ===================================

-- Create Database
CREATE DATABASE IF NOT EXISTS f1_fantasy_manager;
USE f1_fantasy_manager;

-- Table: escuderias (Formula 1 Teams/Constructors)
CREATE TABLE IF NOT EXISTS escuderias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    presupuesto DECIMAL(10, 2) NOT NULL,
    pais VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: usuarios (Users)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'usuario') DEFAULT 'usuario',
    saldo_presupuesto DECIMAL(10, 2) DEFAULT 50000,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: pilotos (Formula 1 Drivers)
CREATE TABLE IF NOT EXISTS pilotos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    id_escuderia INT NOT NULL,
    numero_casco INT,
    precio DECIMAL(10, 2) NOT NULL,
    pais VARCHAR(50),
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_escuderia) REFERENCES escuderias(id) ON DELETE RESTRICT
);

-- Table: ligas (Leagues)
CREATE TABLE IF NOT EXISTS ligas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('publica', 'privada') DEFAULT 'privada',
    id_admin INT NOT NULL,
    temporada INT NOT NULL DEFAULT 2025,
    descripcion TEXT,
    max_participantes INT DEFAULT 20,
    activa BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_admin) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Table: equipos (User Teams)
CREATE TABLE IF NOT EXISTS equipos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    id_usuario INT NOT NULL,
    id_liga INT NOT NULL,
    presupuesto_restante DECIMAL(10, 2) DEFAULT 50000,
    puntos_totales INT DEFAULT 0,
    posicion INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_usuario_liga (id_usuario, id_liga),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_liga) REFERENCES ligas(id) ON DELETE CASCADE
);

-- Table: equipo_pilotos (User Team Drivers - Many to Many)
CREATE TABLE IF NOT EXISTS equipo_pilotos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_equipo INT NOT NULL,
    id_piloto INT NOT NULL,
    precio_adquisicion DECIMAL(10, 2) NOT NULL,
    transferible BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_equipo_piloto (id_equipo, id_piloto),
    FOREIGN KEY (id_equipo) REFERENCES equipos(id) ON DELETE CASCADE,
    FOREIGN KEY (id_piloto) REFERENCES pilotos(id) ON DELETE RESTRICT
);

-- Table: carreras (Races/Grand Prix)
CREATE TABLE IF NOT EXISTS carreras (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    circuito VARCHAR(100) NOT NULL,
    fecha_carrera DATE NOT NULL,
    temporada INT NOT NULL DEFAULT 2025,
    pais VARCHAR(50),
    ronda INT,
    completada BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: resultados (Race Results)
CREATE TABLE IF NOT EXISTS resultados (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_carrera INT NOT NULL,
    id_piloto INT NOT NULL,
    posicion_llegada INT,
    puntos INT DEFAULT 0,
    vueltas_completadas INT,
    retirado BOOLEAN DEFAULT FALSE,
    tiempo_total VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_carrera_piloto (id_carrera, id_piloto),
    FOREIGN KEY (id_carrera) REFERENCES carreras(id) ON DELETE CASCADE,
    FOREIGN KEY (id_piloto) REFERENCES pilotos(id) ON DELETE RESTRICT
);

-- Table: puntuaciones_usuario (User Points by Race)
CREATE TABLE IF NOT EXISTS puntuaciones_usuario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_carrera INT NOT NULL,
    id_equipo INT NOT NULL,
    puntos_obtenidos INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_carrera_equipo (id_carrera, id_equipo),
    FOREIGN KEY (id_carrera) REFERENCES carreras(id) ON DELETE CASCADE,
    FOREIGN KEY (id_equipo) REFERENCES equipos(id) ON DELETE CASCADE
);

-- Indexes for performance
CREATE INDEX idx_usuario_email ON usuarios(email);
CREATE INDEX idx_piloto_escuderia ON pilotos(id_escuderia);
CREATE INDEX idx_liga_admin ON ligas(id_admin);
CREATE INDEX idx_equipo_usuario ON equipos(id_usuario);
CREATE INDEX idx_equipo_liga ON equipos(id_liga);
CREATE INDEX idx_carrera_temporada ON carreras(temporada);
CREATE INDEX idx_resultado_carrera ON resultados(id_carrera);
CREATE INDEX idx_puntuacion_carrera ON puntuaciones_usuario(id_carrera);
