-- FocusFlow - Pomodoro Focus Timer
-- Database: focusflow

CREATE DATABASE IF NOT EXISTS focusflow;
USE focusflow;

CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    estimated_pomodoros INT DEFAULT 1,
    completed_pomodoros INT DEFAULT 0,
    status ENUM('todo', 'done') DEFAULT 'todo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NULL,
    mode ENUM('focus', 'short_break', 'long_break') DEFAULT 'focus',
    minutes INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE SET NULL
);

-- Sample tasks
INSERT INTO tasks (title, estimated_pomodoros, completed_pomodoros, status) VALUES
('Finish PHP assignment', 4, 3, 'todo'),
('Read Clean Code chapter 5', 2, 2, 'done'),
('Practice JavaScript arrays', 3, 1, 'todo'),
('Review MySQL joins', 2, 0, 'todo'),
('Prepare LinkedIn post', 1, 1, 'done');

-- Sample focus sessions from today
INSERT INTO sessions (task_id, mode, minutes) VALUES
(1, 'focus', 25),
(1, 'focus', 25),
(1, 'focus', 25),
(2, 'focus', 25),
(2, 'focus', 25),
(3, 'focus', 25),
(5, 'focus', 25),
(1, 'short_break', 5);
