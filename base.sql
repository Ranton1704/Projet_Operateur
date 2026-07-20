PRAGMA foreign_keys = OFF;

DROP TABLE IF EXISTS prefixes;
CREATE TABLE prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT NOT NULL UNIQUE
);

DROP TABLE IF EXISTS types_operation;
CREATE TABLE types_operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL UNIQUE -- 'depot', 'retrait', 'transfert'
);

DROP TABLE IF EXISTS baremes_frais;
CREATE TABLE baremes_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_type_operation INTEGER NOT NULL,
    montant_min REAL NOT NULL,
    montant_max REAL NOT NULL,
    frais REAL NOT NULL,
    FOREIGN KEY (id_type_operation) REFERENCES types_operation(id),
    CHECK (montant_max >= montant_min)
);

DROP TABLE IF EXISTS comptes;
CREATE TABLE comptes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_telephone TEXT NOT NULL UNIQUE,
    solde REAL NOT NULL DEFAULT 0.0
);

DROP TABLE IF EXISTS operations;
CREATE TABLE operations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_type_operation INTEGER NOT NULL,
    numero_expediteur TEXT NOT NULL,
    numero_destinataire TEXT,
    montant REAL NOT NULL,
    frais REAL NOT NULL DEFAULT 0.0,
    date_operation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_type_operation) REFERENCES types_operation(id),
    FOREIGN KEY (numero_expediteur) REFERENCES comptes(numero_telephone),
    FOREIGN KEY (numero_destinataire) REFERENCES comptes(numero_telephone)
);

PRAGMA foreign_keys = ON;


INSERT INTO prefixes (prefixe) VALUES ('033'), ('037');

INSERT INTO types_operation (nom) VALUES ('depot'), ('retrait'), ('transfert');


INSERT INTO baremes_frais (id_type_operation, montant_min, montant_max, frais) VALUES
-- Pour les retraits
(2, 100, 1000, 50),
(2, 1001, 5000, 50),
(2, 5001, 10000, 100),
(2, 10001, 25000, 200),
(2, 25001, 50000, 400),
(2, 50010, 100000, 800),
(2, 100001, 250000, 1500),
(2, 250001, 500000, 1500),
(2, 500001, 1000000, 2500),
(2, 1000001, 2000000, 3000),
-- Pour les transferts
(3, 100, 1000, 50),
(3, 1001, 5000, 50),
(3, 5001, 10000, 100),
(3, 10001, 25000, 200),
(3, 25001, 50000, 400),
(3, 50010, 100000, 800),
(3, 100001, 250000, 1500),
(3, 250001, 500000, 1500),
(3, 500001, 1000000, 2500),
(3, 1000001, 2000000, 3000);