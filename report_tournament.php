<?php
require_once 'config/database.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['tournament_id']) || !isset($_POST['report_reason'])) {
    header("Location: index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Verify tournament exists and is active
$stmt = $db->prepare("SELECT * FROM tournaments WHERE tournament_id = ? AND status = 'active'");
$stmt->execute([$_POST['tournament_id']]);
$tournament = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tournament) {
    header("Location: index.php");
    exit();
}

// Check if user has already reported this tournament
$stmt = $db->prepare("SELECT report_id FROM tournament_reports 
                    WHERE tournament_id = ? AND reporter_id = ? 
                    AND reported_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
$stmt->execute([$_POST['tournament_id'], $_SESSION['user_id']]);
if ($stmt->rowCount() > 0) {
    $_SESSION['error'] = "You have already reported this tournament in the last 24 hours.";
    header("Location: tournament_details.php?id=" . $_POST['tournament_id']);
    exit();
}

// Insert report
$stmt = $db->prepare("INSERT INTO tournament_reports (tournament_id, reporter_id, report_reason) VALUES (?, ?, ?)");
if ($stmt->execute([$_POST['tournament_id'], $_SESSION['user_id'], $_POST['report_reason']])) {
    $_SESSION['success'] = "Tournament reported successfully. Our team will review the report.";
} else {
    $_SESSION['error'] = "Failed to submit report. Please try again.";
}

header("Location: tournament_details.php?id=" . $_POST['tournament_id']);
exit();
?> 