<?php
$conn->query("
    UPDATE payments
    SET status='overdue'
    WHERE status='pending'
    AND due_date < CURDATE()
");
?>