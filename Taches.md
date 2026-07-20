## Nom Binôme : Ranto (4242) et Ajaina (4371)
# Version 1 – Système Mobile Money

## 1. Initialisation du projet

### Mise en place de l'environnement (Ranto)

* Création du projet CodeIgniter 4
* Configuration de l'environnement de développement
* Vérification du bon fonctionnement de l'application

### Configuration de la base de données (Ajaina)

* Configuration du fichier `/app/Config/Database.php` pour SQLite
* Création du fichier `base.sql` à la racine du projet
* Création des tables nécessaires :

  * utilisateurs
  * préfixes
  * types_operations
  * baremes_frais
  * comptes
  * transactions
* Import de la base de données

---

## 2. Authentification

### Connexion des clients (Ranto)

* Authentification via numéro de téléphone
* Vérification de l'existence du numéro
* Création automatique du compte si le numéro possède un préfixe valide
* Gestion de la session utilisateur

### Développement technique (Ranto)

* Configuration des routes
* Création du contrôleur d'authentification
* Création des vues de connexion
* Gestion de la déconnexion


## 3. Module Opérateur 

### Gestion des préfixes (Ajaina)

* Ajouter un préfixe
* Modifier un préfixe
* Supprimer un préfixe
* Lister les préfixes autorisés

### Gestion des types d'opérations (Ajaina)

* Création des types :

  * Dépôt
  * Retrait
  * Transfert
* Activation / désactivation d'un type d'opération

### Gestion des barèmes de frais (Ajaina)

* Création des tranches de montants
* Définition des frais associés
* Modification des barèmes
* Consultation des barèmes en vigueur

### Suivi financier (Ranto)

* Calcul des revenus générés par les frais
* Consultation des gains par type d'opération
* Consultation du gain total de l'opérateur

### Situation des comptes clients (Ranto)

* Nombre total de comptes
* Solde global détenu par les clients
* Consultation du solde d'un client
* Liste des comptes et de leurs soldes

---

## 4. Module Client

### Connexion simplifiée (Ranto)

* Connexion à l'aide du numéro de téléphone
* Vérification du préfixe autorisé

### Gestion du compte (Ajaina)

#### Consultation du solde (Ajaina)

* Affichage du solde actuel

#### Dépôt (Ajaina)

* Enregistrement d'un dépôt
* Mise à jour automatique du solde

#### Retrait (Ajaina)

* Vérification du solde disponible
* Calcul automatique des frais
* Mise à jour du solde

#### Transfert (Ajaina)

* Sélection du bénéficiaire
* Vérification du solde disponible
* Calcul automatique des frais
* Mise à jour des deux comptes

#### Historique des opérations (Ajaina)

* Liste chronologique des transactions
* Affichage du type d'opération
* Affichage du montant
* Affichage des frais appliqués
* Affichage de la date de l'opération
