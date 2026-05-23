<?php
//The Creating, Reading, Updating, and Deleting of tasks
class TaskController {
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

    function getAll($user_id, $folder_id = null) {
        if ($folder_id) {
            $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE user_id = ? AND folder_id = ? ORDER BY created_at DESC");
            $stmt->execute([$user_id, $folder_id]);
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$user_id]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function add($user_id, $title, $description = "", $folder_id = null) {
        $stmt = $this->conn->prepare("INSERT INTO tasks (user_id, title, description, folder_id, status) VALUES (?, ?, ?, ?, 'pending')");
        return $stmt->execute([$user_id, $title, $description, $folder_id]);
    }

    function edit($id, $user_id, $title, $description = "") {
        $stmt = $this->conn->prepare("UPDATE tasks SET title = ?, description = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$title, $description, $id, $user_id]);
    }

    function delete($id, $user_id) {
        //delete existing tasks
        $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $user_id]);
    }

    function markComplete($id, $user_id) {
        //after adding, can mark complete if the task is done
        $stmt = $this->conn->prepare("UPDATE tasks SET status = 'complete' WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $user_id]);
    }
}