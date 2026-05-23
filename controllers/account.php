<?php
// This will contain all the processes/functions
// that affect the Account model
class AccountController {
    // Properties
    private $conn;

    function __construct($server_name, $username, $password, $db_name)
    {
        //connect to the SQL server
        $this->conn = new PDO(
            "mysql:host=$server_name;port=3306;dbname=$db_name;charset=utf8",
            $username,
            $password
        );
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    function register($username, $password) {
        // account creation logic
        // check if username already exists
        $check = $this->conn->prepare("SELECT id FROM accounts WHERE username = ?");
        $check->execute([$username]);

        if ($check->rowCount() > 0) {
            return false; // username taken
        }

        // hash the password before storing
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("INSERT INTO accounts (username, password) VALUES (?, ?)");
        return $stmt->execute([$username, $hashed]);
    }

    function login($username, $password) {
        // account reading logic
        $stmt = $this->conn->prepare("SELECT id, username, password FROM accounts WHERE username = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false; // username not found
        }

        if (!password_verify($password, $row['password'])) {
            return false; // if password is wrong
        }

        // store in session
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];

        return true;
    }

    function update($id, $username, $password) {
        // account updating logic
    }

    function delete($id, $username, $password) {
        // account deletion logic
    }
}