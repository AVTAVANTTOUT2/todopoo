# Oral — `index.php` (explication ligne par ligne)

Objectif du fichier : **page unique** qui fait :
- Affichage (HTML brut)
- Gestion des actions (GET/POST)
- Mode édition (pré-remplir le formulaire)

Sans framework, sans JS : tout se fait avec **liens** + **formulaires** + **redirect**.

---

## Concepts importants à expliquer à l’oral

### 1) Pourquoi un “router” minimal avec `action` ?
Pour faire plusieurs opérations (delete/toggle/edit) dans **un seul fichier** :
- `index.php?action=delete&id=3`
- `index.php?action=toggle&id=3`
- `index.php?action=edit&id=3`

Ça remplace un vrai routeur MVC (interdit ici car trop “complexe”).

### 2) Pourquoi `header('Location: index.php')` après une action ?
C’est le pattern **PRG** : *Post/Redirect/Get*.
- Évite le “resoumettre le formulaire” quand on actualise.
- Fait revenir sur une URL propre (sans `action=...`).

### 3) Pourquoi `htmlspecialchars()` ?
Pour afficher du texte utilisateur **sans exécuter du HTML** (anti-XSS).

---

## Ligne par ligne (PHP en haut)

### L1
`<?php`
- Début du script PHP.

### L2
`header('Content-Type: text/html; charset=UTF-8');`
- Force l’encodage de sortie en UTF-8 (accents).
- “Propre” même si le navigateur devine souvent tout seul.

### L3
`require 'db.php';`
- Inclut la connexion PDO `$pdo`.
- `require` (et pas `include`) : si ça manque, on stoppe (erreur fatale) → plus sûr.

### L4
`require 'Tache.php';`
- Inclut la classe `Tache`.

### L5
(vide)
- Lisibilité.

### L6
`$tache = new Tache($pdo);`
- On instancie l’objet “service” pour accéder au CRUD.
- On lui donne la connexion : la classe ne crée pas la BDD elle-même.

### L7
`$action = $_GET['action'] ?? '';`
- Lit l’action dans l’URL.
- `?? ''` : si absent, on met une chaîne vide pour éviter des warnings.

### L8
`$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;`
- Récupère l’id depuis l’URL.
- Cast `(int)` : on force un entier (évite “3abc”, etc.).
- `0` signifie “pas d’id valide”.

### L9
`$edit = null;`
- Variable qui contiendra la tâche à modifier (ou `null` si pas en mode édition).

---

## Actions GET (delete / toggle / edit)

### L11
`if ($action === 'delete' && $id) { ... }`
- Si on demande `delete` et qu’on a un id non nul :
  - on supprime
  - on redirige
  - on `exit` pour arrêter le script (évite de continuer à afficher après une redirection)

Pourquoi c’est en GET ?
- Consigne du sujet.
- (Dans la vraie vie on préfèrerait POST pour delete, mais ici on suit l’exercice.)

### L12
`if ($action === 'toggle' && $id) { ... }`
- Même principe : on coche/décoche et on redirige.
- Important : **pas de JS** → on clique un lien.

### L13
`if ($action === 'edit' && $id) { $edit = $tache->find($id); }`
- Ici pas de redirect : on veut **afficher** la page avec le formulaire pré-rempli.
- `find($id)` récupère la ligne à éditer.

---

## Formulaire POST (create / update)

### L15
`if ($_SERVER['REQUEST_METHOD'] === 'POST') {`
- On distingue clairement : POST = formulaire soumis.

### L16
`$titre = trim($_POST['titre'] ?? '');`
- Récupère le champ `titre`.
- `trim()` : enlève espaces inutiles.
- `?? ''` : évite warnings si absent.

### L17
`$description = trim($_POST['description'] ?? '');`
- Même logique pour la description.

### L18
`$idPost = (int)($_POST['id_tache'] ?? 0);`
- Champ caché : si > 0 → on est en **update**, sinon **create**.

