<?php
session_start();

// SESSION GUARD, SO ONLY LOGGED IN USERS CAN ACCESS THE DASHBOARD
if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    die();
}

// LOADS THE FOLLOWING CLASSES
require_once __DIR__ . '/../../models/task.php';
require_once __DIR__ . '/../../controllers/task.php';
require_once __DIR__ . '/../../models/folder.php';
require_once __DIR__ . '/../../controllers/folder.php';
require_once __DIR__ . '/../../public/database.config.php';

// INSTANTIATE CONTROLLERS FOR MANAGING TASKS AND FOLDERS RESPECTIVELY
$controller = new TaskController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME, $DB_PORT);
$folderController = new FolderController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME, $DB_PORT);

$user_id = $_SESSION['user_id'];

$message = "";
$errors = "";

// ADD TASK FUNCTION
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add"])) {
    $title = $_POST["title"] ?? "";
    $description = $_POST["description"] ?? "";
    $folder_id = $_POST["folder_id"] ?? null;
    $due_date = $_POST["due_date"] ?? null;
    if (!empty($title)) {
        $controller->add($user_id, $title, $description, $folder_id ?: null, $due_date ?: null);
        header("Location: index.php?added=1");
        die();
    } else {
        $errors = "Title cannot be empty.";
    }
}

if (isset($_GET['added'])) {
    // MESSAGE SHOWN AFTER TASK IS ADDED
    $message = "Task added.";
}

// EDIT TASK FUNCTION
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit"])) {
    $id = $_POST["task_id"];
    $title = $_POST["title"];
    $description = $_POST["description"] ?? "";
    $due_date = $_POST["due_date"] ?? null;
    $folder_id = $_POST["folder_id"] ?? null;
    $controller->edit($id, $user_id, $title, $description, $due_date ?: null, $folder_id ?: null);
    header("Location: index.php?updated=1");
    die();
}


if (isset($_GET['updated'])) {
    // SHOW MESSAGE AFTER TASK IS UPDATED
    $message = "Task updated.";
}

// DELETE TASK FUNCTION
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete"])) {
    $id = $_POST["task_id"];
    $controller->delete($id, $user_id);
    $message = "Task deleted.";
}

// MARK TASK AS COMPLETE FUNCTION
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["complete"])) {
    $id = $_POST["task_id"];
    $controller->markComplete($id, $user_id);
    $message = "Task marked as complete.";
}

// ADD FOLDER FUNCTION
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_folder"])) {
    $folder_name = $_POST["folder_name"] ?? "";
    if (!empty($folder_name)) {
        $folderController->add($user_id, $folder_name);
    }
}

// EDIT FOLDER FUNCTION
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit_folder"])) {
    $folderController->edit($_POST["folder_id"], $user_id, $_POST["folder_name"]);
    header("Location: index.php");
    die();
}

// DELETE FOLDER FUNCTION
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_folder"])) {
    $folderController->delete($_POST["folder_id"], $user_id);
    header("Location: index.php");
    die();
}

// RETRIEVES ALL FOLDERS BELONGING TO THE SPECIFIC USER
$folders = $folderController->getAll($user_id);
$active_folder = $_GET['folder'] ?? null;
$tasks = $controller->getAll($user_id, $active_folder);

// TOTALS THE NUMBER OF TASKS, COMPLETE TASKS, AND PENDING TASKS
$total = count($tasks);
$complete = count(array_filter($tasks, fn($t) => $t['status'] === 'complete'));
$pending = $total - $complete;

// RANDOM GREETING EVERY NEW SESSION
$greetings = [
    "Welcome",
    "Good day",
    "Time to work",
    "Hello there",
    "work, work",
    "Let's get working",
    "Yo",
    "Good day to do work"
];

$greeting = $greetings[array_rand($greetings)];
?>

<!-- LOGIC MUST BE ON TOP (THE BULK OF YOUR PHP CODE) -->
<!-- SEPARATE YOUR CODE -->
<!-- INTERFACE (FRONTEND) SHOULD BE ON THE BOTTOM (PHP/HTML BLOCKS) -->

<?php require '../partial/header_dashboard.php'; ?>

