<?php
// session_start();
include '../../config/database.php';
include '../auth.php';


if (!isset($_GET['id'])) {
    die('Member ID missing');
}

$member_id = $_GET['id'];

/* ================= MEMBER + BANK ================= */
$stmt = $conn->prepare("
    SELECT 
        m.*,
        b.account_name,
        b.account_no,
        b.bank_name,
        b.ifsc,
        b.upi_id,
        b.cheque_photo
    FROM members m
    LEFT JOIN member_bank_details b 
        ON b.member_id = m.member_id
    WHERE m.member_id = ?
");

if (!$stmt) {
    die("SQL Prepare Failed: " . $conn->error);
}

$stmt->bind_param("s", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$m = $result->fetch_assoc();

/* ================= AGE ================= */
$dob = new DateTime($m['dob']);
$today = new DateTime();
$age = $today->diff($dob)->y;
?>

<!DOCTYPE html>
<html>

<head>
    <title>Member Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: white;
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 24px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .info-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 1rem;
            color: #111827;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div id="page-content-wrapper">
            <?php include '../layout/header.php'; ?>

            <div class="container-fluid p-4">

                <!-- Back & Actions -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="index.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                    <a href="edit.php?id=<?= $m['member_id'] ?>" class="btn btn-warning btn-sm shadow-sm">
                        <i class="fas fa-edit me-1"></i> Edit Profile
                    </a>
                </div>

                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                style="width: 60px; height: 60px; font-size: 1.5rem;">
                                <?= substr($m['full_name'], 0, 1) ?>
                            </div>
                            <div>
                                <h2 class="mb-0 fw-bold"><?= htmlspecialchars($m['full_name']) ?></h2>
                                <p class="mb-0 opacity-75">Member ID: #<?= htmlspecialchars($m['member_id']) ?></p>
                            </div>
                        </div>
                        <div class="text-md-end">
                            <span
                                class="badge <?= $m['is_active'] ? 'bg-success' : 'bg-danger' ?> fs-6 px-3 py-2 shadow-sm">
                                <?= $m['is_active'] ? 'Active Member' : 'Inactive Account' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-xl-9">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                                <ul class="nav nav-tabs card-header-tabs" id="viewTabs" role="tablist">
                                    <li class="nav-item">
                                        <button class="nav-link active fw-bold" id="personal-tab" data-bs-toggle="tab"
                                            data-bs-target="#personal" type="button">
                                            <i class="fas fa-user-circle me-2"></i>Personal Info
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link fw-bold" id="bank-tab" data-bs-toggle="tab"
                                            data-bs-target="#bank" type="button">
                                            <i class="fas fa-university me-2"></i>Bank Details
                                        </button>
                                    </li>
                                    <!-- <li class="nav-item">
                                        <button class="nav-link fw-bold" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button">
                                            <i class="fas fa-chart-line me-2"></i>Activity
                                        </button>
                                    </li> -->
                                </ul>
                            </div>

                            <div class="card-body p-4">
                                <div class="tab-content">

                                    <!-- Personal Tab -->
                                    <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                        <div class="row g-4">
                                            <div class="col-md-6 col-lg-4">
                                                <div class="info-label">Date of Birth</div>
                                                <div class="info-value"><?= date('d M Y', strtotime($m['dob'])) ?></div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="info-label">Age</div>
                                                <div class="info-value"><?= $age ?> years</div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="info-label">Gender</div>
                                                <div class="info-value"><?= htmlspecialchars($m['gender']) ?></div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="info-label">Mobile Number</div>
                                                <div class="info-value"><?= htmlspecialchars($m['mobile']) ?></div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="info-label">Email Address</div>
                                                <div class="info-value">
                                                    <?= htmlspecialchars($m['email']) ?: '<span class="text-muted">-</span>' ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="info-label">Aadhaar Number</div>
                                                <div class="info-value"><?= htmlspecialchars($m['aadhar']) ?></div>
                                            </div>
                                            <div class="col-12">
                                                <div class="info-label">Permanent Address</div>
                                                <div class="info-value"><?= nl2br(htmlspecialchars($m['address'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bank Tab -->
                                    <div class="tab-pane fade" id="bank" role="tabpanel">
                                        <?php if (!empty($m['account_no'])): ?>
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <div class="p-3 border rounded bg-light h-100">
                                                        <h6 class="fw-bold mb-3 text-primary">Account Details</h6>
                                                        <div class="mb-3">
                                                            <div class="info-label">Account Holder</div>
                                                            <div class="info-value">
                                                                <?= htmlspecialchars($m['account_name']) ?></div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <div class="info-label">Account Number</div>
                                                            <div class="info-value fs-5 fw-bold font-monospace">
                                                                <?= htmlspecialchars($m['account_no']) ?></div>
                                                        </div>
                                                        <div class="mb-0">
                                                            <div class="info-label">Bank Name</div>
                                                            <div class="info-value"><?= htmlspecialchars($m['bank_name']) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="p-3 border rounded bg-light h-100">
                                                        <h6 class="fw-bold mb-3 text-primary">Payment Info</h6>
                                                        <div class="mb-3">
                                                            <div class="info-label">IFSC Code</div>
                                                            <div class="info-value font-monospace">
                                                                <?= htmlspecialchars($m['ifsc']) ?></div>
                                                        </div>
                                                        <div class="mb-0">
                                                            <div class="info-label">UPI ID</div>
                                                            <div class="info-value">
                                                                <?= htmlspecialchars($m['upi_id'] ?: '-') ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center p-5">
                                                <i class="fas fa-university fa-3x text-muted mb-3 opacity-25"></i>
                                                <h6 class="text-muted">No bank details added for this member.</h6>
                                                <a href="edit.php?id=<?= $m['member_id'] ?>"
                                                    class="btn btn-sm btn-outline-primary mt-2">Add Bank Details</a>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats (Placeholder active groups) -->
                    <div class="col-12 col-xl-3">
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Membership Info</h6>
                                <div class="mb-3">
                                    <div class="info-label">Joined On</div>
                                    <div class="info-value"><?= date('d M Y', strtotime($m['joining_date'])) ?></div>
                                </div>
                                <!-- Add real stats here later -->
                                <div class="alert alert-light border small text-muted mb-0">
                                    <i class="fas fa-info-circle me-1"></i> Activity stats coming soon.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <?php include '../layout/scripts.php'; ?>
</body>

</html>