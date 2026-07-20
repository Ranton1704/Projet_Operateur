PRAGMA foreign_keys = OFF;

DROP TABLE IF EXISTS prefixes_autres_operateurs;
DROP TABLE IF EXISTS operations;
DROP TABLE IF EXISTS comptes;
DROP TABLE IF EXISTS baremes_frais;
DROP TABLE IF EXISTS autres_operateurs;
DROP TABLE IF EXISTS types_operation;
DROP TABLE IF EXISTS prefixes;

-- Préfixes de l'opérateur principal
CREATE TABLE prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT NOT NULL UNIQUE,
    CHECK(prefixe GLOB '[0-9][0-9][0-9]')
);

-- Types d'opérations
CREATE TABLE types_operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL UNIQUE
);

-- Opérateurs externes
CREATE TABLE autres_operateurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL UNIQUE,
    commission_pourcentage REAL NOT NULL DEFAULT 0.0,
    CHECK (
        commission_pourcentage >= 0
        AND commission_pourcentage <= 100
    )
);

-- Comptes
CREATE TABLE comptes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_telephone TEXT NOT NULL UNIQUE,
    solde REAL NOT NULL DEFAULT 0,
    CHECK(length(numero_telephone) = 10),
    CHECK(solde >= 0)
);

-- Barèmes de frais
CREATE TABLE baremes_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_type_operation INTEGER NOT NULL,
    montant_min REAL NOT NULL,
    montant_max REAL NOT NULL,
    frais REAL NOT NULL,

    FOREIGN KEY (id_type_operation)
        REFERENCES types_operation(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CHECK(montant_min > 0),
    CHECK(montant_max >= montant_min),
    CHECK(frais >= 0)
);

-- Opérations
CREATE TABLE operations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,

    id_type_operation INTEGER NOT NULL,

    numero_expediteur TEXT NOT NULL,
    numero_destinataire TEXT,

    montant REAL NOT NULL,
    frais REAL NOT NULL DEFAULT 0,

    est_autre_operateur INTEGER NOT NULL DEFAULT 0,

    id_autre_operateur INTEGER,

    commission_supplementaire REAL NOT NULL DEFAULT 0,

    date_operation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    statut TEXT NOT NULL DEFAULT 'SUCCES',

    FOREIGN KEY (id_type_operation)
        REFERENCES types_operation(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    FOREIGN KEY (numero_expediteur)
        REFERENCES comptes(numero_telephone)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    FOREIGN KEY (numero_destinataire)
        REFERENCES comptes(numero_telephone)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    FOREIGN KEY (id_autre_operateur)
        REFERENCES autres_operateurs(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CHECK(montant > 0),
    CHECK(frais >= 0),
    CHECK(commission_supplementaire >= 0),

    CHECK(
        statut IN ('SUCCES', 'ANNULE', 'ECHEC')
    ),

    CHECK(
        est_autre_operateur IN (0,1)
    )
);

-- Préfixes des autres opérateurs
CREATE TABLE prefixes_autres_operateurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,

    id_autre_operateur INTEGER NOT NULL,

    prefixe TEXT NOT NULL,

    FOREIGN KEY (id_autre_operateur)
        REFERENCES autres_operateurs(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    UNIQUE(id_autre_operateur, prefixe),

    CHECK(prefixe GLOB '[0-9][0-9][0-9]')
);

-- Index
CREATE INDEX idx_operations_date
ON operations(date_operation);

CREATE INDEX idx_operations_expediteur
ON operations(numero_expediteur);

CREATE INDEX idx_operations_destinataire
ON operations(numero_destinataire);

CREATE INDEX idx_baremes_type
ON baremes_frais(id_type_operation);

PRAGMA foreign_keys = ON;

--------------------------------------------------
-- DONNEES INITIALES
--------------------------------------------------

INSERT INTO prefixes (prefixe)
VALUES
('034'),
('038');

INSERT INTO types_operation (nom)
VALUES
('depot'),
('retrait'),
('transfert');

INSERT INTO baremes_frais
(
    id_type_operation,
    montant_min,
    montant_max,
    frais
)
VALUES

-- RETRAITS
(2,100,1000,50),
(2,1001,5000,50),
(2,5001,10000,100),
(2,10001,25000,200),
(2,25001,50000,400),
(2,50001,100000,800),
(2,100001,250000,1500),
(2,250001,500000,1500),
(2,500001,1000000,2500),
(2,1000001,2000000,3000),

-- TRANSFERTS
(3,100,1000,50),
(3,1001,5000,50),
(3,5001,10000,100),
(3,10001,25000,200),
(3,25001,50000,400),
(3,50001,100000,800),
(3,100001,250000,1500),
(3,250001,500000,1500),
(3,500001,1000000,2500),
(3,1000001,2000000,3000);

INSERT INTO autres_operateurs
(nom, commission_pourcentage)
VALUES
('Telma',5.0),
('Orange',3.0),
('Airtel',2.0);

INSERT INTO prefixes_autres_operateurs
(id_autre_operateur,prefixe)
VALUES
(1,'034'),
(1,'038'),
(2,'032'),
(2,'039'),
(3,'033');