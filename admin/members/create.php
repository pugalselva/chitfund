<!DOCTYPE html>
<html>
<head>
<title>Member Enrollment</title>
<link rel="stylesheet" href="../../assets/css/style.css">

</head>

<body>

<div class="wrapper">
<?php include '../layout/sidebar.php'; ?>

<div class="main">

<div class="topbar">
    <div>
        <div class="page-title">Member Enrollment</div>
        <div class="page-subtitle">Register a new member with complete details</div>
    </div>
</div>

<div class="content">

<!-- STEPPER -->
<div class="step-tabs">
    <button id="tabPersonal" class="active" onclick="showStep('personal')">
        Personal Details
    </button>
    <button id="tabBank" onclick="showStep('bank')">
        Bank Details
    </button>
</div>

<form method="post" enctype="multipart/form-data">

<!-- ================= PERSONAL DETAILS ================= -->
<div id="personal" class="step-content active form-box">

<h4>Personal Information</h4><br>

<div class="form-group">
<label>Full Name *</label>
<input class="form-control" name="full_name" required>
</div>

<div class="form-row">
<div class="form-group">
<label>Gender *</label>
<select class="form-control" name="gender" required>
<option value="">Select</option>
<option>Male</option>
<option>Female</option>
</select>
</div>

<div class="form-group">
<label>Date of Birth *</label>
<input type="date" class="form-control" name="dob" required>
</div>
</div>

<div class="form-group">
<label>Permanent Address *</label>
<textarea class="form-control" name="address" required></textarea>
</div>

<div class="form-row">
<div class="form-group">
<label>Aadhaar Number *</label>
<input class="form-control" name="aadhaar" required>
</div>

<div class="form-group">
<label>Mobile Number *</label>
<input class="form-control" name="mobile" required>
</div>
</div>

<div class="form-row">
<div class="form-group">
<label>Email (Optional)</label>
<input class="form-control" name="email">
</div>

<div class="form-group">
<label>Joining Date *</label>
<input type="date" class="form-control" name="joining_date" required>
</div>
</div>

<div class="form-row">
<div class="form-group">
<label>Password *</label>
<input type="password" class="form-control" name="password" required>
</div>

<div class="form-group">
<label>Confirm Password *</label>
<input type="password" class="form-control" required>
</div>
</div>
<div class="form-group">
<label>Cancelled Cheque / Passbook Photo *</label>
<input type="file" class="form-control" name="bank_doc" required>
</div>
<div class="form-group">
    <label>Active Status</label><br><br>

    <label class="switch">
        <input type="checkbox" name="status" value="1" checked>
        <span class="slider"></span>
    </label>

    <span style="margin-left:10px;font-size:14px;">
        Member is Active
    </span>
</div>



<br>
<button type="button" class="btn-primary" onclick="showStep('bank')">
Next → Bank Details
</button>

</div>

<!-- ================= BANK DETAILS ================= -->
<div id="bank" class="step-content form-box">

<h4>Bank Account Details</h4><br>

<div class="form-group">
<label>Account Holder Name *</label>
<input class="form-control" name="acc_name" required>
</div>

<div class="form-row">
<div class="form-group">
<label>Account Number *</label>
<input class="form-control" name="acc_no" required>
</div>

<div class="form-group">
<label>Bank Name *</label>
<input class="form-control" name="bank_name" required>
</div>
</div>

<div class="form-row">
<div class="form-group">
<label>IFSC Code *</label>
<input class="form-control" name="ifsc" required>
</div>

<div class="form-group">
<label>UPI ID (Optional)</label>
<input class="form-control" name="upi">
</div>
</div>

<div class="form-group">
<label>Cancelled Cheque / Passbook Photo *</label>
<input type="file" class="form-control" name="bank_doc" required>
</div>

<br>
<button type="button" class="btn-secondary" onclick="showStep('personal')">
← Back
</button>

<button type="submit" class="btn-primary">
Enroll Member
</button>

<a href="index.php">
<button type="button" class="btn-secondary">Cancel</button>
</a>

</div>

</form>

</div>
</div>
</div>

<script>
function showStep(step){
    document.getElementById('personal').classList.remove('active');
    document.getElementById('bank').classList.remove('active');

    document.getElementById('tabPersonal').classList.remove('active');
    document.getElementById('tabBank').classList.remove('active');

    document.getElementById(step).classList.add('active');

    if(step === 'personal'){
        document.getElementById('tabPersonal').classList.add('active');
    } else {
        document.getElementById('tabBank').classList.add('active');
    }
}
</script>

</body>
</html>
