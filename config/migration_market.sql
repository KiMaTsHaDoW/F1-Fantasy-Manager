-- Migration: dynamic market price system
USE f1fantasy;

CREATE TABLE IF NOT EXISTS market_prices (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_id    VARCHAR(60) NOT NULL,
    item_type  ENUM('driver','constructor') NOT NULL,
    price      DECIMAL(5,1) NOT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_item (item_id, item_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS price_transactions (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_id    VARCHAR(60) NOT NULL,
    item_type  ENUM('driver','constructor') NOT NULL,
    action     ENUM('buy','sell') NOT NULL,
    user_id    INT UNSIGNED NOT NULL,
    race_round INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pending (item_id, item_type, race_round)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS price_history (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_id       VARCHAR(60) NOT NULL,
    item_type     ENUM('driver','constructor') NOT NULL,
    price         DECIMAL(5,1) NOT NULL,
    race_round    INT UNSIGNED NOT NULL,
    calculated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_item_round (item_id, item_type, race_round)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
