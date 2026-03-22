-- ============================================================
--  SUITDEM ERP — Database Schema v19.75
--  MySQL 5.7+ compatible
--  ⚠️  Sur hébergement mutualisé (Clever Cloud, LWS...) :
--  La base est déjà créée par l'hébergeur.
--  Sélectionner la base dans phpMyAdmin AVANT d'importer,
--  puis importer ce fichier directement.
-- ============================================================

-- ── CLIENTS ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS clients (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `Code client`   VARCHAR(50)  NOT NULL UNIQUE,
    `Client`        VARCHAR(150) NOT NULL,
    `Contact`       VARCHAR(150) DEFAULT '',
    `Telephone`     VARCHAR(30)  DEFAULT '',
    `Email`         VARCHAR(150) DEFAULT '',
    `Adresse`       TEXT         DEFAULT '',
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── PRODUCTS (Formules / Packs) ───────────────────────────────
CREATE TABLE IF NOT EXISTS products (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `products`      VARCHAR(200) NOT NULL,
    `Prix`          DECIMAL(10,2) DEFAULT 0.00,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── SERVICES ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS services (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `Services`      VARCHAR(200) NOT NULL,
    `Prix`          DECIMAL(10,2) DEFAULT 0.00,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── OPTIONS ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `options` (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `Options`       VARCHAR(200) NOT NULL,
    `Prix`          DECIMAL(10,2) DEFAULT 0.00,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── MISC (Divers) ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS misc (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `Divers`        VARCHAR(200) NOT NULL,
    `Prix`          DECIMAL(10,2) DEFAULT 0.00,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── INSURANCE (Assurance) ─────────────────────────────────────
CREATE TABLE IF NOT EXISTS insurance (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `Assurance`     VARCHAR(200) NOT NULL,
    `Prix`          DECIMAL(10,2) DEFAULT 0.00,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── VEHICLES ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS vehicles (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `Véhicule`      VARCHAR(100) NOT NULL,
    `Immatriculation` VARCHAR(20) DEFAULT '',
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── TEAM ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS team (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `Nom`           VARCHAR(150) NOT NULL,
    `Fonction`      VARCHAR(100) DEFAULT '',
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── OPERATIONS ───────────────────────────────────────────────
-- Core columns match what App.saveOperation() sends.
-- FULL_DATA_JSON stores the full serialised form state (cart,
-- prestations, inputs, etc.) exactly as before with Supabase.
CREATE TABLE IF NOT EXISTS operations (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `Dossier`       VARCHAR(50)  NOT NULL UNIQUE,   -- e.g. 2026/00001
    `Date`          VARCHAR(20)  DEFAULT '',
    `Client`        VARCHAR(150) DEFAULT '',
    `Tel`           VARCHAR(30)  DEFAULT '',
    `Email`         VARCHAR(150) DEFAULT '',
    `Adresse Dep`   TEXT         DEFAULT '',
    `Adresse Arr`   TEXT         DEFAULT '',
    `Volume`        VARCHAR(20)  DEFAULT '',
    `Categorie`     VARCHAR(50)  DEFAULT '',
    `Total TTC`     DECIMAL(10,2) DEFAULT 0.00,
    `FULL_DATA_JSON` LONGTEXT    DEFAULT '',         -- full form state
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  OPTIONAL — seed some sample data to verify the app loads
-- ============================================================

INSERT IGNORE INTO products (`products`, `Prix`) VALUES
    ('Pack Standard', 350.00),
    ('Pack Confort',  550.00),
    ('Pack Premium',  800.00);

INSERT IGNORE INTO services (`Services`, `Prix`) VALUES
    ('Monte-meuble',  120.00),
    ('Garde-meuble',   80.00);

INSERT IGNORE INTO vehicles (`Véhicule`, `Immatriculation`) VALUES
    ('Camion 20m³', 'AA-123-BB'),
    ('Camion 30m³', 'CC-456-DD');

INSERT IGNORE INTO team (`Nom`, `Fonction`) VALUES
    ('Jean Dupont',   'Chef d\'équipe'),
    ('Marc Martin',   'Équipier'),
    ('Paul Bernard',  'Équipier');

-- ============================================================
--  Done. All 9 tables created.
-- ============================================================
