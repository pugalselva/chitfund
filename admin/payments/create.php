<!DOCTYPE html>
<html>
<head>
<title>Payment Entry</title>
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="wrapper">
<?php include '../layout/sidebar.php'; ?>

<div class="main">

<div class="topbar">
    <div>
        <div class="page-title">Payment Entry</div>
        <div class="page-subtitle">Record a member's monthly contribution payment</div>
    </div>
</div>

<div class="content">

<div class="form-box" style="max-width:600px;">

<h4>Payment Details</h4><br>

<form>

<div class="form-group">
<label>Member *</label>
<select class="form-control">
<option>Select member</option>
<option>M001 - John Doe</option>
<option>M002 - Jane Smith</option>
</select>
</div>

<div class="form-group">
<label>Chit Group *</label>
<select class="form-control">
<option>Select chit group</option>
<option>Elite Savings Group</option>
<option>Business Circle</option>
</select>
</div>

<div class="form-row">
<div class="form-group">
<label>Actual Amount (₹) *</label>
<input type="number" id="actual" class="form-control" value="10000">
</div>

<div class="form-group">
<label>Discount Amount (₹)</label>
<input type="number" id="discount" class="form-control" value="0">
<small>Discount from auction winnings</small>
</div>
</div>

<div class="amount-box">
    <span>Final Amount to Collect:</span>
    <b>₹<span id="final">0</span></b>
</div>

<div class="form-group">
<label>Mode of Payment *</label>
<select class="form-control">
<option>Select payment mode</option>
<option>UPI</option>
<option>CASH</option>
<option>BANK</option>
</select>
</div>

<div class="form-group">
<label>Payment Date *</label>
<input type="date" class="form-control" value="<?= date('Y-m-d') ?>">
</div>

<br>
<button class="btn-primary">Record Payment</button>
<a href="index.php">
<button type="button" class="btn-secondary">Cancel</button>
</a>

</form>

</div>
</div>
</div>
</div>

<script>
function calculate(){
    let actual = Number(document.getElementById('actual').value);
    let discount = Number(document.getElementById('discount').value);
    document.getElementById('final').innerText = actual - discount;
}
document.getElementById('actual').oninput = calculate;
document.getElementById('discount').oninput = calculate;
calculate();
</script>

</body>
</html>
