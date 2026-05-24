<?php
//Mimics the table "tasks" in the database
class Task {
    //PROPERTIES
    public $id = "";
    public $user_id = "";
    public $title = "";
    public $status = "pending";
    public $description = "";
    public $created_at = "";

    function __construct($user_id, $title, $status = "pending", $id = "", $created_at = "")
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->title = $title;
        $this->status = $status;
        $this->created_at = $created_at;
    }
}