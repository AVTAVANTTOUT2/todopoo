# Oral — `install.sql` (explication ligne par ligne)

Objectif du fichier : **installer** la base de données et la table de la TODO list.

## Pourquoi on met un fichier SQL ?
- **Pratique en cours** : tu peux montrer au prof que tu sais **créer** la BDD et la table sans “magie”.
- **Reproductible** : n’importe qui peut recréer la même base.

---

## Ligne par ligne

### L1
`CREATE DATABASE IF NOT EXISTS todo`
- **CREATE DATABASE** : crée une base de données.
- **IF NOT EXISTS** : évite une erreur si la base existe déjà (script relançable).
- **todo** : nom demandé (simple, explicite).

### L2
`CHARACTER SET utf8mb4`
- **utf8mb4** : l’encodage recommandé pour MySQL (supporte accents + emojis).
- **Cohérence** : on met le même encodage côté BDD et côté PDO (DSN `charset=utf8mb4`).

### L3
`COLLATE utf8mb4_unicode_ci;`
- **Collation** : règle la façon de comparer / trier les textes.
- **unicode_ci** : comparaisons “intelligentes” et insensibles à la casse en général.

### L4
(ligne vide)
- **Lisibilité** : sépare les blocs (pas obligatoire, mais pédagogique).

### L5
`USE todo;`
- Dit à MySQL : “les prochaines commandes concernent la base `todo`”.
- Sans ça, tu pourrais créer la table dans une autre base par erreur.

### L6
(ligne vide)
- Même raison : lisibilité.

### L7
`CREATE TABLE IF NOT EXISTS tache (`
- Crée la table demandée.
- **IF NOT EXISTS** : rend le script relançable sans planter.

### L8
`id_tache INT AUTO_INCREMENT PRIMARY KEY,`
- **INT** : identifiant numérique.
- **AUTO_INCREMENT** : MySQL génère automatiquement 1,2,3…
- **PRIMARY KEY** : identifiant unique (recherches rapides, nécessaire pour CRUD).

### L9
`titre VARCHAR(100) NOT NULL,`
- **VARCHAR(100)** : texte court limité à 100 caractères (consigne).
- **NOT NULL** : on interdit un titre vide côté BDD (contrainte simple).

### L10
`description TEXT,`
- **TEXT** : texte potentiellement long.
- Pas de `NOT NULL` : on autorise une description vide (optionnel).

### L11
`fait BOOLEAN NOT NULL DEFAULT 0,`
- **BOOLEAN** : MySQL le stocke en pratique comme un petit entier (0/1).
- **DEFAULT 0** : par défaut une tâche n’est pas faite.
- **NOT NULL** : évite `NULL` (état ambigu).

### L12
`date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP`
- **TIMESTAMP** : date+heure.
- **DEFAULT CURRENT_TIMESTAMP** : la date se remplit automatiquement à l’insertion.
- Avantage : le code PHP n’a pas à envoyer l’heure.

### L13
`) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;`
- **ENGINE=InnoDB** : demandé + meilleur moteur “standard” (transactions, FK…).
- **DEFAULT CHARSET=utf8mb4** : cohérence d’encodage au niveau table.

### L14
(fin)
- Rien de plus : ce fichier ne fait que l’installation.
