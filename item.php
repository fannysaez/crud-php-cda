<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=php_cours;port=3506', 'root', 'root');

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
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
?>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($pizza['name']) ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    </head>

    <body>
        <div class="container mt-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end">
                        <span class="badge rounded-pill bg-success fs-6"><?= number_format($pizza['price'], 2) ?> €</span>
                    </div>
                    <h1 class="card-title text-center"><?= htmlspecialchars($pizza['name']) ?></h1>
                    <p class="card-text fst-italic text-center mx-auto" style="max-width: 600px;"><?= htmlspecialchars($pizza['description']) ?></p>

                    <?php if (!empty($pizza['origine'])): ?>
                        <h2 class="h5 mt-4">À propos</h2>
                        <p class="card-text"><?= htmlspecialchars($pizza['origine']) ?></p>
                    <?php endif; ?>

                    <h2 class="h5 mt-4">Ingrédients</h2>
                    <ul class="list-group list-group-flush mb-4">
                        <?php foreach ($ingredients as $ingredient): ?>
                            <li class="list-group-item"><?= htmlspecialchars($ingredient['name']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="text-center mt-4">
                        <a href="CRUD/edit.php?id=<?= $pizza['id'] ?>" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Modifier
                        </a>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>

    </html>
<?php } ?>