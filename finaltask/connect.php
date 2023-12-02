<?php
$host = 'localhost:3308';
$username = 'root';
$database = 'finaldb';

$con = mysqli_connect($host, $username, '', $database);

if(mysqli_connect_errno()){
    die("Failed to connect to database: ". mysqli_connect_error());
}



?>