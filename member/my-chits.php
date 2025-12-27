<!DOCTYPE html>
<html>

<head>
    <title>My Chits</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include 'layout/sidebar.php'; ?>

        <div class="main">

            <div class="topbar">
                <div>
                    <div class="page-title">My Chit Groups</div>
                    <div class="page-subtitle">View and manage your active chit fund memberships</div>
                </div>
    <div class="topbar">
                <div>
                    <b>Member User</b><br>
                    sandy@gmail.com
                    <a href="../logout.php" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>

            </div>
            </div>

            <div class="content">

                <div class="chit-grid">

                    <!-- CHIT 1 -->
                    <div class="chit-card">
                        <div class="chit-header">
                            <div>
                                <div class="chit-title">Elite Savings Group</div>
                                <small>CG001</small>
                            </div>
                            <span class="badge active">active</span>
                        </div>

                        <div class="chit-stats">
                            <div class="stat">
                                <div class="icon icon-blue">₹</div>
                                <div>
                                    Monthly Contribution<br>
                                    <b>₹10,000</b>
                                </div>
                            </div>

                            <div class="stat">
                                <div class="icon icon-purple">💼</div>
                                <div>
                                    Pool Amount<br>
                                    <b>₹2,50,000</b>
                                </div>
                            </div>

                            <div class="stat">
                                <div class="icon icon-green">👥</div>
                                <div>
                                    Total Members<br>
                                    <b>25</b>
                                </div>
                            </div>

                            <div class="stat">
                                <div class="icon icon-orange">📅</div>
                                <div>
                                    Duration<br>
                                    <b>25 months</b>
                                </div>
                            </div>
                        </div>

                        <small>Progress</small>
                        <div class="progress-bar">
                            <div class="progress" style="width:48%;"></div>
                        </div>
                        <small>12 / 25 months</small>

                        <div class="chit-footer">
                            <div>
                                Completed Months<br>
                                <b>12</b>
                            </div>
                            <div>
                                Remaining Months<br>
                                <b>13</b>
                            </div>
                            <div>
                                Auction Type<br>
                                <b>Reverse</b>
                            </div>
                            <div>
                                Foreman Commission<br>
                                <b>5%</b>
                            </div>
                        </div>

                        <div class="chit-total">
                            Total Chit Value<br>
                            <b>₹25,00,000</b>
                        </div>
                    </div>

                    <!-- CHIT 2 -->
                    <div class="chit-card">
                        <div class="chit-header">
                            <div>
                                <div class="chit-title">Business Circle</div>
                                <small>CG002</small>
                            </div>
                            <span class="badge active">active</span>
                        </div>

                        <div class="chit-stats">
                            <div class="stat">
                                <div class="icon icon-blue">₹</div>
                                <div>Monthly Contribution<br><b>₹15,000</b></div>
                            </div>
                            <div class="stat">
                                <div class="icon icon-purple">💼</div>
                                <div>Pool Amount<br><b>₹3,00,000</b></div>
                            </div>
                            <div class="stat">
                                <div class="icon icon-green">👥</div>
                                <div>Total Members<br><b>20</b></div>
                            </div>
                            <div class="stat">
                                <div class="icon icon-orange">📅</div>
                                <div>Duration<br><b>20 months</b></div>
                            </div>
                        </div>

                        <small>Progress</small>
                        <div class="progress-bar">
                            <div class="progress" style="width:35%;"></div>
                        </div>
                        <small>7 / 20 months</small>

                        <div class="chit-footer">
                            <div>Completed Months<br><b>7</b></div>
                            <div>Remaining Months<br><b>13</b></div>
                            <div>Auction Type<br><b>Reverse</b></div>
                            <div>Foreman Commission<br><b>5%</b></div>
                        </div>

                        <div class="chit-total">
                            Total Chit Value<br>
                            <b>₹30,00,000</b>
                        </div>
                    </div>

                    <!-- CHIT 3 -->
                    <div class="chit-card">
                        <div class="chit-header">
                            <div>
                                <div class="chit-title">Community Fund</div>
                                <small>CG003</small>
                            </div>
                            <span class="badge upcoming">upcoming</span>
                        </div>

                        <div class="chit-stats">
                            <div class="stat">
                                <div class="icon icon-blue">₹</div>
                                <div>Monthly Contribution<br><b>₹5,000</b></div>
                            </div>
                            <div class="stat">
                                <div class="icon icon-purple">💼</div>
                                <div>Pool Amount<br><b>₹1,50,000</b></div>
                            </div>
                            <div class="stat">
                                <div class="icon icon-green">👥</div>
                                <div>Total Members<br><b>30</b></div>
                            </div>
                            <div class="stat">
                                <div class="icon icon-orange">📅</div>
                                <div>Duration<br><b>30 months</b></div>
                            </div>
                        </div>

                        <small>Progress</small>
                        <div class="progress-bar">
                            <div class="progress" style="width:0%;"></div>
                        </div>
                        <small>0 / 30 months</small>

                        <div class="chit-footer">
                            <div>Completed Months<br><b>0</b></div>
                            <div>Remaining Months<br><b>30</b></div>
                            <div>Auction Type<br><b>Open</b></div>
                            <div>Foreman Commission<br><b>4%</b></div>
                        </div>

                        <div class="chit-total">
                            Total Chit Value<br>
                            <b>₹15,00,000</b>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

</body>

</html>
