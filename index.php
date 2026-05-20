<?php
session_start();

require_once __DIR__ . '/models/account.php';
require_once __DIR__ . '/controllers/account.php';
require_once __DIR__ . '/public/database.config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    $credentials = new Account($username, $password);
    $controller = new AccountController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);

    $result = $controller->login(
        $credentials->username,
        $credentials->password
    );

    if ($result) {
        header("Location: /finalexam/views/dashboard/index.php");
        die();
    } else {
    $errors = "Invalid username or password.";
    }
}
?>

<!-- PHP code -->
<!-- login page is separated into two halves -->
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

            <h1>Welcome back.</h1>
            <p style="text-align:center; font-size: 0.85rem; margin-bottom: 1.5rem;">Sign in to your account</p>

            <?php if (!empty($message)): ?>
                <p style="color: #4ac880;"><?= $message ?></p>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <p style="color: #e07070;"><?= $errors ?></p>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Enter your username" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" name="login">Login</button>

                <p class="mt-2" style="text-align:center; font-size: 0.85rem;">
                    Don't have an account? <a href="/finalexam/register.php">Register</a>
                </p>
            </form>

        </div>
    </div>

</div>

<?php require 'views/partial/footer.php'; ?>