<?php
require 'config.php';

// Today's stats
$todaySessions = (int)$pdo->query("SELECT COUNT(*) FROM sessions WHERE mode = 'focus' AND DATE(completed_at) = CURDATE()")->fetchColumn();
$todayMinutes = (int)$pdo->query("SELECT COALESCE(SUM(minutes), 0) FROM sessions WHERE mode = 'focus' AND DATE(completed_at) = CURDATE()")->fetchColumn();
$tasksDone = (int)$pdo->query("SELECT COUNT(*) FROM tasks WHERE status = 'done'")->fetchColumn();
$totalTasks = (int)$pdo->query("SELECT COUNT(*) FROM tasks")->fetchColumn();
$streak = (int)$pdo->query("SELECT COUNT(DISTINCT DATE(completed_at)) FROM sessions WHERE mode = 'focus' AND completed_at >= CURDATE() - INTERVAL 6 DAY")->fetchColumn();

$tasks = $pdo->query('SELECT * FROM tasks ORDER BY status ASC, created_at DESC')->fetchAll();
$history = $pdo->query("SELECT s.*, t.title FROM sessions s LEFT JOIN tasks t ON t.id = s.task_id ORDER BY s.completed_at DESC LIMIT 8")->fetchAll();

$modeLabels = ['focus' => 'Focus', 'short_break' => 'Short break', 'long_break' => 'Long break'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FocusFlow - Pomodoro Focus Timer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container py-4">
    <header class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar">HK</div>
            <div>
                <h1 class="h4 mb-0">FocusFlow</h1>
                <small class="text-muted">Pomodoro Focus Timer</small>
            </div>
        </div>
        <div class="user-chip">
            <span class="avatar avatar-sm">HK</span>
            <span>Hammad Khan</span>
        </div>
    </header>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card text-center p-3">
                <div class="stat-number"><?= $todaySessions ?></div>
                <div class="stat-label">Focus sessions today</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card text-center p-3">
                <div class="stat-number"><?= $todayMinutes ?></div>
                <div class="stat-label">Focus minutes today</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card text-center p-3">
                <div class="stat-number"><?= $tasksDone ?> / <?= $totalTasks ?></div>
                <div class="stat-label">Tasks completed</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card text-center p-3">
                <div class="stat-number"><?= $streak ?> days</div>
                <div class="stat-label">Focus streak</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card timer-card p-4 text-center shadow-sm">
                <div class="btn-group mb-4" role="group">
                    <button type="button" class="btn btn-outline-light mode-btn active" data-mode="focus" data-minutes="25">Focus</button>
                    <button type="button" class="btn btn-outline-light mode-btn" data-mode="short_break" data-minutes="5">Short Break</button>
                    <button type="button" class="btn btn-outline-light mode-btn" data-mode="long_break" data-minutes="15">Long Break</button>
                </div>
                <div class="timer-ring mx-auto mb-3">
                    <div id="timerDisplay" class="timer-display">25:00</div>
                    <div id="timerModeLabel" class="timer-mode">Focus session</div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white-50">Working on</label>
                    <select id="activeTask" class="form-select">
                        <option value="0">No task selected</option>
                        <?php foreach ($tasks as $t): if ($t['status'] === 'todo'): ?>
                        <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['title']) ?></option>
                        <?php endif; endforeach; ?>
                    </select>
                </div>
                <div class="d-flex justify-content-center gap-2">
                    <button id="startBtn" class="btn btn-light btn-lg px-4">Start</button>
                    <button id="resetBtn" class="btn btn-outline-light btn-lg">Reset</button>
                </div>
                <p id="timerMessage" class="mt-3 mb-0 text-white-50 small">Pick a task, press start, and stay focused for 25 minutes.</p>
            </div>

            <div class="card p-4 mt-4 shadow-sm">
                <h2 class="h5 mb-3">Session history</h2>
                <?php if (empty($history)): ?>
                    <p class="text-muted mb-0">No sessions yet. Start your first focus session.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($history as $s): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><?= htmlspecialchars($s['title'] ?? 'General focus') ?> <small class="text-muted">(<?= $modeLabels[$s['mode']] ?>)</small></span>
                            <span class="badge bg-primary rounded-pill"><?= (int)$s['minutes'] ?> min</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card p-4 shadow-sm">
                <h2 class="h5 mb-3">Today's tasks</h2>
                <form action="add_task.php" method="post" class="row g-2 mb-4">
                    <div class="col-md-8">
                        <input type="text" name="title" class="form-control" placeholder="Add a task to focus on..." required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="estimated_pomodoros" class="form-control" min="1" max="12" value="1" title="Estimated pomodoros">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Add</button>
                    </div>
                </form>
                <?php if (empty($tasks)): ?>
                    <p class="text-muted mb-0">No tasks yet. Add your first one above.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($tasks as $t): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="<?= $t['status'] === 'done' ? 'text-decoration-line-through text-muted' : '' ?>"><?= htmlspecialchars($t['title']) ?></span>
                                <small class="text-muted d-block"><?= (int)$t['completed_pomodoros'] ?> / <?= (int)$t['estimated_pomodoros'] ?> pomodoros</small>
                            </div>
                            <div class="d-flex gap-1">
                                <?php if ($t['status'] === 'todo'): ?>
                                <a href="complete_task.php?id=<?= (int)$t['id'] ?>" class="btn btn-sm btn-success">Done</a>
                                <a href="edit_task.php?id=<?= (int)$t['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <?php endif; ?>
                                <a href="delete_task.php?id=<?= (int)$t['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirmDelete()">Delete</a>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="text-center text-muted mt-4 small">Built by Hammad Khan with HTML, CSS, JavaScript, Bootstrap, MySQL and PHP</footer>
</div>
<script src="assets/js/script.js"></script>
</body>
</html>
