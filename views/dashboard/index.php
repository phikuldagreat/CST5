<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /finalexam/index.php");
    die();
}

require_once __DIR__ . '/../../models/task.php';
require_once __DIR__ . '/../../controllers/task.php';
require_once __DIR__ . '/../../public/database.config.php';

$controller = new TaskController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
$user_id = $_SESSION['user_id'];
$message = "";
$errors = "";

//add function
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add"])) {
    $title = $_POST["title"] ?? "";
    if (!empty($title)) {
        $controller->add($user_id, $title);
        $message = "Task added.";
    } else {
        $errors = "Title cannot be empty.";
    }
}

//edit function
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit"])) {
    $id = $_POST["task_id"];
    $title = $_POST["title"];
    $controller->edit($id, $user_id, $title);
    $message = "Task updated.";
}

//delete function
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete"])) {
    $id = $_POST["task_id"];
    $controller->delete($id, $user_id);
    $message = "Task deleted.";
}

//mark complete function
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["complete"])) {
    $id = $_POST["task_id"];
    $controller->markComplete($id, $user_id);
    $message = "Task marked as complete.";
}

$tasks = $controller->getAll($user_id);
?>

<!-- LOGIC MUST BE ON TOP (THE BULK OF YOUR PHP CODE) -->
<!-- SEPARATE YOUR CODE -->
<!-- INTERFACE (FRONTEND) SHOULD BE ON THE BOTTOM (PHP/HTML BLOCKS) -->

<?php require '../partial/header_dashboard.php'; ?>

<div class="dashboard-layout">

    <!-- sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">TrackIT!</div>
        <nav class="sidebar-nav">
            <a href="index.php" class="sidebar-link active">Tasks</a>
        </nav>
        <div class="sidebar-footer">
            <span style="font-size: 0.8rem; color: #4a6a7a;">Logged in as<br><strong style="color: #7eb8d4;"><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
            <a href="/finalexam/logout.php" class="btn btn-danger" style="width: auto; padding: 0.4rem 1rem; font-size: 0.8rem; margin-top: 1rem; display: block; text-align: center;">Logout</a>
        </div>
    </aside>

    <!-- main content -->
    <main class="dashboard-main">

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><?= $errors ?></div>
        <?php endif; ?>

        <!-- toolbar -->
        <div class="card mb-2" style="padding: 0.75rem 1rem;">
            <form method="POST" class="flex" style="align-items: center; gap: 0.75rem;">
                <input type="text" name="title" placeholder="New task title..." required style="flex: 1;">
                <button type="submit" name="add" class="btn btn-primary" style="width: auto;">+ Add Task</button>
            </form>
        </div>

        <!-- task table -->
        <table class="table">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Date Added</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tasks)): ?>
                    <tr><td colspan="4" style="color: #4a6a7a; text-align:center; padding: 2rem;">No tasks yet. Add one above.</td></tr>
                <?php else: ?>
                    <?php foreach ($tasks as $task): ?>
                    <tr class="<?= $task['status'] === 'complete' ? 'row-complete' : 'row-pending' ?>">
                        <td><?= htmlspecialchars($task['title']) ?></td>
                        <td>
                            <?php if ($task['status'] === 'complete'): ?>
                                <span style="background: #1a3a2a; color: #4ac880; border-radius: 20px; padding: 0.2rem 0.75rem; font-size: 0.8rem; font-weight: 500;">✓ Complete</span>
                            <?php else: ?>
                                <span style="background: #3a3010; color: #f0d080; border-radius: 20px; padding: 0.2rem 0.75rem; font-size: 0.8rem; font-weight: 500;">● Pending</span>
                            <?php endif; ?>
                        </td>
                        <td style="color: #4a6a7a; font-size: 0.85rem;"><?= date('M d, Y', strtotime($task['created_at'])) ?></td>
                        <td style="text-align: center;">
                            <div class="flex" style="gap: 0.5rem; align-items: center; justify-content: center;">

                                <?php if (isset($_GET['edit_id']) && $_GET['edit_id'] == $task['id']): ?>
                                    <form method="POST" class="flex" style="gap: 0.4rem; align-items: center;">
                                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                        <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" style="width: 180px; padding: 0.3rem 0.5rem; font-size: 0.85rem;">
                                        <button type="submit" name="edit" class="btn btn-primary" style="width: auto; padding: 0.3rem 0.6rem; font-size: 0.8rem;">Save</button>
                                        <a href="index.php" class="btn btn-secondary" style="width: auto; padding: 0.3rem 0.6rem; font-size: 0.8rem;">Cancel</a>
                                    </form>
                                <?php else: ?>
                                    <?php if ($task['status'] !== 'complete'): ?>
                                        <a href="?edit_id=<?= $task['id'] ?>" class="btn btn-secondary" style="width: auto; padding: 0.3rem 0.6rem; font-size: 0.8rem;">Edit</a>
                                    <?php endif; ?>
                                    <form method="POST">
                                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                        <button type="submit" name="delete" class="btn btn-danger" style="width: auto; padding: 0.3rem 0.6rem; font-size: 0.8rem;" 
                                                onclick="return confirm('Are you sure you want to delete this task?')">Delete
                                        </button>
                                    </form>
                                    <?php if ($task['status'] !== 'complete'): ?>
                                    <form method="POST">
                                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                        <button type="submit" name="complete" class="btn btn-success" style="width: auto; padding: 0.3rem 0.6rem; font-size: 0.8rem;">Done</button>
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