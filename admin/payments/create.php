<?php
session_start();
include '../../config/database.php';

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
                    <form id="paymentForm">
                        <div class="form-group">
                            <label>Member *</label>
                            <select name="member_id" class="form-control" required>
                                <option value="">Select member</option>
                                <?php while($m = $members->fetch_assoc()): ?>
                                <option value="<?= $m['member_id'] ?>">
                                    <?= $m['full_name'] ?> (<?= $m['member_id'] ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Chit Group *</label>
                            <select name="chit_group_id" class="form-control" required>
                                <option value="">Select chit group</option>
                                <?php while($g = $groups->fetch_assoc()): ?>
                                <option value="<?= $g['id'] ?>">
                                    <?= $g['group_name'] ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Actual Amount *</label>
                                <input type="number" name="actual_amount" required>
                            </div>

                            <div class="form-group">
                                <label>Discount Amount</label>
                                <input type="number" name="discount_amount" value="0">
                                <small>Discount from auction winnings</small>
                            </div>
                        </div>
                        <div class="amount-box">
                            <span>Final Amount to Collect:</span>
                            <b>₹<span id="final">0</span></b>
                        </div>
                        <div class="form-group">
                            <label>Payment Mode *</label>
                            <select name="payment_mode"class="form-control" required>
                                <option value="UPI">UPI</option>
                                <option value="CASH">Cash</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Payment Date *</label>
                            <input type="date" name="payment_date" required>
                        </div>

                        <br>
                        <button type="submit" class="btn-primary">Record Payment</button>
                        <a href="index.php">
                            <button type="button" class="btn-secondary">Cancel</button>
                        </a>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        function calculate() {
            let actual = Number(document.getElementById('actual').value);
            let discount = Number(document.getElementById('discount').value);
            document.getElementById('final').innerText = actual - discount;
        }
        document.getElementById('actual').oninput = calculate;
        document.getElementById('discount').oninput = calculate;
        calculate();
    </script>
    <script>
        document.getElementById('paymentForm').addEventListener('submit', e => {
            e.preventDefault();

            fetch('store.php', {
                    method: 'POST',
                    body: new FormData(e.target)
                })
                .then(res => res.text())
                .then(result => {
                    if (result === 'success') {
                        alert('Payment recorded');
                        window.location.href = 'index.php';
                    } else {
                        alert(result);
                    }
                });
        });
    </script>

</body>

</html>
