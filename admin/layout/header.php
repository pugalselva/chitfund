<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
?>

<div class="topbar">
    <!-- <h3><?= $pageTitle ?? 'Dashboard' ?></h3> -->

    <div>
        <b><?= $_SESSION['username'] ?? 'Admin User' ?></b><br>
        <?= $_SESSION['email'] ?? 'admin@example.com' ?>

        <a href="../../logout.php" class="btn btn-danger">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</div>
