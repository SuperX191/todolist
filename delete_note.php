<?php
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

$id = $_GET['id'];
$pdo->prepare("DELETE FROM notes WHERE id = ?")->execute([$id]);

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>