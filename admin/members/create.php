<?php
include '../auth.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Member Enrollment</title>
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
                        <h4 class="mb-0 fw-bold">Member Enrollment</h4>
                        <small class="text-secondary">Register a new member to the system</small>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-12 col-xl-10">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                                <ul class="nav nav-tabs card-header-tabs" id="enrollTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active fw-bold" id="personal-tab" data-bs-toggle="tab"
                                            data-bs-target="#personal" type="button" role="tab">
                                            <i class="fas fa-user-plus me-2"></i>Personal Details
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
                                <form method="post" action="store.php" enctype="multipart/form-data">

                                    <div class="tab-content" id="enrollTabsContent">

                                        <!-- PERSONAL DETAILS -->
                                        <div class="tab-pane fade show active" id="personal" role="tabpanel">

                                            <div class="row g-3 mb-4">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="full_name"
                                                            id="full_name" placeholder="Full Name" required>
                                                        <label for="full_name">Full Name *</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="utr_id"
                                                            id="utr_id" placeholder="UTR ID" required>
                                                        <label for="utr_id">UTR ID *</label>
                                                    </div>
                                                    <div class="form-text small"><i class="fas fa-info-circle me-1"></i>
                                                        UTR ID will be used as the login password</div>
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-4">
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <select class="form-select" name="gender" id="gender" required>
                                                            <option value="" selected disabled>Select</option>
                                                            <option>Male</option>
                                                            <option>Female</option>
                                                            <option>Other</option>
                                                        </select>
                                                        <label for="gender">Gender *</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="date" class="form-control" name="dob" id="dob"
                                                            required>
                                                        <label for="dob">Date of Birth *</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="mobile"
                                                            id="mobile" placeholder="Mobile" required>
                                                        <label for="mobile">Mobile Number *</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-floating mb-4">
                                                <textarea class="form-control" name="address" id="address"
                                                    placeholder="Address" style="height: 100px" required></textarea>
                                                <label for="address">Permanent Address *</label>
                                            </div>

                                            <div class="row g-3 mb-4">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="aadhaar"
                                                            id="aadhaar" placeholder="Aadhaar" required>
                                                        <label for="aadhaar">Aadhaar Number *</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="email" class="form-control" name="email" id="email"
                                                            placeholder="Email">
                                                        <label for="email">Email Address (Optional)</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-4">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="date" class="form-control" name="joining_date"
                                                            id="joining_date" required>
                                                        <label for="joining_date">Joining Date *</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-secondary small fw-bold">Profile Photo
                                                        *</label>
                                                    <input type="file" class="form-control" name="photo">
                                                </div>
                                            </div>

                                            <div class="form-check form-switch mb-4">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="status" id="status" value="1" checked>
                                                <label class="form-check-label" for="status">Activate Account
                                                    Immediately</label>
                                            </div>

                                            <div class="d-flex justify-content-end">
                                                <button type="button" class="btn btn-primary px-4"
                                                    onclick="switchTab('#bank-tab')">
                                                    Next: Bank Details <i class="fas fa-arrow-right ms-2"></i>
                                                </button>
                                            </div>

                                        </div>

                                        <!-- BANK DETAILS -->
                                        <div class="tab-pane fade" id="bank" role="tabpanel">

                                            <div class="row g-3 mb-4">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="acc_name"
                                                            id="acc_name" placeholder="Holder Name" required>
                                                        <label for="acc_name">Account Holder Name *</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="acc_no"
                                                            id="acc_no" placeholder="Account No" required>
                                                        <label for="acc_no">Account Number *</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-4">
                                                <div class="col-md-4">
                                                    <div class="form-floating position-relative">
                                                        <input type="text" class="form-control" name="ifsc" id="ifsc"
                                                            placeholder="IFSC" required onblur="validateIFSC()">
                                                        <label for="ifsc">IFSC Code *</label>
                                                        <div id="ifscMsg" class="form-text position-absolute"
                                                            style="top: 100%; font-size: 0.75rem;"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control bg-light"
                                                            name="bank_name" id="bank_name" placeholder="Bank Name"
                                                            readonly required>
                                                        <label for="bank_name">Bank Name (Auto)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="upi" id="upi"
                                                            placeholder="UPI ID">
                                                        <label for="upi">UPI ID (Optional)</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-5">
                                                <label class="form-label text-secondary small fw-bold">Cancelled Cheque
                                                    / Passbook *</label>
                                                <input type="file" class="form-control" name="bank_doc" required>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    onclick="switchTab('#personal-tab')">
                                                    <i class="fas fa-arrow-left ms-2"></i> Back
                                                </button>
                                                <div class="d-flex gap-2">
                                                    <a href="index.php" class="btn btn-light border">Cancel</a>
                                                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                                                        <i class="fas fa-check-circle me-2"></i> Enroll Member
                                                    </button>
                                                </div>
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

        let ifscValid = false;

        function validateIFSC() {
            const ifscInput = document.getElementById('ifsc');
            const bankName = document.getElementById('bank_name');
            const msg = document.getElementById('ifscMsg');

            const ifsc = ifscInput.value.trim().toUpperCase();

            // Reset
            msg.textContent = '';
            bankName.value = '';
            ifscInput.classList.remove('is-valid', 'is-invalid');
            ifscValid = false;

            if (!ifsc) return;

            msg.className = 'form-text text-muted';
            msg.textContent = 'Validating...';

            fetch(`https://ifsc.razorpay.com/${ifsc}`)
                .then(res => {
                    if (!res.ok) throw new Error('Invalid IFSC');
                    return res.json();
                })
                .then(data => {
                    bankName.value = data.BANK;
                    msg.innerHTML = `<i class="fas fa-check-circle me-1"></i> ${data.BANK}, ${data.BRANCH}`;
                    msg.className = 'form-text text-success fw-bold';
                    ifscInput.classList.add('is-valid');
                    ifscValid = true;
                })
                .catch(() => {
                    msg.innerHTML = `<i class="fas fa-times-circle me-1"></i> Invalid IFSC Code`;
                    msg.className = 'form-text text-danger fw-bold';
                    ifscInput.classList.add('is-invalid');
                });
        }

        document.querySelector('form').addEventListener('submit', function (e) {
            if (!ifscValid) {
                // If bank details tab is active or we forced skipped validation earlier (not recommended)
                // But strictly we should enforce it. 
                // If the user hasn't touched the bank tab yet, they might not have entered IFSC.
                // The 'required' attribute handles empty. We handle invalid format here.
                const ifsc = document.getElementById('ifsc').value;
                if (ifsc && !ifscValid) {
                    e.preventDefault();
                    alert('Please enter a valid IFSC code before submitting.');
                    switchTab('#bank-tab');
                    document.getElementById('ifsc').focus();
                }
            }
        });
    </script>
</body>

</html>