<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$base = "/statice_design_chitfund/member/";
?>

<div class="sidebar">
    <h3 class="logo">Chit Fund</h3>

    <a href="<?= $base ?>dashboard.php"
       class="<?= $currentPage=='dashboard.php'?'active':'' ?>">
        📊 Dashboard
    </a>

    <a href="<?= $base ?>my-chits.php"
       class="<?= $currentPage=='my-chits.php'?'active':'' ?>">
        📦 My-Chit
    </a>
    <a href="<?= $base ?>auctions_view.php"
       class="<?= $currentPage=='auctions_view.php'?'active':'' ?>">
        🔥 Auction view
    </a>
    <!-- <a href="<?= $base ?>live-auction.php"
       class="<?= $currentPage=='live-auction.php'?'active':'' ?>">
        🔥 Live Auction
    </a> -->

    <a href="<?= $base ?>auction-history.php"
       class="<?= $currentPage=='auction-history.php'?'active':'' ?>">
        🔨 Auctions History
    </a>

    <a href="<?= $base ?>payment-history.php"
       class="<?= $currentPage=='payment-history.php'?'active':'' ?>">
        💰 Payments Records
    </a>

    <a href="<?= $base ?>profile.php"
       class="<?= $currentPage=='profile.php'?'active':'' ?>">
        📄 Profile
    </a>

    <!-- <a href="<?= $base ?>settings/index.php"
       class="<?= $currentPage=='index.php' && strpos($_SERVER['REQUEST_URI'],'settings')?'active':'' ?>">
        ⚙ Settings
    </a> -->
</div>

