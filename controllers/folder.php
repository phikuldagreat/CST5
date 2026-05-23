<?php
class FolderController {
    private $conn;

    function __construct($server_name, $username, $password, $db_name, $port = 3306)
    {
        $this->conn = new PDO(
            "mysql:host=$server_name;port=$port;dbname=$db_name;charset=utf8",
            $username,
            $password
        );
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    function getAll($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM folders WHERE user_id = ? ORDER BY name ASC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function add($user_id, $name) {
        $stmt = $this->conn->prepare("INSERT INTO folders (user_id, name) VALUES (?, ?)");
        return $stmt->execute([$user_id, $name]);
    }

    function delete($id, $user_id) {
        $stmt = $this->conn->prepare("DELETE FROM folders WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $user_id]);
    }
}