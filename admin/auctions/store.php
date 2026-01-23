<?php
session_name('chitfund_admin');
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

$groupId = (int) $_POST['chit_group_id'];
$startTime = $_POST['auction_datetime'];
$endTime = $_POST['auction_end_datetime'];
$startBid = (int) $_POST['starting_bid_amount'];
$status = $_POST['status'];
$auctionType = $_POST['auction_type']; // fetched automatically


/* ❌ Invalid time protection */
if (strtotime($endTime) <= strtotime($startTime)) {
    die('Auction end time must be after start time');
}

/* 🔒 Check group status */
$check = $conn->prepare("
    SELECT status, duration_months
    FROM chit_groups
    WHERE id=?
");
$check->bind_param("i", $groupId);
$check->execute();
$group = $check->get_result()->fetch_assoc();

if ($group['status'] === 'completed') {
    die('Auction cannot be created. Group is completed.');
}

/* 🔢 Calculate next auction month */
$stmt = $conn->prepare("
    SELECT IFNULL(MAX(auction_month),0)+1 AS next_month
    FROM auctions
    WHERE chit_group_id=?
    AND status != 'cancelled'
");
$stmt->bind_param("i", $groupId);
$stmt->execute();

$month = (int) $stmt->get_result()->fetch_assoc()['next_month'];

/* ❌ Prevent exceeding duration */
if ($month > (int) $group['duration_months']) {
    die(
        "Cannot create auction. 
         Maximum {$group['duration_months']} months already scheduled."
    );
}

/* ✅ INSERT AUCTION */
$stmt = $conn->prepare("
    INSERT INTO auctions
    (chit_group_id, auction_month,
     auction_datetime, auction_end_datetime,
     starting_bid_amount, status)
    VALUES (?,?,?,?,?,?)
");

$stmt->bind_param(
    "iissis",
    $groupId,
    $month,
    $startTime,
    $endTime,
    $startBid,
    $status
);

$stmt->execute();

/* 📨 SEND SMS NOTIFICATION */
$members = $conn->query("
    SELECT m.mobile, m.full_name, cg.group_name 
    FROM chit_group_members cgm
    JOIN members m ON m.member_id = cgm.member_id
    JOIN chit_groups cg ON cg.id = cgm.group_id
    WHERE cgm.group_id = $groupId
");

while ($mem = $members->fetch_assoc()) {
    $mobile = $mem['mobile'];
    $msg = "📢 New Auction Alert!
Group: {$mem['group_name']}
Month: $month
Date: " . date('d M Y h:i A', strtotime($startTime)) . "
Log in to participate!";

    // Send SMS 
    sendSMS($mobile, $msg);
}

function sendSMS($mobile, $message)
{
    // 1. Log to file for testing
    $log = "[" . date('Y-m-d H:i:s') . "] To: $mobile | Msg: $message" . PHP_EOL;
    file_put_contents('../../sms_logs.txt', $log, FILE_APPEND);

    // 2. Real API Implementation - UNCOMMENT AND FILL THIS
    /*
    // Example: Fast2SMS, Twilio, etc.
    // $apiKey = "YOUR_API_KEY";
    // $url = "https://www.fast2sms.com/dev/bulkV2?authorization=$apiKey&route=q&message=".urlencode($message)."&flash=0&numbers=$mobile";
    // file_get_contents($url);
    */
}

header("Location: index.php?success=1");
exit;

?>