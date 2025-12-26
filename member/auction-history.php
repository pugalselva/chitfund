<!DOCTYPE html>
<html>

<head>
    <title>Auction History</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include 'layout/sidebar.php'; ?>

        <div class="main">

            <div class="topbar">
                <div>
                    <div class="page-title">Auction History</div>
                    <div class="page-subtitle">
                        View past auction results and your share of discounts
                    </div>
                </div>

                <div style="text-align:right;">
                    <b>Member User</b><br>
                    sandy@gmail.com
                </div>
            </div>

            <div class="content">

                <!-- AUCTION 1 -->
                <div class="auction-history-card">

                    <div class="auction-header">
                        <div>
                            <div class="auction-title">
                                Elite Savings Group - Month 12
                            </div>
                            <small>AUC003</small>
                        </div>
                        <span class="badge completed">Completed</span>
                    </div>

                    <div class="auction-meta">
                        <div class="meta-item">
                            <div class="meta-icon icon-blue">📅</div>
                            <div>
                                Auction Date<br>
                                <b>20/11/2024</b><br>
                                <small>2:00:00 pm</small>
                            </div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-icon icon-purple">🏆</div>
                            <div>
                                Winner<br>
                                <b>Jane Smith</b><br>
                                <small>M002</small>
                            </div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-icon icon-green">📉</div>
                            <div>
                                Your Share of Discount<br>
                                <b>₹1,000</b><br>
                                <small>From 10.00% total discount</small>
                            </div>
                        </div>
                    </div>

                    <div class="auction-stats">
                        <div>
                            Pool Amount<br>
                            <b>₹2,50,000</b>
                        </div>
                        <div>
                            Winning Bid<br>
                            <b>₹2,25,000</b>
                        </div>
                        <div>
                            Total Discount<br>
                            <b style="color:#16a34a;">₹25,000</b>
                        </div>
                    </div>

                    <div class="auction-note">
                        <b>How it works:</b>
                        The discount of ₹25,000 is distributed equally among all members.
                        Your monthly contribution was reduced by ₹1,000 this month.
                    </div>

                </div>

                <!-- AUCTION 2 -->
                <div class="auction-history-card">

                    <div class="auction-header">
                        <div>
                            <div class="auction-title">
                                Business Circle - Month 7
                            </div>
                            <small>AUC004</small>
                        </div>
                        <span class="badge completed">Completed</span>
                    </div>

                    <div class="auction-meta">
                        <div class="meta-item">
                            <div class="meta-icon icon-blue">📅</div>
                            <div>
                                Auction Date<br>
                                <b>25/11/2024</b><br>
                                <small>3:00:00 pm</small>
                            </div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-icon icon-purple">🏆</div>
                            <div>
                                Winner<br>
                                <b>Robert Johnson</b><br>
                                <small>M003</small>
                            </div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-icon icon-green">📉</div>
                            <div>
                                Your Share of Discount<br>
                                <b>₹1,200</b><br>
                                <small>From 10.00% total discount</small>
                            </div>
                        </div>
                    </div>

                    <div class="auction-stats">
                        <div>
                            Pool Amount<br>
                            <b>₹3,00,000</b>
                        </div>
                        <div>
                            Winning Bid<br>
                            <b>₹2,70,000</b>
                        </div>
                        <div>
                            Total Discount<br>
                            <b style="color:#16a34a;">₹30,000</b>
                        </div>
                    </div>

                    <div class="auction-note">
                        <b>How it works:</b>
                        The discount of ₹30,000 is distributed equally among all members.
                        Your monthly contribution was reduced by ₹1,200 this month.
                    </div>

                </div>

            </div>
        </div>
    </div>

</body>

</html>
