<?php
// BASICALLY DESTROYS THE SESSION AND TAKES YOU BACK TO THE LOGIN PAGE :>
session_start();
session_destroy();
header("Location: /index.php");
die();