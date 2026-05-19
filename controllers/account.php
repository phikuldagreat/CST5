<?php
// This will contain all the processes/functions
// that affect the Account model
class AccountController {
    // Properties
    private $conn;

    function __construct($server_name, $username, $password, $db_name)
    {
        //connect to the SQL server
        $this->conn = new mysqli(
            $server_name,
            $username,
            $password,
            $db_name
        );
    }

    function register($username, $password) {
        // account creation logic
        // check if username already exists
        $check = $this->conn->prepare("SELECT id FROM accounts WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            return false; // username taken
        }

        // hash the password before storing
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("INSERT INTO accounts (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed);

        return $stmt->execute();
    }

    function login($username, $password) {
        // account reading logic
        $stmt = $this->conn->prepare("SELECT id, username, password FROM accounts WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return false; // username not found
        }

        $row = $result->fetch_assoc();

        if (!password_verify($password, $row['password'])) {
            // if password is wrong
            return false; 
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