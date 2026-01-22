<?php
session_name('chitfund_member');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header('Location: ../index.php');
    exit();
}