### L19
`if ($titre !== '') {`
- Contrôle minimal côté serveur : on refuse un titre vide.
- (Le HTML a aussi `required`, mais **il ne suffit pas** : on valide toujours côté serveur.)

### L20
`if ($idPost) { $tache->update(...); }`
- Si on a un id, on met à jour cette tâche.

### L21
`else { $tache->create(...); }`
- Sinon on crée une nouvelle tâche.

### L23–L25
Redirect + exit
- C’est le PRG : évite les doubles ajouts sur refresh.

---

## Chargement de la liste

### L27
`$liste = $tache->all();`
- On récupère toutes les tâches pour les afficher dans la table.
- On le fait **après** les actions (sinon on afficherait une version “avant modification”).

### L28
`?>`
- On ferme PHP pour écrire le HTML plus simplement.

---

## Partie HTML (affichage)

### L29
`<!doctype html>`
- Déclare HTML5.

### L30
`<html lang="fr">`
- Langue du document : bon pour accessibilité et SEO (même en local).

### L31–L34
`<head> ...`
- `meta charset="UTF-8"` : encodage côté HTML.
- `title` : titre de l’onglet.

### L35–L37
`<body> ... <h1>`
- Titre principal.

---

## Formulaire (ajout / édition)

### L38
`<h2><?= $edit ? 'Modifier...' : 'Ajouter...' ?></h2>`
- Ternaire PHP : change le titre selon le mode.
- Compact et lisible.

### L39
`<form method="post" action="index.php">`
- Envoie les champs en POST vers la même page.
- `action="index.php"` : explicite (même si le défaut serait la page courante).

### L40
`<input type="hidden" name="id_tache" value="...">`
- Champ caché : c’est le “switch” create/update.
- Si on édite → id réel ; sinon → `0`.

### L43
`<input ... required value="<?= ... ?>">`
- `required` : aide côté navigateur.
- `maxlength="100"` : cohérent avec `VARCHAR(100)` SQL (évite d’envoyer trop long).
- `value="<?= htmlspecialchars(...) ?>"` :
  - en mode edit : on pré-remplit
  - sinon : vide
  - `htmlspecialchars` : empêche d’injecter du HTML dans le formulaire.

### L47
`<textarea ...><?= ... ?></textarea>`
- Pour un texte plus long.
- Pré-remplissage identique en mode edit.
- `htmlspecialchars` aussi (sinon on peut casser le HTML).

### L49
`<button type="submit">...`
- Texte dépend du mode (ajouter vs mettre à jour).

### L50
`<?php if ($edit): ?> <a href="index.php">Annuler</a><?php endif; ?>`
- Affiche “Annuler” seulement en mode édition.
- “Annuler” = revenir à la page sans `action=edit` → formulaire vide.

---

## Tableau de liste

### L54
`<table border="1" ...>`
- Table HTML brute (consigne : pas de CSS).
- `border/cellpadding/cellspacing` : rendu lisible sans CSS.

### L62
`<?php foreach ($liste as $row): ?>`
- Boucle d’affichage : une ligne par tâche.

### L65–L67
Lien toggle + affichage `[x]` / `[ ]`
- Pas de checkbox HTML car on ne veut pas de JS ni de formulaire supplémentaire.
- On simule la case par un lien cliquable.

### L69–L71
`htmlspecialchars(...)` partout
- Données venant de la BDD → à l’origine, ça vient de l’utilisateur → on échappe.

### L73
Lien modifier :
- `action=edit` charge la tâche dans `$edit` et pré-remplit le formulaire.

### L75
Lien supprimer :
- `action=delete` appelle `delete()` puis redirect.

---

## Ce que tu peux dire en conclusion à l’oral
- “J’ai une page unique qui lit `action` et `id`.”
- “La classe `Tache` encapsule toutes les requêtes SQL via PDO.”
- “Après chaque action, je redirige pour éviter la double soumission.”
- “J’échappe l’affichage avec `htmlspecialchars` pour la sécurité.”
