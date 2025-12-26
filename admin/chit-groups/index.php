<!DOCTYPE html>
<html>
<head>
<title>Chit Groups</title>
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="wrapper">

<?php include '../layout/sidebar.php'; ?>

<div class="main">

    <div class="topbar">
        <div>
            <div class="page-title">Chit Groups</div>
            <div class="page-subtitle">Manage all chit fund groups</div>
        </div>

    
    </div>

    <div class="content">
           <!-- REDIRECT BUTTON -->
        <a href="create.php">
            <button class="btn-primary">＋ Create Group</button>
        </a>

        <div class="table-box">
            <b>All Groups (4)</b><br><br>

            <table>
                <thead>
                    <tr>
                        <th>Group ID</th>
                        <th>Group Name</th>
                        <th>Total Value</th>
                        <th>Members</th>
                        <th>Contribution</th>
                        <th>Duration</th>
                        <th>Progress</th>
                        <th>Auction Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>CG001</td>
                        <td>
                            Elite Savings Group<br>
                            <small>Commission: 5%</small>
                        </td>
                        <td>₹25,00,000</td>
                        <td>25</td>
                        <td>₹10,000</td>
                        <td>25 months</td>
                        <td>12 / 25<br><small>48%</small></td>
                        <td>Reverse</td>
                        <td><span class="badge active">active</span></td>
                        <td>
    <a href="view.php?id=CG001" style="text-decoration:none;">
        👁 View
    </a>
</td>
                    </tr>

                    <tr>
                        <td>CG002</td>
                        <td>
                            Business Circle<br>
                            <small>Commission: 5%</small>
                        </td>
                        <td>₹30,00,000</td>
                        <td>20</td>
                        <td>₹15,000</td>
                        <td>20 months</td>
                        <td>7 / 20<br><small>35%</small></td>
                        <td>Reverse</td>
                        <td><span class="badge active">active</span></td>
                       <td>
    <a href="view.php?id=CG001" style="text-decoration:none;">
        👁 View
    </a>
</td>
                    </tr>

                    <tr>
                        <td>CG003</td>
                        <td>
                            Community Fund<br>
                            <small>Commission: 4%</small>
                        </td>
                        <td>₹15,00,000</td>
                        <td>30</td>
                        <td>₹5,000</td>
                        <td>30 months</td>
                        <td>0 / 30<br><small>0%</small></td>
                        <td>Open</td>
                        <td><span class="badge upcoming">upcoming</span></td>
                        <td>
    <a href="view.php?id=CG001" style="text-decoration:none;">
        👁 View
    </a>
</td>
                    </tr>

                    <tr>
                        <td>CG004</td>
                        <td>
                            Professional Network<br>
                            <small>Commission: 5%</small>
                        </td>
                        <td>₹30,00,000</td>
                        <td>15</td>
                        <td>₹20,000</td>
                        <td>15 months</td>
                        <td>15 / 15<br><small>100%</small></td>
                        <td>Reverse</td>
                        <td><span class="badge completed">completed</span></td>
                       <td>
    <a href="view.php?id=CG001" style="text-decoration:none;">
        👁 View
    </a>
</td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>
</div>
</div>

</body>
</html>
