<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $estimated = max(1, (int)($_POST['estimated_pomodoros'] ?? 1));

    if ($title !== '') {
        $stmt = $pdo->prepare('INSERT INTO tasks (title, estimated_pomodoros) VALUES (?, ?)');
        $stmt->execute([$title, $estimated]);
    }
}

header('Location: index.php');
exit;
