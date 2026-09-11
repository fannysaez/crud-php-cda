<?php
session_start();
$pdo = new PDO('mysql:host=127.0.0.1;dbname=php_cours;port=3506', 'root', 'root');

$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $stmt = $pdo->prepare('SELECT * FROM pizza WHERE name LIKE ? ORDER BY name ASC');
    $stmt->execute(['%' . $search . '%']);
} else {
    $stmt = $pdo->query('SELECT * FROM pizza ORDER BY id');
}
$pizzas = $stmt->fetchAll(PDO::FETCH_ASSOC);
$nombreResultats = count($pizzas);

function tronquer($texte, $longueur = 60) {
    if (strlen($texte) > $longueur) {
        return substr($texte, 0, $longueur) . '...';
    }
    return $texte;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des pizzas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-4">

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['type'] ?? 'success' ?> d-flex align-items-center gap-2" id="messageSucces">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <?= htmlspecialchars($_SESSION['message']) ?>
            </div>
            <?php
            unset($_SESSION['message']);
            unset($_SESSION['type']);
            ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4 gap-3">
            <h1 class="mb-0">Liste des pizzas</h1>

            <form method="GET" class="d-flex gap-2 flex-grow-1" style="max-width: 400px;">
                <input type="text" name="search" class="form-control" placeholder="Rechercher par nom..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i>
                </button>
                <?php if (!empty($search)): ?>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                <?php endif; ?>
            </form>

            <a href="CRUD/add.php" class="btn btn-success text-nowrap">
                <i class="bi bi-plus-lg"></i> Ajouter une pizza
            </a>
        </div>

        <?php if (!empty($search)): ?>
            <p class="text-muted"><?= $nombreResultats ?> résultat(s) pour "<?= htmlspecialchars($search) ?>"</p>
        <?php endif; ?>

        <?php if (!empty($search) && $nombreResultats === 0): ?>
            <div class="alert alert-warning">Aucune pizza ne correspond à ta recherche.</div>
        <?php else: ?>
        <table class="table table-striped table-bordered align-middle" style="table-layout: fixed;">
            <thead class="table-dark">
                <tr>
                    <th style="width: 15%;">Nom</th>
                    <th style="width: 55%;">Description</th>
                    <th class="text-nowrap" style="width: 10%;">Prix</th>
                    <th class="text-center text-nowrap" style="width: 20%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pizzas as $pizza): ?>
                <tr>
                    <td><?= htmlspecialchars($pizza['name']) ?></td>
                    <td>
                        <?= htmlspecialchars(tronquer($pizza['description'])) ?>
                        <?php if (strlen($pizza['description']) > 60): ?>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modaleDesc<?= $pizza['id'] ?>" title="Voir plus">
                                <i class="bi bi-plus-circle"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td class="text-nowrap"><?= number_format($pizza['price'], 2) ?> €</td>
                    <td class="text-center text-nowrap">
                        <a href="item.php?id=<?= $pizza['id'] ?>" class="btn btn-sm btn-outline-primary" title="Voir">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="CRUD/edit.php?id=<?= $pizza['id'] ?>" class="btn btn-sm btn-outline-warning" title="Modifier">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modaleDelete<?= $pizza['id'] ?>" class="btn btn-sm btn-outline-danger" title="Supprimer">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <?php foreach ($pizzas as $pizza): ?>
    <div class="modal fade" id="modaleDesc<?= $pizza['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= htmlspecialchars($pizza['name']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?= htmlspecialchars($pizza['description']) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modaleDelete<?= $pizza['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Supprimer <?= htmlspecialchars($pizza['name']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Es-tu sûr de vouloir supprimer <strong class="text-primary"><?= htmlspecialchars($pizza['name']) ?></strong>
                    (<strong class="text-success"><?= number_format($pizza['price'], 2) ?> €</strong>) ?
                    Ses ingrédients liés seront aussi supprimés.
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <a href="CRUD/delete.php?id=<?= $pizza['id'] ?>" class="btn btn-danger">Confirmer la suppression</a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const messageSucces = document.getElementById('messageSucces');
        if (messageSucces) {
            setTimeout(function () {
                messageSucces.remove();
            }, 3000);
        }
    </script>
</body>
</html>