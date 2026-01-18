<?php
include '../auth.php';
?>
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
                <?php include '../layout/header.php'; ?>
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

                <form method="post" action="store.php" enctype="multipart/form-data">

                    <!-- ================= PERSONAL DETAILS ================= -->
                    <div id="personal" class="step-content active form-box">

                        <h4>Personal Information</h4><br>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Full Name *</label>
                                <input class="form-control" name="full_name" required>
                            </div>
                             <div class="form-group">
        <label>UTR ID *</label>
        <input class="form-control" name="utr_id" required>
        <small style="font-size:12px;color:#555;">
            UTR ID will be used as login password
        </small>
    </div>
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

                        <!-- <div class="form-row">
                            <div class="form-group">
                                <label>Password *</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>

                            <div class="form-group">
                                <label>Confirm Password *</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                        </div> -->
                        <div class="form-group">
                            <label>Upload Photo *</label>
                            <input type="file" class="form-control" name="photo">
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
                                <input class="form-control" name="bank_name" id="bank_name" required readonly>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>IFSC Code *</label>
                                <input class="form-control" name="ifsc" id="ifsc" required
                                    placeholder="e.g. HDFC0000123" onblur="validateIFSC()">

                                <small id="ifscMsg" style="font-size:12px;"></small>
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
    <!-- script -->
    <script>
        let ifscValid = false;

        function validateIFSC() {
            const ifscInput = document.getElementById('ifsc');
            const bankName = document.getElementById('bank_name');
            const msg = document.getElementById('ifscMsg');

            const ifsc = ifscInput.value.trim().toUpperCase();

            // Reset
            msg.textContent = '';
            bankName.value = '';
            ifscValid = false;

            if (!ifsc) return;

            msg.style.color = '#6b7280';
            msg.textContent = 'Validating IFSC...';

            fetch(`https://ifsc.razorpay.com/${ifsc}`)
                .then(res => {
                    if (!res.ok) throw new Error('Invalid IFSC');
                    return res.json();
                })
                .then(data => {
                    bankName.value = data.BANK;
                    msg.textContent = `✔ ${data.BANK}, ${data.BRANCH}`;
                    msg.style.color = '#16a34a';
                    ifscValid = true;
                })
                .catch(() => {
                    msg.textContent = '✖ Invalid IFSC Code';
                    msg.style.color = '#dc2626';
                    ifscInput.focus();
                });
        }
    </script>
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {

            if (!ifscValid) {
                e.preventDefault();
                alert('Please enter a valid IFSC code');
                document.getElementById('ifsc').focus();
            }
        });
    </script>

    <script>
        function showStep(step) {
            document.getElementById('personal').classList.remove('active');
            document.getElementById('bank').classList.remove('active');

            document.getElementById('tabPersonal').classList.remove('active');
            document.getElementById('tabBank').classList.remove('active');

            document.getElementById(step).classList.add('active');

            if (step === 'personal') {
                document.getElementById('tabPersonal').classList.add('active');
            } else {
                document.getElementById('tabBank').classList.add('active');
            }
        }
    </script>

</body>

</html>
