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
    $controller = new AccountController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);

    $result = $controller->register(
        $credentials->username,
        $credentials->password
    );

    if ($result) {
        $message = "Account created! You can now login.";
    } else {
        $errors = "Registration failed. Try again.";
    }
}
?>

<!-- PHP code -->
<!-- register UI is also separated into two halves -->
<?php require 'views/partial/header.php'; ?>

<div class="auth-split">

    <!-- left side of page -->
    <div class="auth-left">
        <div class="auth-branding">
            <h2>TrackIT!</h2>
            <p>Stay on top of your tasks.<br>Simple. Clean. Focused.</p>
        </div>
    </div>

    <!-- right side of page -->
    <div class="auth-right">
        <div class="auth-card">

            <h1>Create account</h1>
            <p style="text-align:center; font-size: 0.85rem; margin-bottom: 1.5rem;">Register to get started</p>

            <?php if (!empty($message)): ?>
                <p style="color: #4ac880;"><?= $message ?></p>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <p style="color: #e07070;"><?= $errors ?></p>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit" name="register">Register</button>

                <p class="mt-2" style="text-align:center; font-size: 0.85rem;">
                    Already have an account? <a href="/finalexam/index.php">Login</a>
                </p>
            </form>

        </div>
    </div>

</div>

<?php require 'views/partial/footer.php'; ?>