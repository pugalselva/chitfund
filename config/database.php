<?php
$conn = new mysqli("localhost", "root", "MYSQL70@pug", "chit_fund_project");
if ($conn->connect_error) {
    die("Database Connection Failed");
}
?>
