<?php
header('Content-Type: text/html; charset=UTF-8');
require 'db.php';
require 'Tache.php';

$tache = new Tache($pdo);
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$edit = null;

if ($action === 'delete' && $id) { $tache->delete($id); header('Location: index.php'); exit; }
if ($action === 'toggle' && $id) { $tache->toggle($id); header('Location: index.php'); exit; }
if ($action === 'edit' && $id) { $edit = $tache->find($id); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $idPost = (int)($_POST['id_tache'] ?? 0);
    if ($titre !== '') {
        if ($idPost) { $tache->update($idPost, $titre, $description); }
        else { $tache->create($titre, $description); }
    }
    header('Location: index.php');
    exit;
}

$liste = $tache->all();
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TODO LIST POO PHP</title>
</head>
<body>
    <h1>TODO LIST</h1>

    <h2><?= $edit ? 'Modifier une tâche' : 'Ajouter une tâche' ?></h2>
    <form method="post" action="index.php">
        <input type="hidden" name="id_tache" value="<?= $edit ? (int)$edit['id_tache'] : 0 ?>">
        <p>
            <label>Titre</label><br>
            <input type="text" name="titre" maxlength="100" required value="<?= $edit ? htmlspecialchars($edit['titre']) : '' ?>">
        </p>
        <p>
            <label>Description</label><br>
            <textarea name="description" rows="3" cols="50"><?= $edit ? htmlspecialchars($edit['description']) : '' ?></textarea>
        </p>
        <button type="submit"><?= $edit ? 'Mettre à jour' : 'Ajouter' ?></button>
        <?php if ($edit): ?> <a href="index.php">Annuler</a><?php endif; ?>
    </form>

    <h2>Liste des tâches</h2>
    <table border="1" cellpadding="6" cellspacing="0">
        <tr>
            <th>Fait</th>
            <th>Titre</th>
            <th>Description</th>
            <th>Date création</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($liste as $row): ?>
            <tr>
                <td>
                    <a href="index.php?action=toggle&id=<?= (int)$row['id_tache'] ?>">
                        <?= $row['fait'] ? '[x]' : '[ ]' ?>
                    </a>
                </td>
                <td><?= htmlspecialchars($row['titre']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['date_creation']) ?></td>
                <td>
                    <a href="index.php?action=edit&id=<?= (int)$row['id_tache'] ?>">modifier</a>
                    |
                    <a href="index.php?action=delete&id=<?= (int)$row['id_tache'] ?>">supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
