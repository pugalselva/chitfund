<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$base = "/statice_design_chitfund_random/admin/";
?>

<div class="sidebar" id="sidebar-wrapper">
    <div class="sidebar-heading">
        <i class="fas fa-coins me-2 text-primary"></i> Chit Fund
    </div>
    <div class="list-group list-group-flush mt-3">
        <a href="<?= $base ?>dashboard.php"
            class="list-group-item list-group-item-action <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a>

        <a href="<?= $base ?>chit-groups/index.php"
            class="list-group-item list-group-item-action <?= $currentPage == 'index.php' && strpos($_SERVER['REQUEST_URI'], 'chit-groups') ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Chit Groups
        </a>

        <a href="<?= $base ?>members/index.php"
            class="list-group-item list-group-item-action <?= $currentPage == 'index.php' && strpos($_SERVER['REQUEST_URI'], 'members') ? 'active' : '' ?>">
            <i class="fas fa-user-friends"></i> Members
        </a>

        <a href="<?= $base ?>auctions/index.php"
            class="list-group-item list-group-item-action <?= $currentPage == 'index.php' && strpos($_SERVER['REQUEST_URI'], 'auctions') ? 'active' : '' ?>">
            <i class="fas fa-gavel"></i> Auctions
        </a>

        <a href="<?= $base ?>payments/index.php"
            class="list-group-item list-group-item-action <?= $currentPage == 'index.php' && strpos($_SERVER['REQUEST_URI'], 'payments') ? 'active' : '' ?>">
            <i class="fas fa-wallet"></i> Payments
        </a>

        <a href="<?= $base ?>reports/index.php"
            class="list-group-item list-group-item-action <?= $currentPage == 'index.php' && strpos($_SERVER['REQUEST_URI'], 'reports') ? 'active' : '' ?>">
            <i class="fas fa-file-invoice-dollar"></i> Reports
        </a>

        <a href="<?= $base ?>settings/index.php"
            class="list-group-item list-group-item-action <?= $currentPage == 'index.php' && strpos($_SERVER['REQUEST_URI'], 'settings') ? 'active' : '' ?>">
            <i class="fas fa-cog"></i> Settings
        </a>
    </div>
</div>