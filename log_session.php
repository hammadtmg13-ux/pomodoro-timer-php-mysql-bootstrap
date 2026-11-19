<?php
require 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'POST only']);
    exit;
}

$mode = $_POST['mode'] ?? 'focus';
$minutes = max(1, (int)($_POST['minutes'] ?? 25));
$taskId = (int)($_POST['task_id'] ?? 0);

if (!in_array($mode, ['focus', 'short_break', 'long_break'], true)) {
    $mode = 'focus';
}

$stmt = $pdo->prepare('INSERT INTO sessions (task_id, mode, minutes) VALUES (?, ?, ?)');
$stmt->execute([$taskId > 0 ? $taskId : null, $mode, $minutes]);

if ($mode === 'focus' && $taskId > 0) {
    $stmt = $pdo->prepare('UPDATE tasks SET completed_pomodoros = completed_pomodoros + 1 WHERE id = ?');
    $stmt->execute([$taskId]);
}

echo json_encode(['ok' => true]);