<div class="dashboard-layout">

    <!-- SIDEBAR CONTENT -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <img src="../../public/assets/trakkr_logo.png" alt="TrakkR Logo" class="sidebar-logo">
            <span class="brand-T">T</span><span class="brand-rakk">rakk</span><span class="brand-R">R</span>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php" class="sidebar-link <?= !$active_folder ? 'active' : '' ?>">All Tasks</a>
        </nav>
        <div class="sidebar-section-label">Folders</div>
        <nav class="sidebar-nav">
            <?php foreach ($folders as $folder): ?>
            <div class="sidebar-folder-item">
                <?php if (isset($_GET['edit_folder']) && $_GET['edit_folder'] == $folder['id']): ?>
                    <form method="POST" class="sidebar-add-folder" style="flex-direction: row; margin-top: 0;">
                        <input type="hidden" name="folder_id" value="<?= $folder['id'] ?>">
                        <input type="text" name="folder_name" value="<?= htmlspecialchars($folder['name']) ?>" required>
                        <button type="submit" name="edit_folder" class="sidebar-add-folder-btn" style="width: auto; padding: 0.2rem 0.5rem;">✓</button>
                        <a href="index.php" class="sidebar-add-folder-btn" style="width: auto; padding: 0.2rem 0.5rem; text-align:center;">✕</a>
                    </form>
                <?php else: ?>
                    <a href="?folder=<?= $folder['id'] ?>" class="sidebar-link <?= $active_folder == $folder['id'] ? 'active' : '' ?>">
                        📁 <?= htmlspecialchars($folder['name']) ?>
                    </a>
                    <div style="display:flex; gap: 0.2rem;">
                        <a href="?edit_folder=<?= $folder['id'] ?>" class="sidebar-folder-delete" style="color: #B3B9C1;">✎</a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="folder_id" value="<?= $folder['id'] ?>">
                            <button type="submit" name="delete_folder" class="sidebar-folder-delete" onclick="return confirm('Delete this folder?')">✕</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?> 
        </nav>
        <form method="POST" class="sidebar-add-folder">
            <input type="text" name="folder_name" placeholder="New folder..." required>
            <button type="submit" name="add_folder" class="sidebar-add-folder-btn">+ Add Folder</button>
        </form>
        <div class="sidebar-footer">
            <div class="sidebar-user-card">
                <span class="sidebar-user"><?= $greeting ?>,<br><strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
                <a href="/logout.php" class="btn btn-danger sidebar-logout" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="dashboard-main">
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><?= $errors ?></div>
        <?php endif; ?>

        <!-- STATISTICS -->
        <div class="stats-row">
            <div class="card stat-card">
                <div class="stat-card-number"><?= $total ?></div>
                <div class="stat-card-label">Total</div>
            </div>
            <div class="card stat-card">
                <div class="stat-card-number-pending"><?= $pending ?></div>
                <div class="stat-card-label">Pending</div>
            </div>
            <div class="card stat-card">
                <div class="stat-card-number-complete"><?= $complete ?></div>
                <div class="stat-card-label">Done</div>
            </div>
        </div>

        <!-- ADD TASK TOGGLE -->
        <!-- SHOWS UP AFTER CLICKING (+ Add Task) -->
        <?php if (isset($_GET['add'])): ?>
        <div class="card add-task-card mb-2">
            <h3 class="add-task-title">New Task</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" placeholder="Task title..." required>
                </div>
                <div class="form-group">
                    <label>Description <span class="description-label-hint">(optional)</span></label>
                    <textarea name="description" placeholder="Add a description..." class="textarea-task"></textarea>
                </div>
                <div class="form-group">
                    <label>Folder <span class="description-label-hint">(optional)</span></label>
                    <select name="folder_id">
                        <option value="">No folder</option>
                        <?php foreach ($folders as $folder): ?>
                            <option value="<?= $folder['id'] ?>" <?= $active_folder == $folder['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($folder['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Due Date <span class="description-label-hint">(optional)</span></label>
                    <input type="date" name="due_date">
                </div>
                <div class="add-task-actions">
                    <a href="index.php" class="btn btn-secondary btn-action">Cancel</a>
                    <button type="submit" name="add" class="btn btn-primary btn-action">+ Add Task</button>
                </div>
            </form>
        </div>
        <?php else: ?>
        <div class="add-task-btn mb-2">
            <a href="?add=1" class="btn btn-primary btn-action">+ Add Task</a>
        </div>
        <?php endif; ?>

        <!-- TASK TABLE -->
        <table class="table">
            <thead>
                <tr>
                    <!-- TABLE FIRST ROW VALUES -->
                    <th>Task</th>
                    <th>Status</th>
                    <th>Due Date</th>
                    <th>Date Added</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tasks)): ?>
                    <tr><td colspan="4" class="td-empty">No tasks yet. Add one above.</td></tr>
                <?php else: ?>
                    <?php foreach ($tasks as $task): ?>
                    <tr class="<?= $task['status'] === 'complete' ? 'row-complete' : 'row-pending' ?>">
                        <td>
                            <?= htmlspecialchars($task['title']) ?>
                            <?php if (!empty($task['description'])): ?>
                                <div class="task-description"><?= htmlspecialchars($task['description']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- TASK STATUS -->
                            <?php if ($task['status'] === 'complete'): ?>
                                <span class="badge-complete">✓ Complete</span>
                            <?php else: ?>
                                <span class="badge-pending">● Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="td-date">
                            <?= !empty($task['due_date']) ? date('M d, Y', strtotime($task['due_date'])) : '—' ?>
                        </td>
                            <td class="td-date"><?= date('M d, Y', strtotime($task['created_at'])) ?></td>
                            <td class="td-actions">
                            <div class="flex" style="gap: 0.5rem; align-items: center; justify-content: center;">
                                <?php if (isset($_GET['edit_id']) && $_GET['edit_id'] == $task['id']): ?>
                                    <tr class="<?= $task['status'] === 'complete' ? 'row-complete' : 'row-pending' ?>">
                                        <td>
                                            <form method="POST" class="edit-inline-form">
                                                <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                                <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" class="edit-input">
                                                <input type="date" name="due_date" value="<?= htmlspecialchars($task['due_date'] ?? '') ?>" class="edit-input">
                                                <select name="folder_id" class="edit-input">
                                                    <option value="">No folder</option>
                                                    <?php foreach ($folders as $folder): ?>
                                                        <option value="<?= $folder['id'] ?>" <?= $task['folder_id'] == $folder['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($folder['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>                                                
                                                <textarea name="description" class="edit-inline-textarea"><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
                                                <div class="edit-inline-actions">
                                                    <button type="submit" name="edit" class="btn btn-primary btn-action">Save</button>
                                                    <a href="index.php" class="btn btn-secondary btn-action">Cancel</a>
                                                </div>
                                            </form>
                                        </td>
                                        <td><?= $task['status'] === 'complete' ? '<span class="badge-complete">✓ Complete</span>' : '<span class="badge-pending">● Pending</span>' ?></td>
                                        <td class="td-date"><?= date('M d, Y', strtotime($task['created_at'])) ?></td>
                                        <td class="td-actions">
                                            <div class="flex" style="gap: 0.5rem; align-items: center; justify-content: center;">
                                                <form method="POST">
                                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                                    <button type="submit" name="delete" class="btn btn-danger btn-action" onclick="return confirm('Are you sure you want to delete this task?')">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php if ($task['status'] !== 'complete'): ?>
                                        <a href="?edit_id=<?= $task['id'] ?>" class="btn btn-secondary btn-action">Edit</a>
                                    <?php endif; ?>
                                    <form method="POST">
                                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                        <button type="submit" name="delete" class="btn btn-danger" class="btn btn-* btn-action" 
                                                onclick="return confirm('Are you sure you want to delete this task?')">Delete
                                        </button>
                                    </form>
                                    <?php if ($task['status'] !== 'complete'): ?>
                                    <form method="POST">
                                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                        <button type="submit" name="complete" class="btn btn-success btn-action" 
                                                onclick="return confirm('Mark this task as complete? This cannot be undone.')">Done
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    </main>

</div>

<?php require '../partial/footer.php'; ?>