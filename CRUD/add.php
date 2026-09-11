<?php
session_start();
$pdo = new PDO('mysql:host=127.0.0.1;dbname=php_cours;port=3506', 'root', 'root');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $origine = $_POST['origine'];
    $price = $_POST['price'];

    $stmt = $pdo->prepare('INSERT INTO pizza (name, description, origine, price) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $description, $origine, $price]);

    $_SESSION['message'] = 'Pizza "' . $name . '" ajoutée avec succès !';
    $_SESSION['type'] = 'success';
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter une pizza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <div class="card-body">
                <h1 class="card-title mb-4">Ajouter une pizza</h1>

                <form method="POST" id="formAjout">
                    <div class="mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">À propos (origine)</label>
                        <textarea name="origine" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prix</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modaleAdd" class="btn btn-success">
                            <i class="bi bi-plus-lg"></i> Ajouter
                        </a>
                        <a href="../index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modaleAdd" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer l'ajout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="texteModaleAdd">
                    Ajouter cette pizza ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="formAjout" class="btn btn-success">Confirmer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('modaleAdd').addEventListener('show.bs.modal', function() {
            const nom = document.querySelector('input[name="name"]').value;
            const prix = document.querySelector('input[name="price"]').value;
            document.getElementById('texteModaleAdd').innerHTML =
                'Ajouter la pizza <strong class="text-primary">' + nom + '</strong> au prix de <strong class="text-success">' + prix + ' €</strong> ?';
        });
    </script>
</body>

</html>