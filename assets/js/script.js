// FocusFlow - Pomodoro timer logic

let totalSeconds = 25 * 60;
let remaining = totalSeconds;
let timerId = null;
let currentMode = 'focus';
let currentMinutes = 25;

const display = document.getElementById('timerDisplay');
const modeLabel = document.getElementById('timerModeLabel');
const startBtn = document.getElementById('startBtn');
const resetBtn = document.getElementById('resetBtn');
const message = document.getElementById('timerMessage');
const activeTask = document.getElementById('activeTask');

const modeNames = {
    focus: 'Focus session',
    short_break: 'Short break',
    long_break: 'Long break'
};

function formatTime(seconds) {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
}

function render() {
    display.textContent = formatTime(remaining);
}

function setMode(mode, minutes) {
    stopTimer();
    currentMode = mode;
    currentMinutes = minutes;
    totalSeconds = minutes * 60;
    remaining = totalSeconds;
    modeLabel.textContent = modeNames[mode];
    message.textContent = mode === 'focus'
        ? 'Pick a task, press start, and stay focused for 25 minutes.'
        : 'Take a break. You earned it.';
    document.querySelectorAll('.mode-btn').forEach(function (btn) {
        btn.classList.toggle('active', btn.dataset.mode === mode);
    });
    render();
}

function stopTimer() {
    if (timerId !== null) {
        clearInterval(timerId);
        timerId = null;
    }
    startBtn.textContent = 'Start';
}

function logSession() {
    fetch('log_session.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'mode=' + encodeURIComponent(currentMode)
            + '&minutes=' + currentMinutes
            + '&task_id=' + encodeURIComponent(activeTask.value)
    }).catch(function () {
        // Session logging is best effort in the preview
    });
}

startBtn.addEventListener('click', function () {
    if (timerId !== null) {
        stopTimer();
        message.textContent = 'Paused. Press start when you are ready to continue.';
        return;
    }
    startBtn.textContent = 'Pause';
    message.textContent = currentMode === 'focus' ? 'Stay focused. You can do this.' : 'Relax and recharge.';
    timerId = setInterval(function () {
        remaining -= 1;
        if (remaining <= 0) {
            stopTimer();
            logSession();
            remaining = 0;
            render();
            message.textContent = currentMode === 'focus'
                ? 'Focus session complete. Nice work, take a break.'
                : 'Break over. Ready for the next focus session?';
            return;
        }
        render();
    }, 1000);
});

resetBtn.addEventListener('click', function () {
    stopTimer();
    remaining = totalSeconds;
    render();
    message.textContent = 'Timer reset. Press start when you are ready.';
});

document.querySelectorAll('.mode-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        setMode(btn.dataset.mode, parseInt(btn.dataset.minutes, 10));
    });
});

function confirmDelete() {
    return confirm('Delete this task?');
}

render();
