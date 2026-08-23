<?php
//Mimics the table "folders" in the database
class Folder {
    //PROPERTIES
    public $id = "";
    public $user_id = "";
    public $name = "";
    public $created_at = "";

    function __construct($user_id, $name, $id = "", $created_at = "")
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->name = $name;
        $this->created_at = $created_at;
    }
}