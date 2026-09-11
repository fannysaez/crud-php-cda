<?php
session_start();
$pdo = new PDO('mysql:host=127.0.0.1;dbname=php_cours;port=3506', 'root', 'root');

$id = $_GET['id'] ?? null;
$name = '';

if ($id) {
    $stmt = $pdo->prepare('SELECT name FROM pizza WHERE id = ?');
    $stmt->execute([$id]);
    $pizza = $stmt->fetch(PDO::FETCH_ASSOC);
    $name = $pizza['name'] ?? '';

    $stmt = $pdo->prepare('DELETE FROM ingredient WHERE pizza_id = ?');
    $stmt->execute([$id]);

    $stmt = $pdo->prepare('DELETE FROM pizza WHERE id = ?');
    $stmt->execute([$id]);
}

$_SESSION['message'] = 'Pizza "' . $name . '" supprimée avec succès !';
$_SESSION['type'] = 'danger';
header('Location: ../index.php');
exit;