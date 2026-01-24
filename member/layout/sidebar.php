<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$base = "/statice_design_chitfund_random/member/"; // Adjust if necessary based on your folder structure

// Helper to check active state
function isActive($page, $current)
{
    return $page === $current ? 'active' : '';
}
?>

<div class="border-end bg-white" id="sidebar-wrapper">
    <div class="sidebar-heading border-bottom bg-light d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <i class="fas fa-coins fa-2x me-2 text-primary"></i>
            <span class="fw-bold text-dark">ChitFund</span>
        </div>
        <button class="btn btn-sm btn-light d-md-none" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="list-group list-group-flush pt-2">
        <a href="<?= $base ?>dashboard.php"
            class="list-group-item list-group-item-action <?= isActive('dashboard.php', $currentPage) ?>">
            <i class="fas fa-th-large"></i> Dashboard
        </a>

        <a href="<?= $base ?>my-chits.php"
            class="list-group-item list-group-item-action <?= isActive('my-chits.php', $currentPage) ?>">
            <i class="fas fa-box-open"></i> My Chits
        </a>

        <a href="<?= $base ?>auctions_view.php"
            class="list-group-item list-group-item-action <?= isActive('auctions_view.php', $currentPage) ?>">
            <i class="fas fa-gavel"></i> Auctions View
        </a>

        <!-- Live Auction (Commented out as per original) -->
        <!--
        <a href="<?= $base ?>live-auction.php" class="list-group-item list-group-item-action <?= isActive('live-auction.php', $currentPage) ?>">
            <i class="fas fa-stopwatch"></i> Live Auction
        </a>
        -->

        <a href="<?= $base ?>auction-history.php"
            class="list-group-item list-group-item-action <?= isActive('auction-history.php', $currentPage) ?>">
            <i class="fas fa-history"></i> Auction History
        </a>

        <a href="<?= $base ?>payment-history.php"
            class="list-group-item list-group-item-action <?= isActive('payment-history.php', $currentPage) ?>">
            <i class="fas fa-file-invoice-dollar"></i> Payment Records
        </a>

        <a href="<?= $base ?>profile.php"
            class="list-group-item list-group-item-action <?= isActive('profile.php', $currentPage) ?>">
            <i class="fas fa-user-circle"></i> Profile
        </a>

        <a href="../logout.php" class="list-group-item list-group-item-action text-danger mt-3 border-top">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>