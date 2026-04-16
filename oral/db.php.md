# Oral — `db.php` (explication ligne par ligne)

Objectif du fichier : créer **une seule connexion PDO** réutilisable dans tout le projet (variable `$pdo`).

## Pourquoi PDO ?
- **Standard** en PHP moderne.
- **Sécurité** : avec requêtes préparées (dans `Tache.php`), on limite les injections SQL.
- **Exceptions** : plus simple à gérer qu’un mélange de retours `false`.

---

## Ligne par ligne

### L1
`<?php`
- Démarre le code PHP.
- Important : pas d’output avant, sinon certains `header()` peuvent échouer ailleurs.

### L2
`$host = 'localhost';`
- Serveur MySQL en local (phpMyAdmin / MAMP).
- Variable séparée pour rendre le DSN lisible.

### L3
`$db   = 'todo';`
- Nom de la base (créée par `install.sql`).

### L4
`$user = 'root';`
- Identifiant MySQL local (consigne).

### L5
`$pass = '';`
- Mot de passe vide dans certains environnements (consigne).
- On met une valeur par défaut *avant* d’essayer de se connecter.

### L6
(ligne vide)
- Lisibilité : sépare config et connexion.

### L7
`try {`
- On tente une connexion.
- L’idée : si la connexion échoue, on teste l’autre mot de passe (`root`) dans le `catch`.

### L8–L13
```php
$pdo = new PDO(
    "mysql:host=$host;dbname=$db;charset=utf8mb4",
    $user,
    $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
```
- **new PDO(...)** : ouvre une connexion à MySQL.
- **DSN** `"mysql:..."` :
  - `host=$host` : où est MySQL.
  - `dbname=$db` : quelle base utiliser.
  - `charset=utf8mb4` : garantit l’UTF-8 côté connexion (accents, emojis).
- **$user, $pass** : identifiants.
- **options** :
  - `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` : au lieu de retourner `false`, PDO lance une exception → plus simple, et on ne “rate” pas une erreur.

### L14
`} catch (PDOException $e) {`
- Si la connexion échoue (mauvais mot de passe / MySQL arrêté / base absente), on arrive ici.
- On capture précisément `PDOException` (erreurs PDO).

### L15
`$pass = 'root';`
- Deuxième cas demandé : parfois le mot de passe de `root` est `root`.

### L16–L21
Deuxième tentative de connexion avec le nouveau `$pass`.
- Même DSN et mêmes options pour rester cohérent.

### L22
`}`
- Fin du `catch`.

## Résultat important
- À la fin, on obtient **une variable globale** `$pdo` que `index.php` peut utiliser.
- Pas de fonction ici : c’est volontairement ultra simple pour un projet scolaire.
