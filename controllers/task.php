<?php
//The Creating, Reading, Updating, and Deleting of tasks
class TaskController {
    private $conn;

    function __construct($server_name, $username, $password, $db_name)
    {
        //connects to the SQL server
        $this->conn = new mysqli($server_name, $username, $password, $db_name);
    }

    function getAll($user_id) {
        //collects all tasks in the database
        //based on who's logged in
        $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function add($user_id, $title) {
        //add new tasks
        $stmt = $this->conn->prepare("INSERT INTO tasks (user_id, title, status) VALUES (?, ?, 'pending')");
        $stmt->bind_param("is", $user_id, $title);
        return $stmt->execute();
    }

    function edit($id, $user_id, $title) {
        //change task details
        $stmt = $this->conn->prepare("UPDATE tasks SET title = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $title, $id, $user_id);
        return $stmt->execute();
    }

    function delete($id, $user_id) {
        //delete existing tasks
        $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        return $stmt->execute();
    }

    function markComplete($id, $user_id) {
        //after adding, can mark complete if the task is donef
        $stmt = $this->conn->prepare("UPDATE tasks SET status = 'complete' WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        return $stmt->execute();
    }
}