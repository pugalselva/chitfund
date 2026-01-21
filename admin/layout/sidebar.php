<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$base = "/statice_design_chitfund_random/admin/";
?>

<div class="sidebar">
    <h3 class="logo">Chit Fund</h3>

    <a href="<?= $base ?>dashboard.php"
       class="<?= $currentPage=='dashboard.php'?'active':'' ?>">
        📊 Dashboard
    </a>

    <a href="<?= $base ?>chit-groups/index.php"
       class="<?= $currentPage=='index.php' && strpos($_SERVER['REQUEST_URI'],'chit-groups')?'active':'' ?>">
        👥 Chit Groups
    </a>

    <a href="<?= $base ?>members/index.php"
       class="<?= $currentPage=='index.php' && strpos($_SERVER['REQUEST_URI'],'members')?'active':'' ?>">
        🙍 Members
    </a>

    <a href="<?= $base ?>auctions/index.php"
       class="<?= $currentPage=='index.php' && strpos($_SERVER['REQUEST_URI'],'auctions')?'active':'' ?>">
        🔨 Auctions
    </a>

    <a href="<?= $base ?>payments/index.php"
       class="<?= $currentPage=='index.php' && strpos($_SERVER['REQUEST_URI'],'payments')?'active':'' ?>">
        💰 Payments
    </a>

    <a href="<?= $base ?>reports/index.php"
       class="<?= $currentPage=='index.php' && strpos($_SERVER['REQUEST_URI'],'reports')?'active':'' ?>">
        📄 Reports
    </a>

    <a href="<?= $base ?>settings/index.php"
       class="<?= $currentPage=='index.php' && strpos($_SERVER['REQUEST_URI'],'settings')?'active':'' ?>">
        ⚙ Settings
    </a>
   <!-- <a href="<?= $base ?>logout.php"
   class="<?= strpos($_SERVER['REQUEST_URI'], 'logout') ? 'active' : '' ?>">
   🚪 Logout
</a> -->
</div>
