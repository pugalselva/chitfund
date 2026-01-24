<?php
include '../../config/database.php';
include '../auth.php';

if (!isset($_GET['id'])) {
    die('Member ID missing');
}

$member_id = $_GET['id'];

/* Fetch member */
$stmt = $conn->prepare('SELECT * FROM members WHERE member_id = ?');
$stmt->bind_param('s', $member_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Member not found');
}

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
$m = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Member</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <div class="d-flex" id="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div id="page-content-wrapper">
            <?php include '../layout/header.php'; ?>

            <div class="container-fluid p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0 fw-bold">Edit Member</h4>
                        <small class="text-secondary">Update member information</small>
                    </div>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>

                <div class="row justify-content-center">
                    <div class="col-12 col-xl-10">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                                <ul class="nav nav-tabs card-header-tabs" id="editTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active fw-bold" id="personal-tab" data-bs-toggle="tab"
                                            data-bs-target="#personal" type="button" role="tab">
                                            <i class="fas fa-user me-2"></i>Personal Details
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link fw-bold" id="bank-tab" data-bs-toggle="tab"
                                            data-bs-target="#bank" type="button" role="tab">
                                            <i class="fas fa-university me-2"></i>Bank Details
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-body p-4">
                                <form method="post" action="update.php" enctype="multipart/form-data">
                                    <input type="hidden" name="member_id" value="<?= $m['member_id'] ?>">

                                    <div class="tab-content" id="editTabsContent">

                                        <!-- PERSONAL TAB -->
                                        <div class="tab-pane fade show active" id="personal" role="tabpanel">

                                            <div class="row g-3 mb-4">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="full_name"
                                                            id="full_name"
                                                            value="<?= htmlspecialchars($m['full_name']) ?>" required>
                                                        <label for="full_name">Full Name *</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <select class="form-select" name="gender" id="gender" required>
                                                            <option value="" disabled>Select</option>
                                                            <option <?= $m['gender'] == 'Male' ? 'selected' : '' ?>>Male
                                                            </option>
                                                            <option <?= $m['gender'] == 'Female' ? 'selected' : '' ?>>
                                                                Female</option>
                                                        </select>
                                                        <label for="gender">Gender *</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="date" class="form-control" name="dob" id="dob"
                                                            value="<?= $m['dob'] ?>" required>
                                                        <label for="dob">Date of Birth *</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="mobile"
                                                            id="mobile" value="<?= htmlspecialchars($m['mobile']) ?>"
                                                            required>
                                                        <label for="mobile">Mobile Number *</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-floating mb-3">
                                                <textarea class="form-control" name="address" id="address"
                                                    style="height: 100px"
                                                    required><?= htmlspecialchars($m['address']) ?></textarea>
                                                <label for="address">Permanent Address *</label>
                                            </div>

                                            <div class="row g-3 mb-4">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="aadhaar"
                                                            id="aadhaar" value="<?= htmlspecialchars($m['aadhar']) ?>"
                                                            required>
                                                        <label for="aadhaar">Aadhaar Number *</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="email" class="form-control" name="email" id="email"
                                                            value="<?= htmlspecialchars($m['email']) ?>">
                                                        <label for="email">Email Address</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="date" class="form-control" name="joining_date"
                                                            id="joining_date" value="<?= $m['joining_date'] ?>"
                                                            required>
                                                        <label for="joining_date">Joining Date *</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="text-secondary opacity-25">
                                            <h6 class="text-muted fw-bold mb-3">Security & Status</h6>

                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="password" class="form-control" name="new_password"
                                                            id="new_password" placeholder="New Password">
                                                        <label for="new_password">New Password</label>
                                                    </div>
                                                    <div class="form-text">Leave blank to keep existing password</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="password" class="form-control"
                                                            name="confirm_password" id="confirm_password"
                                                            placeholder="Confirm Password">
                                                        <label for="confirm_password">Confirm New Password</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="status" id="status" value="1" <?= $m['is_active'] ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="status">Active Account</label>
                                            </div>

                                            <div class="d-flex justify-content-end mt-4">
                                                <button type="button" class="btn btn-primary px-4"
                                                    onclick="switchTab('#bank-tab')">
                                                    Next: Bank Details <i class="fas fa-arrow-right ms-2"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- BANK TAB -->
                                        <div class="tab-pane fade" id="bank" role="tabpanel">

                                            <div class="row g-3 mb-4">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="acc_name"
                                                            id="acc_name"
                                                            value="<?= htmlspecialchars($m['account_name']) ?>"
                                                            required>
                                                        <label for="acc_name">Account Holder Name *</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="acc_no"
                                                            id="acc_no"
                                                            value="<?= htmlspecialchars($m['account_no']) ?>" required>
                                                        <label for="acc_no">Account Number *</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="bank_name"
                                                            id="bank_name"
                                                            value="<?= htmlspecialchars($m['bank_name']) ?>" required>
                                                        <label for="bank_name">Bank Name *</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="ifsc" id="ifsc"
                                                            value="<?= htmlspecialchars($m['ifsc']) ?>" required>
                                                        <label for="ifsc">IFSC Code *</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="upi" id="upi"
                                                            value="<?= htmlspecialchars($m['upi_id']) ?>">
                                                        <label for="upi">UPI ID (Optional)</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label text-secondary small fw-bold">Bank Document
                                                    (Passbook/Cheque) </label>
                                                <input type="file" class="form-control" name="bank_doc">
                                                <div class="form-text">Upload new document to replace existing one.
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between mt-4">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    onclick="switchTab('#personal-tab')">
                                                    <i class="fas fa-arrow-left ms-2"></i> back
                                                </button>
                                                <button type="submit" class="btn btn-success px-4 fw-bold">
                                                    <i class="fas fa-save me-2"></i> Update Member
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include '../layout/scripts.php'; ?>
    <script>
        function switchTab(tabId) {
            const triggerEl = document.querySelector(tabId);
            const tab = new bootstrap.Tab(triggerEl);
            tab.show();
        }
    </script>
</body>

</html>