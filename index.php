<?php
// Define the file to store tasks
$tasksFile = "tasks.txt";

// Function to load tasks from the file
function loadTasks($tasksFile) {
    if (!file_exists($tasksFile)) {
        return [];
    }

    $tasks = file($tasksFile, FILE_IGNORE_NEW_LINES);
    return array_map(function ($line) {
        [$task, $status] = explode('|', $line);
        return ['task' => $task, 'status' => $status];
    }, $tasks);
}

// Function to save tasks to the file
function saveTasks($tasksFile, $tasks) {
    $data = array_map(function ($task) {
        return "{$task['task']}|{$task['status']}";
    }, $tasks);
    file_put_contents($tasksFile, implode(PHP_EOL, $data));
}

// Load tasks
$tasks = loadTasks($tasksFile);

// Handle Add Task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_task'])) {
    $newTask = trim($_POST['new_task']);
    if ($newTask) {
        $tasks[] = ['task' => $newTask, 'status' => 'pending'];
        saveTasks($tasksFile, $tasks);
    }
}

// Handle Mark as Complete
if (isset($_GET['complete'])) {
    $taskIndex = $_GET['complete'];
    if (isset($tasks[$taskIndex])) {
        $tasks[$taskIndex]['status'] = 'complete';
        saveTasks($tasksFile, $tasks);
    }
}

// Handle Delete Task
if (isset($_GET['delete'])) {
    $taskIndex = $_GET['delete'];
    if (isset($tasks[$taskIndex])) {
        unset($tasks[$taskIndex]);
        saveTasks($tasksFile, $tasks);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>PHP To-Do List</h1>

    <!-- Add Task Form -->
    <form action="index.php" method="POST">
        <input type="text" name="new_task" placeholder="Enter a new task" required>
        <button type="submit">Add Task</button>
    </form>

    <!-- Task List -->
    <ul>
        <?php foreach ($tasks as $index => $task): ?>
            <li>
                <span style="text-decoration: <?php echo $task['status'] === 'complete' ? 'line-through' : 'none'; ?>">
                    <?php echo htmlspecialchars($task['task']); ?>
                </span>
                <?php if ($task['status'] === 'pending'): ?>
                    <a href="index.php?complete=<?php echo $index; ?>">Mark as Complete</a>
                <?php endif; ?>
                <a href="index.php?delete=<?php echo $index; ?>">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
