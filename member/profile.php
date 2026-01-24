<?php
include 'auth.php';
include '../config/database.php';

$name = $_SESSION['name'] ?? 'Member';
$email = $_SESSION['email'] ?? '';

$userId = $_SESSION['user_id'];

/* Fetch member details */
$stmt = $conn->prepare("
    SELECT *
    FROM members
    WHERE user_id = ?
    LIMIT 1
");
$stmt->bind_param('i', $userId);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();

if (!$member) {
    die('Member record not found');
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .profile-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            background: #fff;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .profile-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.5rem;
        }

        .profile-body {
            padding: 2rem;
        }

        .form-floating>.form-control:focus,
        .form-floating>.form-control:not(:placeholder-shown) {
            padding-top: 1.625rem;
            padding-bottom: 0.625rem;
        }

        .btn-primary {
            background-color: #4338ca;
            border-color: #4338ca;
        }

        .btn-primary:hover {
            background-color: #3730a3;
            border-color: #3730a3;
        }
    </style>
</head>

<body class="bg-light">
    <div class="d-flex" id="wrapper">
        <?php include 'layout/sidebar.php'; ?>

        <div id="page-content-wrapper" class="w-100">
            <!-- Navbar -->
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4 py-3">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-light text-primary me-3 d-lg-none" id="sidebarToggle">
                            <i class="fas fa-bars fa-lg"></i>
                        </button>
                        <div>
                            <h4 class="mb-0 fw-bold text-dark">Profile Settings</h4>
                            <p class="mb-0 text-muted small d-none d-md-block">Manage your account information</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-end d-none d-md-block">
                            <h6 class="mb-0 fw-bold"><?= htmlspecialchars($name) ?></h6>
                            <small class="text-muted"><?= htmlspecialchars($email) ?></small>
                        </div>
                        <a href="../logout.php" class="btn btn-outline-danger btn-sm rounded-circle p-2" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4 py-4">
                <div class="row g-4">
                    <!-- Column 1: Personal Information -->
                    <div class="col-12 col-lg-6">
                        <div class="card profile-card h-100">
                            <div class="profile-header">
                                <h5 class="mb-0 fw-bold text-dark"><i
                                        class="fas fa-user-circle me-2 text-primary"></i>Personal Information</h5>
                            </div>
                            <div class="profile-body">
                                <form id="profileForm">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="full_name" name="full_name"
                                            placeholder="Full Name"
                                            value="<?= htmlspecialchars($member['full_name']) ?>" required>
                                        <label for="full_name">Full Name</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="name@example.com"
                                            value="<?= htmlspecialchars($member['email']) ?>">
                                        <label for="email">Email Address</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="mobile" name="mobile"
                                            placeholder="Mobile Number"
                                            value="<?= htmlspecialchars($member['mobile']) ?>" required>
                                        <label for="mobile">Mobile Number</label>
                                    </div>

                                    <div class="form-floating mb-4">
                                        <textarea class="form-control" placeholder="Address" id="address" name="address"
                                            style="height: 100px"><?= htmlspecialchars($member['address']) ?></textarea>
                                        <label for="address">Address</label>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                                        <i class="fas fa-save me-2"></i>Save Changes
                                    </button>
                                    <div class="mt-3 text-center" id="profileMsg"></div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Security -->
                    <div class="col-12 col-lg-6">
                        <div class="card profile-card h-100">
                            <div class="profile-header">
                                <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-lock me-2 text-warning"></i>Security
                                </h5>
                            </div>
                            <div class="profile-body">
                                <form id="passwordForm">
                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="current_password"
                                            name="current_password" placeholder="Current Password" required>
                                        <label for="current_password">Current Password</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="new_password"
                                            name="new_password" placeholder="New Password" required>
                                        <label for="new_password">New Password</label>
                                    </div>

                                    <div class="form-floating mb-4">
                                        <input type="password" class="form-control" id="confirm_password"
                                            name="confirm_password" placeholder="Confirm Password" required>
                                        <label for="confirm_password">Confirm Password</label>
                                    </div>

                                    <button type="submit" class="btn btn-dark w-100 py-2 fw-medium">
                                        <i class="fas fa-key me-2"></i>Update Password
                                    </button>
                                    <div class="mt-3 text-center" id="passwordMsg"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /* =====================
           UPDATE PROFILE
        ===================== */
        document.getElementById('profileForm').addEventListener('submit', e => {
            e.preventDefault();
            const btn = e.target.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';

            fetch('ajax/update-profile.php', {
                method: 'POST',
                body: new FormData(e.target)
            })
                .then(res => res.json())
                .then(data => {
                    const msg = document.getElementById('profileMsg');
                    msg.innerHTML = `<div class="alert alert-${data.status ? 'success' : 'danger'} alert-dismissible fade show" role="alert">
                                    ${data.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                 </div>`;
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                })
                .catch(err => {
                    console.error(err);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        });

        /* =====================
           CHANGE PASSWORD
        ===================== */
        document.getElementById('passwordForm').addEventListener('submit', e => {
            e.preventDefault();

            const newPass = document.getElementById('new_password').value;
            const confirmPass = document.getElementById('confirm_password').value;

            if (newPass !== confirmPass) {
                const msg = document.getElementById('passwordMsg');
                msg.innerHTML = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    Passwords do not match!
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                 </div>`;
                return;
            }

            const btn = e.target.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';

            fetch('ajax/change-password.php', {
                method: 'POST',
                body: new FormData(e.target)
            })
                .then(res => res.json())
                .then(data => {
                    const msg = document.getElementById('passwordMsg');
                    msg.innerHTML = `<div class="alert alert-${data.status ? 'success' : 'danger'} alert-dismissible fade show" role="alert">
                                    ${data.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                 </div>`;

                    if (data.status) e.target.reset();
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                })
                .catch(err => {
                    console.error(err);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        });

        // Toggle Sidebar for Mobile
    </script>
    <script src="../assets/js/scripts.js"></script>
</body>

</html>