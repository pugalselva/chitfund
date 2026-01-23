<?php
date_default_timezone_set('Asia/Kolkata');
$conn = new mysqli("localhost", "root", "MYSQL70@pug", "chit_fund_project_random");
if ($conn->connect_error) {
    die("Database Connection Failed");
}
// var_dump($conn);
// exit;
?>