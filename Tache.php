<?php
class Tache
{
    private $pdo;

    public function __construct($pdo) { $this->pdo = $pdo; }

    // Récupère toutes les tâches.
    public function all() {
        return $this->pdo->query("SELECT * FROM tache ORDER BY id_tache DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère une tâche par son id.
    public function find($id) {
        $q = $this->pdo->prepare("SELECT * FROM tache WHERE id_tache = ?");
        $q->execute([$id]);
        return $q->fetch(PDO::FETCH_ASSOC);
    }

    // Crée une nouvelle tâche.
    public function create($titre, $description) {
        $q = $this->pdo->prepare("INSERT INTO tache (titre, description) VALUES (?, ?)");
        return $q->execute([$titre, $description]);
    }

    // Met à jour une tâche.
    public function update($id, $titre, $description) {
        $q = $this->pdo->prepare("UPDATE tache SET titre = ?, description = ? WHERE id_tache = ?");
        return $q->execute([$titre, $description, $id]);
    }

    // Supprime une tâche.
    public function delete($id) {
        $q = $this->pdo->prepare("DELETE FROM tache WHERE id_tache = ?");
        return $q->execute([$id]);
    }

    // Inverse l'état fait/pas fait.
    public function toggle($id) {
        $q = $this->pdo->prepare("UPDATE tache SET fait = NOT fait WHERE id_tache = ?");
        return $q->execute([$id]);
    }
}
