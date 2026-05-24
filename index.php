<?php
session_start();

// LOAD FOLLOWING CLASSES
require_once __DIR__ . '/models/account.php';
require_once __DIR__ . '/controllers/account.php';
require_once __DIR__ . '/public/database.config.php';

$error = "";

// HANDLES THE LOGIN PROCESS
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {
    // SAFELY RETRIEVES THE username AND password FROM THE SUBMITTED FORM DATA
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    // DATA OBJECT FOR LOGIN DETAILS AND CONTROLLER FOR AUTH LOGIC
    $credentials = new Account($username, $password);
    $controller = new AccountController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME, $DB_PORT);

    // VERIFIES THE CREDENTIALS, IF IT EXISTS
    $result = $controller->login(
        $credentials->username,
        $credentials->password
    );

    // CHECKS IF LOGIN IS VALID, REDIRECTS TO THE DASHBOARD
    // ELSE, IT SENDS AN INVALID USERNAME OF PASSWORD MESSAGE
    if ($result) {
        header("Location: /views/dashboard/index.php");
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

    <!-- LEFT SIDE OF LOGIN PAGE: THE TEXT -->
    <div class="auth-left">
        <div class="auth-branding">
            <div class="sidebar-brand">
                <img src="public/assets/trakkr_logo.png" alt="TrakkR Logo" class="sidebar-logo">
                <span class="brand-T">T</span><span class="brand-rakk">rakk</span><span class="brand-R">R</span>
            </div>
            <p>Stay on top of your tasks.<br>Simple. Clean. Focused.</p>
        </div>
    </div>

    <!-- RIGHT SIDE OF LOGIN PAGE: FORM -->
    <div class="auth-right">
        <div class="auth-card">

            <h1>Welcome.</h1>
            <p style="text-align:center; font-size: 0.85rem; margin-bottom: 1.5rem;">Log in to your account</p>

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

                <button type="submit" name="login">Login</button>

                <p class="mt-2" style="text-align:center; font-size: 0.85rem;">
                    Don't have an account? <a href="/register.php">Register</a>
                </p>
            </form>

        </div>
    </div>

</div>

<?php require 'views/partial/footer.php'; ?>