<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");


$host = "localhost";
$user = "iufioyem_quizzing";
$pass = "z-u3v_Np8=4G";
$db = "iufioyem_quizzing";

$conn = mysqli_connect($host, $user, $pass, $db);

if (mysqli_connect_error()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
