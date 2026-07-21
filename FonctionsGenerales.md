# Bibliothèque de Fonctions Utilitaires Généralisées

## Objectif

Cette bibliothèque regroupe des fonctions génériques réutilisables dans n'importe quel projet (Web, API REST, application mobile, gestion financière, CRUD, ERP, etc.). L'objectif est d'éviter la duplication de code et de centraliser les traitements courants.

---

# 1. Gestion des données

### get()

Récupère une valeur dans un tableau à partir d'un chemin.

```php
function get(
    array $data,
    string $path,
    mixed $default = null
): mixed
```

Exemple :

```php
get($user, 'profil.nom');
```

---

### set()

Ajoute ou modifie une valeur dans une structure de données.

```php
function set(
    array &$data,
    string $path,
    mixed $value
): void
```

---

### has()

Vérifie l'existence d'une donnée.

```php
function has(
    array $data,
    string $path
): bool
```

---

# 2. Transformation des données

### normalize()

Normalise une donnée selon une règle.

```php
function normalize(
    mixed $value,
    string $type
): mixed
```

Types possibles :

```text
upper
lower
trim
int
float
string
boolean
```

---

### mapData()

Transforme chaque élément d'une collection.

```php
function mapData(
    array $items,
    callable $callback
): array
```

---

### filterData()

Filtre une collection selon une condition.

```php
function filterData(
    array $items,
    callable $callback
): array
```

---

### groupBy()

Regroupe une collection par champ.

```php
function groupBy(
    array $items,
    string $field
): array
```

---

# 3. Recherche

### findFirst()

Retourne le premier élément correspondant à un critère.

```php
function findFirst(
    array $items,
    callable $callback
): mixed
```

---

### findAll()

Retourne tous les éléments correspondants.

```php
function findAll(
    array $items,
    callable $callback
): array
```

---

# 4. Validation

### validate()

Valide un ensemble de données.

```php
function validate(
    array $data,
    array $rules
): array
```

Règles possibles :

```text
required
min
max
email
numeric
integer
string
date
boolean
regex
```

---

### isValid()

Validation simple d'une valeur.

```php
function isValid(
    mixed $value,
    string $rule
): bool
```

---

# 5. Gestion des chaînes

### sanitize()

Nettoie une chaîne utilisateur.

```php
function sanitize(
    string $value
): string
```

---

### slug()

Transforme un texte en identifiant URL.

```php
function slug(
    string $value
): string
```

---

### mask()

Masque une donnée sensible.

```php
function mask(
    string $value,
    int $visible = 3
): string
```

---

# 6. Gestion des dates

### dateFormat()

Convertit une date dans un format donné.

```php
function dateFormat(
    mixed $date,
    string $format
): string
```

---

### dateDiff()

Calcule la différence entre deux dates.

```php
function dateDiff(
    string $start,
    string $end,
    string $unit = 'day'
): int
```

---

### isDateBetween()

Vérifie qu'une date appartient à un intervalle.

```php
function isDateBetween(
    string $date,
    string $start,
    string $end
): bool
```

---

# 7. Calculs

### calculatePercentage()

Calcule un pourcentage.

```php
function calculatePercentage(
    float $value,
    float $total
): float
```

---

### safeDivide()

Division sécurisée.

```php
function safeDivide(
    float $a,
    float $b,
    float $default = 0
): float
```

---

### roundNumber()

Arrondi une valeur.

```php
function roundNumber(
    float $value,
    int $precision = 2
): float
```

---

# 8. Pagination

### paginate()

Construit une pagination standard.

```php
function paginate(
    array $items,
    int $page,
    int $limit
): array
```

Retour :

```json
{
    "data": [],
    "page": 1,
    "limit": 10,
    "total": 100,
    "pages": 10
}
```

---

# 9. Réponses API

### response()

Construit une réponse standard.

```php
function response(
    bool $success,
    string $message,
    mixed $data = null,
    array $errors = []
): array
```

---

### success()

Réponse de succès.

```php
function success(
    mixed $data = null,
    string $message = 'OK'
): array
```

---

### error()

Réponse d'erreur.

```php
function error(
    string $message,
    array $errors = []
): array
```

---

# 10. Sécurité

### generateToken()

Génère un token sécurisé.

```php
function generateToken(
    int $length = 32
): string
```

---

### hashValue()

Hash une valeur.

```php
function hashValue(
    string $value
): string
```

---

### verifyHash()

Vérifie un hash.

```php
function verifyHash(
    string $value,
    string $hash
): bool
```

---

# 11. Gestion des fichiers

### fileExtension()

Retourne l'extension d'un fichier.

```php
function fileExtension(
    string $filename
): string
```

---

### fileSize()

Convertit une taille de fichier.

```php
function fileSize(
    int $bytes
): string
```

---

# 12. Gestion des erreurs

### tryCatch()

Exécute une fonction avec gestion d'erreur.

```php
function tryCatch(
    callable $callback,
    mixed $default = null
): mixed
```

---

### retry()

Relance automatiquement une opération en cas d'échec.

```php
function retry(
    callable $callback,
    int $attempts = 3
): mixed
```

---

# Organisation recommandée

```text
Utils/
├── DataUtils.php
├── ValidationUtils.php
├── StringUtils.php
├── DateUtils.php
├── NumberUtils.php
├── SecurityUtils.php
├── ApiUtils.php
├── FileUtils.php
└── ErrorUtils.php
```

# Fonctions prioritaires

1. get()
2. set()
3. validate()
4. normalize()
5. mapData()
6. filterData()
7. response()
8. paginate()
9. sanitize()
10. retry()

Ces fonctions constituent le noyau d'une bibliothèque utilitaire réutilisable dans la majorité des projets PHP, Java, Python ou JavaScript.
