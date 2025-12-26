<!DOCTYPE html>
<html>
<head>
<title>Upcoming Auctions</title>
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="wrapper">
<?php include '../layout/sidebar.php'; ?>

<div class="main">

<div class="topbar">
    <div>
        <div class="page-title">Upcoming Auctions</div>
        <div class="page-subtitle">Scheduled auctions for chit groups</div>
    </div>

</div>

<div class="content">

    <a href="create.php">
        <button class="btn-primary">＋ Create Auction</button>
    </a>
<div class="table-box">
<b>Scheduled Auctions (1)</b><br><br>

<table>
<thead>
<tr>
    <th>Chit Group</th>
    <th>Month</th>
    <th>Pool Amount</th>
    <th>Scheduled Date</th>
    <th>Status</th>
</tr>
</thead>

<tbody>
<tr>
    <td>Business Circle</td>
    <td>Month 8</td>
    <td>₹3,00,000</td>
    <td>📅 25/12/2024 &nbsp; ⏰ 03:00 PM</td>
    <td><span class="badge upcoming">upcoming</span></td>
</tr>
</tbody>
</table>

</div>

</div>
</div>
</div>

</body>
</html>
