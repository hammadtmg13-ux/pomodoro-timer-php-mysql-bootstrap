# FocusFlow - Pomodoro Focus Timer

A Pomodoro focus timer built with HTML, CSS, JavaScript, Bootstrap, MySQL and PHP. Work in focused 25 minute sessions, take short and long breaks, and keep track of your tasks and focus history in one place.

## Features
- Pomodoro timer with Focus (25 min), Short Break (5 min) and Long Break (15 min) modes
- Start, pause and reset the timer
- Pick the task you are working on while the timer runs
- Add tasks with an estimated number of pomodoros
- Mark tasks as done, edit or delete them
- Track completed pomodoros per task
- Stats: focus sessions today, focus minutes today, tasks completed and focus streak
- Session history with task, mode and minutes, saved to MySQL

## Tech stack
- HTML and CSS
- JavaScript
- Bootstrap 5
- PHP with PDO
- MySQL

## Setup
1. Create the database and tables using `database.sql`:
```sql
mysql -u root -p < database.sql
```
2. Update the database details in `config.php` if needed.
3. Copy the project folder to your web server, for example `htdocs` for XAMPP.
4. Open `http://localhost/pomodoro-timer-php-mysql/` in your browser.

## Database
Database name: `focusflow`

Table: `tasks`
- id: INT, auto increment, primary key
- title: VARCHAR(255)
- estimated_pomodoros: INT
- completed_pomodoros: INT
- status: ENUM todo, done
- created_at: TIMESTAMP, default current timestamp

Table: `sessions`
- id: INT, auto increment, primary key
- task_id: INT, can be empty
- mode: ENUM focus, short_break, long_break
- minutes: INT
- completed_at: TIMESTAMP, default current timestamp

## Project structure
- `index.php` - main page, timer, stats, tasks and session history
- `config.php` - MySQL connection with PDO
- `add_task.php` - handles new tasks
- `edit_task.php` - handles editing a task
- `delete_task.php` - deletes a task
- `complete_task.php` - marks a task as done
- `log_session.php` - saves a finished timer session to the database
- `database.sql` - database and table setup with sample tasks and sessions
- `assets/css/style.css` - custom styles
- `assets/js/script.js` - timer logic and session logging

## Author
Built by Hammad Khan
