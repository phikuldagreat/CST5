<?php
// This will contain all the processes/functions
// that affect the Account model
class AccountController {
    // PROPERTIES
    private $conn;

    function __construct($server_name, $username, $password, $db_name, $port = 3306)
    {
        // CONNECTS TO RAILWAY DATABASE
        $this->conn = new PDO(
            "mysql:host=$server_name;port=$port;dbname=$db_name;charset=utf8", 
            $username,
            $password
        );
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    function register($username, $password) {
        // ACCOUNT CREATION LOGIC
        $check = $this->conn->prepare("SELECT id FROM accounts WHERE username = ?");
        $check->execute([$username]);

        // CHECKS IF USERNAME ALREADY EXISTS
        if ($check->rowCount() > 0) {
            return ['success' => false, 'message' => 'Username already taken.'];
        }

        // VALIDATE PASSWORD LENGTH
        if (strlen($password) < 8) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters.'];
        }
        
        // HASHES THE PASSWORD BEFORE STORING IN DATABASE
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("INSERT INTO accounts (username, password) VALUES (?, ?)");
        $success = $stmt->execute([$username, $hashed]);

        // RETURNS A MESSAGE IF ACCOUNT IS SUCCESSFULLY CREATED OR NOT
        if ($success) {
            return ['success' => true, 'message' => 'Account created successfully.'];
        } else {
            return ['success' => false, 'message' => 'Failed to create account.'];
        }
    }
    
    function login($username, $password) {
        // ACCOUNT READING LOGIC
        $stmt = $this->conn->prepare("SELECT id, username, password FROM accounts WHERE username = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            // USERNAME NOT FOUND
            return false; 
        }

        if (!password_verify($password, $row['password'])) {
            // VERIFIES PASSWORD
            return false; 
        }

        // STORE IN SESSION
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        return true;
    }

    function update($id, $username, $password) {
        // ACCOUNT UPDATING LOGIC (NOT USED)
    }

    function delete($id, $username, $password) {
        // ACCOUNT DELETION LOGIC (NOT USED)
    }
}