<?php
session_start();

require_once __DIR__ . '/models/account.php';
require_once __DIR__ . '/controllers/account.php';
require_once __DIR__ . '/public/database.config.php';

$message = "";
$errors = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    $credentials = new Account($username, $password);
    $controller = new AccountController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME, $DB_PORT);

    // get result array
    $result = $controller->register(
        $credentials->username,
        $credentials->password
    );

    // checks if result is an array
    if (is_array($result) && $result['success']) {
        $message = $result['message'];
    } else {
        $errors = is_array($result) ? $result['message'] : "Registration failed. Try again.";
    }
}
?>

<!-- PHP code -->
<!-- register UI is also separated into two halves -->
<?php $page_title = "Trakkr. — Register"; ?>
<?php require 'views/partial/header.php'; ?>

<div class="auth-split">

    <!-- left side of page -->
    <div class="auth-left">
        <div class="auth-branding">
            <h2>Trakkr.</h2>
            <p>Stay on top of your tasks.<br>Simple. Clean. Focused.</p>
        </div>
    </div>

    <!-- right side of page -->
    <div class="auth-right">
        <div class="auth-card">

            <h1>Create an account.</h1>
            <p style="text-align:center; font-size: 0.85rem; margin-bottom: 1.5rem;">Register to get started</p>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success"><?= $message ?></div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger"><?= $errors ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Enter username" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" placeholder="Enter password" required>
                        <button type="button" class="password-toggle" onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; 
                                      this.textContent = this.previousElementSibling.type === 'password' ? 'Show' : 'Hide';">Show
                        </button>
                    </div>
                </div>

                <button type="submit" name="register">Register</button>

                <p class="mt-2" style="text-align:center; font-size: 0.85rem;">
                    Already have an account? <a href="/index.php">Login</a>
                </p>
            </form>

        </div>
    </div>

</div>

<?php require 'views/partial/footer.php'; ?>