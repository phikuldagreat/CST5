<?php
//Basically destroys the session and takes you back to the login page :>>
session_start();
session_destroy();
header("Location: /finalexam/index.php");
die();