<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('chitfund_admin');
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
?>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid px-0">
        <button class="btn btn-light d-md-none me-3 shadow-none border-0" id="sidebarToggle">
            <i class="fas fa-bars fa-lg text-primary"></i>
        </button>

        <!-- <h5 class="mb-0 text-dark fw-bold"><?= $pageTitle ?? 'Dashboard' ?></h5> -->

        <div class="ms-auto d-flex align-items-center gap-4">
            <div class="d-none d-sm-flex align-items-center gap-3">
                <div class="text-end">
                    <div class="fw-bold text-dark small"><?= $_SESSION['username'] ?? 'Admin User' ?></div>
                    <div class="text-muted small" style="font-size: 0.75rem;">
                        <?= $_SESSION['email'] ?? 'admin@example.com' ?></div>
                </div>
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 35px; height: 35px; font-size: 0.9rem;">
                    <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                </div>
            </div>

            <a href="../../logout.php" class="btn btn-light text-danger btn-sm border-0 shadow-sm"
                data-bs-toggle="tooltip" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</nav>