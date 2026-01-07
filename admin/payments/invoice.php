<?php
if (!isset($_GET['receipt'])) {
    die('Invalid invoice');
}
$receipt = htmlspecialchars($_GET['receipt']);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Invoice <?= $receipt ?></title>

    <style>
        /* ===== A4 PRINT ===== */
        @page {
            size: A4;
            margin: 20mm;
        }

        body {
            font-family: "Segoe UI", sans-serif;
            background: #f3f4f6;
            padding: 20px;
        }

        .invoice {
            width: 210mm;
            background: #fff;
            padding: 24px;
            margin: auto;
            border: 1px solid #e5e7eb;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 12px;
        }

        .invoice-title {
            font-size: 22px;
            font-weight: 700;
        }

        .invoice-meta {
            font-size: 14px;
            color: #374151;
        }

        .invoice-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        .info-box {
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            font-size: 14px;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }

        .total-box {
            margin-top: 20px;
            text-align: right;
            font-size: 16px;
        }

        .print-btn {
            margin-bottom: 15px;
            background: #111827;
            color: #fff;
            padding: 10px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
        }

        @media print {
            .print-btn {
                display: none;
            }

            body {
                background: #fff;
                padding: 0;
            }

            .invoice {
                border: none;
            }
        }
    </style>
</head>

<body>

    <button class="print-btn" onclick="window.print()">
        🖨 Print Invoice
    </button>

    <div class="invoice" id="invoiceBox">
        <div class="invoice-header">
            <div>
                <div class="invoice-title">Payment Invoice</div>
                <small>Receipt: <?= $receipt ?></small>
            </div>
            <div class="invoice-meta">
                Date: <span id="invDate">—</span>
            </div>
        </div>

        <div class="invoice-info">
            <div class="info-box">
                <b>Member</b><br>
                <span id="memberName"></span>
            </div>

            <div class="info-box">
                <b>Chit Group</b><br>
                <span id="groupName"></span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Monthly Contribution</td>
                    <td id="actualAmount"></td>
                </tr>
                <tr>
                    <td>Discount</td>
                    <td id="discountAmount"></td>
                </tr>
                <tr>
                    <th>Total Payable</th>
                    <th id="finalAmount"></th>
                </tr>
            </tbody>
        </table>

        <div class="total-box">
            <b>Payment Mode:</b> <span id="payMode"></span><br>
            <b>Status:</b> <span id="payStatus"></span>
        </div>

    </div>

    <script>
        fetch('get-invoice.php?receipt=<?= $receipt ?>')
            .then(res => res.json())
            .then(d => {

                document.getElementById('invDate').innerText = d.payment_date;
                document.getElementById('memberName').innerText = d.full_name;
                document.getElementById('groupName').innerText = d.group_name;

                document.getElementById('actualAmount').innerText = '₹' + d.actual_amount;
                document.getElementById('discountAmount').innerText = '-₹' + d.discount_amount;
                document.getElementById('finalAmount').innerText = '₹' + d.final_amount;

                document.getElementById('payMode').innerText = d.payment_mode;
                document.getElementById('payStatus').innerText = d.status.toUpperCase();
            });
    </script>

</body>

</html>
