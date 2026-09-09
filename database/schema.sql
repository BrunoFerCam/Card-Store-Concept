
CREATE DATABASE IF NOT EXISTS card_admin
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE card_admin;

CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    username      VARCHAR(60)      NOT NULL,
    password_hash VARCHAR(255)     NOT NULL,
    created_at    TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cards (
    id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name_en      VARCHAR(120)  NOT NULL,
    name_pt      VARCHAR(120)  NULL,
    game         VARCHAR(60)   NOT NULL,
    edition_id   VARCHAR(20)   NULL,
    edition_name VARCHAR(120)  NULL,
    image        VARCHAR(255)  NULL,
    rarity       VARCHAR(40)   NULL,
    created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
                               ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_cards_game (game),
    KEY idx_cards_edition_id (edition_id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS editions (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    game       VARCHAR(60)  NOT NULL,
    code       VARCHAR(20)  NOT NULL,
    name       VARCHAR(120) NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_editions_game_code (game, code)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rarities (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    game       VARCHAR(60)  NOT NULL,
    name       VARCHAR(40)  NOT NULL,
    color      CHAR(7)      NOT NULL DEFAULT '#6b7280',
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_rarities_game_name (game, name)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
