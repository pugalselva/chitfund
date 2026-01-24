<?php
// session_start();
include '../../config/database.php';
include '../auth.php';


if ($_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

$members = $conn->query("
    SELECT member_id, full_name
    FROM members
    WHERE is_active = 1
");

$groups = $conn->query("
    SELECT id, group_name
    FROM chit_groups
    WHERE is_active = 1
");
?>


<!DOCTYPE html>
<html>

<head>
    <title>Payment Entry</title>
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
                        <h4 class="mb-0 fw-bold">Payment Entry</h4>
                        <small class="text-secondary">Record a monthly contribution</small>
                    </div>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to History
                    </a>
                </div>

                <div class="row g-4">

                    <!-- Left: Form -->
                    <div class="col-12 col-lg-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                                <h5 class="card-title fw-bold text-primary"><i
                                        class="fas fa-file-invoice-dollar me-2"></i>New Transaction</h5>
                            </div>
                            <div class="card-body p-4">
                                <form id="paymentForm">

                                    <div class="form-floating mb-3">
                                        <select name="member_id" id="member_id" class="form-select" required>
                                            <option value="" selected disabled>Select Member</option>
                                            <?php while ($m = $members->fetch_assoc()): ?>
                                                <option value="<?= $m['member_id'] ?>">
                                                    <?= $m['full_name'] ?> (ID: <?= $m['member_id'] ?>)
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                        <label for="member_id">Member *</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <select name="chit_group_id" id="chit_group_id" class="form-select" disabled
                                            required>
                                            <option value="">Select Member First</option>
                                        </select>
                                        <label for="chit_group_id">Chit Group *</label>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="number" name="actual_amount" id="actual_amount"
                                                    class="form-control" placeholder="Amount" readonly required>
                                                <label for="actual_amount">Amount Due (₹)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="date" name="payment_date" id="payment_date"
                                                    class="form-control" value="<?= date('Y-m-d') ?>" required>
                                                <label for="payment_date">Payment Date *</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-floating mb-4">
                                        <select name="payment_mode" class="form-select" required>
                                            <option value="UPI">UPI / GPay / PhonePe</option>
                                            <option value="CASH">Cash</option>
                                            <option value="BANK">Bank Transfer (NEFT/IMPS)</option>
                                        </select>
                                        <label>Payment Mode *</label>
                                    </div>

                                    <div class="d-grid">
                                        <button class="btn btn-success fw-bold py-2" id="submitBtn">
                                            <i class="fas fa-check-circle me-1"></i> Confirm Payment
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Info Panel -->
                    <div class="col-12 col-lg-5">
                        <div class="card border-0 shadow-sm h-100 bg-light">
                            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                                <h5 class="card-title fw-bold text-secondary"><i
                                        class="fas fa-calculator me-2"></i>Breakdown</h5>
                            </div>
                            <div class="card-body p-4">

                                <div id="breakdown-placeholder" class="text-center text-muted py-5">
                                    <i class="fas fa-coins fa-3x mb-3 opacity-25"></i>
                                    <p>Select a member and group to view payment details.</p>
                                </div>

                                <div id="breakdown-content" style="display:none;">
                                    <div class="bg-white p-3 rounded shadow-sm mb-3">
                                        <h6 class="text-secondary text-uppercase fw-bold small">Group Info</h6>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span id="monthDisplay" class="badge bg-info text-dark">Month —</span>
                                            <small id="auctionInfo" class="text-muted text-end"
                                                style="max-width: 60%; line-height: 1.2;"></small>
                                        </div>
                                    </div>

                                    <ul class="list-group list-group-flush rounded shadow-sm mb-4">
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center bg-white">
                                            <span>Installment Amount</span>
                                            <span class="fw-bold">₹<span id="displayAmount">0</span></span>
                                        </li>
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center bg-white">
                                            <span>Dividend / Discount</span>
                                            <span class="text-success fw-bold">- ₹0</span>
                                        </li>
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center bg-light border-top">
                                            <span class="fw-bold text-dark">Total Payable</span>
                                            <span class="fw-bold text-primary fs-5">₹<span
                                                    id="finalDisplay">0</span></span>
                                        </li>
                                    </ul>

                                    <div class="alert alert-warning border-0 small">
                                        <i class="fas fa-info-circle me-1"></i> Please verify the amount before
                                        confirming.
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <?php include '../layout/scripts.php'; ?>
    <script>
        const member = document.getElementById('member_id');
        const group = document.getElementById('chit_group_id');
        const actual = document.getElementById('actual_amount');

        // Breakdown elements
        const breakdownPlaceholder = document.getElementById('breakdown-placeholder');
        const breakdownContent = document.getElementById('breakdown-content');
        const monthDisplay = document.getElementById('monthDisplay');
        const auctionInfo = document.getElementById('auctionInfo');
        const displayAmount = document.getElementById('displayAmount');
        const finalDisplay = document.getElementById('finalDisplay');

        /* Load groups */
        member.addEventListener('change', () => {
            group.innerHTML = '<option value="">Loading...</option>';
            group.disabled = true;

            fetch(`fetch_member_groups.php?member_id=${member.value}`)
                .then(r => r.json())
                .then(data => {
                    group.innerHTML = '<option value="" selected disabled>Select Chit Group</option>';
                    if (data.length > 0) {
                        data.forEach(g => {
                            group.innerHTML += `<option value="${g.id}">${g.group_name}</option>`;
                        });
                        group.disabled = false;
                    } else {
                        group.innerHTML = '<option value="">No active groups found</option>';
                    }
                });
        });

        /* Load amount */
        group.addEventListener('change', () => {
            // Show loading state if needed
            if (!group.value) return;

            fetch(`fetch_payment_amount.php?group_id=${group.value}&member_id=${member.value}`)
                .then(r => r.json())
                .then(res => {
                    if (res.error) {
                        alert(res.error);
                        group.value = "";
                        return;
                    }

                    // Populate fields
                    actual.value = res.amount;
                    breakdownPlaceholder.style.display = 'none';
                    breakdownContent.style.display = 'block';

                    // Update breakdown
                    monthDisplay.innerText = `Month ${res.month}`;
                    auctionInfo.innerText = `Previous Auction: ₹${res.total}`;
                    displayAmount.innerText = res.amount; // Assuming this is final after calc
                    finalDisplay.innerText = res.amount;
                });
        });

        // Submit
        document.getElementById('paymentForm').addEventListener('submit', e => {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';

            fetch('store.php', {
                method: 'POST',
                body: new FormData(e.target)
            })
                .then(res => res.text())
                .then(result => {
                    if (result.trim() === 'success') {
                        // Success animation or redirect
                        btn.className = "btn btn-success fw-bold py-2 w-100";
                        btn.innerHTML = '<i class="fas fa-check me-2"></i> Paid Successfully!';
                        setTimeout(() => {
                            window.location.href = 'index.php';
                        }, 1000);
                    } else {
                        alert(result);
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                })
                .catch(() => {
                    alert("Request failed.");
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        });
    </script>
</body>

</html>