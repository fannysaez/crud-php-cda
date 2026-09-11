<?php
session_start();
$pdo = new PDO('mysql:host=127.0.0.1;dbname=php_cours;port=3506', 'root', 'root');

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $origine = $_POST['origine'];
    $price = $_POST['price'];
    $ingredientsTexte = $_POST['ingredients'];

    $stmt = $pdo->prepare('UPDATE pizza SET name = ?, description = ?, origine = ?, price = ? WHERE id = ?');
    $stmt->execute([$name, $description, $origine, $price, $id]);

    $stmt = $pdo->prepare('DELETE FROM ingredient WHERE pizza_id = ?');
    $stmt->execute([$id]);

    $listeIngredients = explode(',', $ingredientsTexte);
    foreach ($listeIngredients as $ingredient) {
        $ingredient = trim($ingredient);
        if (!empty($ingredient)) {
            $stmt = $pdo->prepare('INSERT INTO ingredient (name, pizza_id) VALUES (?, ?)');
            $stmt->execute([$ingredient, $id]);
        }
    }

    $_SESSION['message'] = 'Pizza "' . $name . '" modifiée avec succès !';
    $_SESSION['type'] = 'primary';
    header('Location: ../index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM pizza WHERE id = ?');
$stmt->execute([$id]);
$pizza = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pizza) {
    echo 'Pizza introuvable.';
} else {
    $stmt = $pdo->prepare('SELECT * FROM ingredient WHERE pizza_id = ?');
    $stmt->execute([$id]);
    $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $ingredientsTexte = implode(', ', array_column($ingredients, 'name'));
?>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <title>Modifier une pizza</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    </head>

    <body>
        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="card" style="max-width: 600px; margin: 0 auto;">
                <div class="card-body">
                    <h1 class="card-title mb-4">Modifier <?= htmlspecialchars($pizza['name']) ?></h1>

                    <form method="POST" id="formEdit">
                        <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($pizza['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" required><?= htmlspecialchars($pizza['description']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">À propos (origine)</label>
                            <textarea name="origine" class="form-control"><?= htmlspecialchars($pizza['origine'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ingrédients (séparés par des virgules)</label>
                            <input type="text" name="ingredients" class="form-control" value="<?= htmlspecialchars($ingredientsTexte) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prix</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="<?= $pizza['price'] ?>" required>
                        </div>

                        <div class="d-flex justify-content-center gap-2">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modaleEdit" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Enregistrer
                            </a>
                            <a href="../index.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Retour
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modaleEdit" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmer la modification</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="texteModaleEdit">
                        Enregistrer les modifications de cette pizza ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" form="formEdit" class="btn btn-warning">Confirmer</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.getElementById('modaleEdit').addEventListener('show.bs.modal', function() {
                const nom = document.querySelector('input[name="name"]').value;
                const prix = document.querySelector('input[name="price"]').value;
                document.getElementById('texteModaleEdit').innerHTML =
                    'Enregistrer <strong class="text-primary">' + nom + '</strong> au prix de <strong class="text-success">' + prix + ' €</strong> ?';
            });
        </script>
    </body>

    </html>
<?php } ?>