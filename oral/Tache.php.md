# Oral — `Tache.php` (explication ligne par ligne)

Objectif du fichier : **mettre toute la logique BDD** dans une seule classe `Tache` (CRUD), pour que `index.php` reste surtout une page d’interface.

## Pourquoi une classe ?
- **POO simple** : la classe représente “la partie tâches” du projet.
- **Lisibilité** : les requêtes SQL sont centralisées au même endroit.
- **Réutilisable** : `index.php` appelle juste des méthodes claires (`create`, `update`, etc.).

## Pourquoi on injecte `$pdo` dans le constructeur ?
- Séparation : `db.php` crée la connexion, `Tache.php` l’utilise.
- Testable (en théorie) : on pourrait remplacer `$pdo` par un autre objet/connexion.

---

## Ligne par ligne

### L1
`<?php`
- Début du fichier PHP.

### L2
`class Tache`
- Déclare la classe (pas de namespace, consigne).
- Nom simple et en français : correspond au domaine (tâches).

### L3
`{`
- Début du bloc de classe.

### L4
`private $pdo;`
- Propriété privée : on stocke la connexion PDO.
- **private** : le reste du code ne doit pas modifier la connexion directement.

### L5
(ligne vide)
- Lisibilité.

### L6
`public function __construct($pdo) { $this->pdo = $pdo; }`
- Constructeur : reçoit la connexion (injection).
- `$this->pdo = $pdo;` : on garde la connexion dans l’objet.
- Format “sur une ligne” : plus court, mais reste lisible pour un projet scolaire.

---

## Méthode `all()`

### L8
`// Récupère toutes les tâches.`
- Commentaire court demandé : explique l’intention.

### L9
`public function all() {`
- Méthode publique : `index.php` doit pouvoir l’appeler.

### L10
`return $this->pdo->query("SELECT * FROM tache ORDER BY id_tache DESC")->fetchAll(PDO::FETCH_ASSOC);`
- **query(...)** : requête simple sans paramètres.
- `SELECT * FROM tache` : récupère toutes les colonnes (projet minimaliste).
- `ORDER BY id_tache DESC` : affiche les tâches récentes en premier.
- `fetchAll(PDO::FETCH_ASSOC)` : tableau de tableaux associatifs (`['titre'=>..., ...]`).
- On `return` directement pour garder le code court.

### L11
`}`
- Fin de la méthode.

---

## Méthode `find($id)`

### L13
Commentaire : intention.

### L14
`public function find($id) {`
- On récupère une seule ligne par identifiant.

### L15
`$q = $this->pdo->prepare("SELECT * FROM tache WHERE id_tache = ?");`
- **prepare** : requête préparée (sécurité + bonne pratique).
- `?` : paramètre positionnel (ultra simple).
- Pourquoi préparer ? Parce que `$id` vient d’un GET, donc de l’utilisateur : on évite l’injection SQL.

### L16
`$q->execute([$id]);`
- Envoie la valeur au paramètre `?`.
- Tableau `[$id]` : correspond à l’ordre des `?`.

### L17
`return $q->fetch(PDO::FETCH_ASSOC);`
- `fetch(...)` : une seule ligne (ou `false` si pas trouvée).
- `PDO::FETCH_ASSOC` : tableau associatif.

### L18
Fin méthode.

---

## Méthode `create($titre, $description)`

### L20
Commentaire : intention.

### L21
`public function create($titre, $description) {`
- On insère une nouvelle tâche.

### L22
`$q = $this->pdo->prepare("INSERT INTO tache (titre, description) VALUES (?, ?)");`
- Requête préparée : `titre` et `description` viennent d’un POST.
- `VALUES (?, ?)` : deux paramètres (dans l’ordre).
- On ne met pas `fait` ni `date_creation` :
  - `fait` a un `DEFAULT 0` côté SQL.
  - `date_creation` a un `DEFAULT CURRENT_TIMESTAMP` côté SQL.
  - Donc le PHP reste minimal.

### L23
`return $q->execute([$titre, $description]);`
- Exécute l’INSERT avec les valeurs.
- Retourne `true/false` (utile si on voulait gérer un message, même si ici on redirect).

### L24
Fin méthode.

---

## Méthode `update($id, $titre, $description)`

### L26–L30
- Même logique : requête préparée.
- `WHERE id_tache = ?` : cible exactement une tâche.
- On met l’`$id` en dernier car c’est le dernier `?`.

Pourquoi on **n’update pas** `date_creation` ?
- Car c’est la date de création, donc on ne la modifie pas.

---

## Méthode `delete($id)`

### L32–L36
- Supprime une tâche.
- Requête préparée : l’id vient de l’utilisateur.
- `DELETE FROM tache WHERE id_tache = ?` : supprime seulement une ligne.

---

## Méthode `toggle($id)`

### L38
Commentaire : intention.

### L39
`public function toggle($id) {`
- On veut cocher/décocher sans JS.

### L40
`$q = $this->pdo->prepare("UPDATE tache SET fait = NOT fait WHERE id_tache = ?");`
- Astuce SQL : `fait = NOT fait`
  - Si `fait = 0` → `NOT 0` donne `1`
  - Si `fait = 1` → `NOT 1` donne `0`
- Avantage : pas besoin de faire un `SELECT` avant, donc c’est très court.

### L41
`return $q->execute([$id]);`
- Exécute l’UPDATE avec l’id.

### L42–L44
- Fin méthode + fin classe.
