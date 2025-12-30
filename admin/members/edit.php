<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

if (!isset($_GET['id'])) {
    die('Member ID missing');
}

$member_id = $_GET['id'];

/* Fetch member */
$stmt = $conn->prepare('SELECT * FROM members WHERE member_id = ?');
$stmt->bind_param('s', $member_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt = $conn->prepare("
    SELECT 
        m.*,
        b.account_name,
        b.account_no,
        b.bank_name,
        b.ifsc,
        b.upi_id
    FROM members m
    LEFT JOIN member_bank_details b 
        ON b.member_id = m.member_id
    WHERE m.member_id = ?
");
$stmt->bind_param('s', $member_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Member not found');
}

$m = $result->fetch_assoc();


// if ($result->num_rows === 0) {
//     die('Member not found');
// }

// $m = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Member</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <div class="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div class="main">
            <div class="topbar">
                <div>
                    <div class="page-title">Edit Member</div>
                    <div class="page-subtitle">Update member details</div>
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

                <form method="post" action="update.php" enctype="multipart/form-data">

                    <input type="hidden" name="member_id" value="<?= $m['member_id'] ?>">

                    <!-- ================= PERSONAL ================= -->
                    <div id="personal" class="step-content active form-box">
                        <h4>Personal Information</h4><br>

                        <div class="form-group">
                            <label>Full Name *</label>
                            <input class="form-control" name="full_name"
                                value="<?= htmlspecialchars($m['full_name']) ?>" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Gender *</label>
                                <select class="form-control" name="gender" required>
                                    <option value="">Select</option>
                                    <option <?= $m['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option <?= $m['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Date of Birth *</label>
                                <input type="date" class="form-control" name="dob" value="<?= $m['dob'] ?>"
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Permanent Address *</label>
                            <textarea class="form-control" name="address" required><?= htmlspecialchars($m['address']) ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Aadhaar Number *</label>
                                <input class="form-control" name="aadhaar" value="<?= htmlspecialchars($m['aadhar']) ?>"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Mobile Number *</label>
                                <input class="form-control" name="mobile" value="<?= htmlspecialchars($m['mobile']) ?>"
                                    required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Email</label>
                                <input class="form-control" name="email" value="<?= htmlspecialchars($m['email']) ?>">
                            </div>

                            <div class="form-group">
                                <label>Joining Date *</label>
                                <input type="date" class="form-control" name="joining_date"
                                    value="<?= $m['joining_date'] ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Update Photo (optional)</label>
                            <input type="file" class="form-control" name="photo">
                        </div>

                        <div class="form-group">
                            <label>Active Status</label><br><br>
                            <label class="switch">
                                <input type="checkbox" name="status" value="1"
                                    <?= $m['is_active'] ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <br>
                        <button type="button" class="btn-primary" onclick="showStep('bank')">
                            Next → Bank Details
                        </button>
                    </div>

                    <!-- ================= BANK ================= -->
                    <div id="bank" class="step-content form-box">

                        <h4>Bank Account Details</h4><br>

                        <div class="form-group">
                            <label>Account Holder Name *</label>
                            <input class="form-control" name="acc_name"
                                value="<?= htmlspecialchars($m['account_name']) ?>" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Account Number *</label>
                                <input class="form-control" name="acc_no"
                                    value="<?= htmlspecialchars($m['account_no']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Bank Name *</label>
                                <input class="form-control" name="bank_name"
                                    value="<?= htmlspecialchars($m['bank_name']) ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>IFSC Code *</label>
                                <input class="form-control" name="ifsc" value="<?= htmlspecialchars($m['ifsc']) ?>"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>UPI ID (Optional)</label>
                                <input class="form-control" name="upi"
                                    value="<?= htmlspecialchars($m['upi_id']) ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Cancelled Cheque / Passbook Photo *</label>
                            <input type="file" class="form-control" name="bank_doc">
                            <small>Leave empty to keep existing document</small>

                        </div>

                        <br>
                        <button type="button" class="btn-secondary" onclick="showStep('personal')">
                            ← Back
                        </button>

                        <button type="submit" class="btn-primary">
                            Update Member
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
