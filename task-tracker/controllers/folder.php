<?php
// The Creating, Reading, Updating, and Deleting of folders
class FolderController {
    // PROPERTIES
    private $conn;

    function __construct($server_name, $username, $password, $db_name, $port = 3306)
    {
        //CONNECTS TO THE RAILWAY DATABASE
        $this->conn = new PDO(
            "mysql:host=$server_name;port=$port;dbname=$db_name;charset=utf8",
            $username,
            $password
        );
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    function getAll($user_id) {
        // RETRIEVES ALL FOLDERS IN THE DATABASE THAT THE USER HAS
        $stmt = $this->conn->prepare("SELECT * FROM folders WHERE user_id = ? ORDER BY name ASC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function add($user_id, $name) {
        // FOLDER CREATION LOGIC
        $stmt = $this->conn->prepare("INSERT INTO folders (user_id, name) VALUES (?, ?)");
        return $stmt->execute([$user_id, $name]);
    }

    function edit($id, $user_id, $name) {
        // FOLDER EDIT LOGIC
        $stmt = $this->conn->prepare("UPDATE folders SET name = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$name, $id, $user_id]);
    }

    function delete($id, $user_id) {
        // FOLDER DELETION LOGIC
        $stmt = $this->conn->prepare("DELETE FROM folders WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $user_id]);
    }
}