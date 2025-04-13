<?php
require_once 'config/database.php';
require_once 'includes/header.php';

if (!isset($_GET['id']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Verify tournament ownership
$stmt = $db->prepare("SELECT t.*, 
                    (SELECT COUNT(*) FROM tournament_participants WHERE tournament_id = t.tournament_id) as current_participants 
                    FROM tournaments t 
                    WHERE t.tournament_id = ? AND t.owner_id = ?");
$stmt->execute([$_GET['id'], $_SESSION['user_id']]);
$tournament = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tournament) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

// Handle tournament end
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if all winners are announced
    $stmt = $db->prepare("SELECT COUNT(*) as winner_count FROM tournament_winners WHERE tournament_id = ?");
    $stmt->execute([$_GET['id']]);
    $winner_count = $stmt->fetch(PDO::FETCH_ASSOC)['winner_count'];

    if ($winner_count == 0) {
        $error = "Please announce winners before ending the tournament";
    } else {
        // Update tournament status
        $stmt = $db->prepare("UPDATE tournaments SET status = 'completed' WHERE tournament_id = ?");
        if ($stmt->execute([$_GET['id']])) {
            // Check for valid reports within 24 hours
            $stmt = $db->prepare("SELECT COUNT(*) as report_count FROM tournament_reports 
                                WHERE tournament_id = ? AND is_valid = 1 
                                AND reported_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
            $stmt->execute([$_GET['id']]);
            $report_count = $stmt->fetch(PDO::FETCH_ASSOC)['report_count'];

            if ($report_count > 0) {
                // Deduct points from owner
                $stmt = $db->prepare("UPDATE users SET points = points - 75 WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
            } else {
                // Add points to owner
                $stmt = $db->prepare("UPDATE users SET points = points + 100 WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
            }

            $success = "Tournament ended successfully!";
        } else {
            $error = "Failed to end tournament.";
        }
    }
}

// Get winners
$stmt = $db->prepare("SELECT DISTINCT tw.position, tw.user_id, u.username, tp.team_name 
                    FROM tournament_winners tw 
                    JOIN tournament_participants tp ON tw.user_id = tp.user_id AND tp.tournament_id = tw.tournament_id
                    JOIN users u ON tw.user_id = u.user_id 
                    WHERE tw.tournament_id = ? 
                    ORDER BY tw.position");
$stmt->execute([$_GET['id']]);
$winners = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get reports
$stmt = $db->prepare("SELECT tr.*, u.username 
                    FROM tournament_reports tr 
                    JOIN users u ON tr.reporter_id = u.user_id 
                    WHERE tr.tournament_id = ? 
                    AND tr.reported_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
$stmt->execute([$_GET['id']]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0">End Tournament - <?php echo htmlspecialchars($tournament['tournament_name']); ?></h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <div class="alert alert-warning">
                    <h5>Important Notes:</h5>
                    <ul class="mb-0">
                        <li>Make sure all winners are announced before ending the tournament.</li>
                        <li>This action cannot be undone.</li>
                        <li>Points will be awarded/deducted based on tournament reports.</li>
                    </ul>
                </div>

                <?php if (count($winners) > 0): ?>
                    <div class="mb-4">
                        <h5>Tournament Winners</h5>
                        <div class="list-group">
                            <?php foreach ($winners as $winner): ?>
                                <div class="list-group-item bg-dark text-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0"><?php echo $winner['position']; ?> Place</h6>
                                            <small><?php echo htmlspecialchars($winner['username']); ?></small>
                                            <?php if ($tournament['is_team_based']): ?>
                                                <br>
                                                <small class="text-muted">Team: <?php echo htmlspecialchars($winner['team_name']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($tournament['is_paid']): ?>
                                            <div class="text-end">
                                                <small class="text-success">₹<?php echo number_format($tournament['winning_prize'], 2); ?></small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        Please announce winners before ending the tournament.
                    </div>
                <?php endif; ?>

                <?php if (count($reports) > 0): ?>
                    <div class="mb-4">
                        <h5>Recent Reports (Last 24 Hours)</h5>
                        <div class="list-group">
                            <?php foreach ($reports as $report): ?>
                                <div class="list-group-item bg-dark text-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">Reported by: <?php echo htmlspecialchars($report['username']); ?></h6>
                                            <small><?php echo nl2br(htmlspecialchars($report['report_reason'])); ?></small>
                                        </div>
                                        <div>
                                            <?php if ($report['is_valid']): ?>
                                                <span class="badge bg-danger">Valid</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (count($winners) > 0): ?>
                    <form method="POST" action="">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to end this tournament? This action cannot be undone.')">
                            End Tournament
                        </button>
                    </form>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="tournament_details.php?id=<?php echo $_GET['id']; ?>" class="btn btn-primary">Back to Tournament</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Tournament Summary</h4>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <div class="list-group-item bg-dark text-light">
                        <div class="d-flex justify-content-between">
                            <span>Total Participants</span>
                            <span><?php echo $tournament['current_participants']; ?>/<?php echo $tournament['max_players']; ?></span>
                        </div>
                    </div>
                    <div class="list-group-item bg-dark text-light">
                        <div class="d-flex justify-content-between">
                            <span>Tournament Type</span>
                            <span><?php echo $tournament['is_paid'] ? 'Paid' : 'Free'; ?></span>
                        </div>
                    </div>
                    <?php if ($tournament['is_paid']): ?>
                        <div class="list-group-item bg-dark text-light">
                            <div class="d-flex justify-content-between">
                                <span>Registration Fee</span>
                                <span>₹<?php echo number_format($tournament['registration_fee'], 2); ?></span>
                            </div>
                        </div>
                        <div class="list-group-item bg-dark text-light">
                            <div class="d-flex justify-content-between">
                                <span>Winning Prize</span>
                                <span>₹<?php echo number_format($tournament['winning_prize'], 2); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="list-group-item bg-dark text-light">
                        <div class="d-flex justify-content-between">
                            <span>Reports (24h)</span>
                            <span><?php echo count($reports); ?></span>
                        </div>
                    </div>
                    <div class="list-group-item bg-dark text-light">
                        <div class="d-flex justify-content-between">
                            <span>Points Impact</span>
                            <span><?php echo count($reports) > 0 ? '-75' : '+100'; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 