<?php
require_once 'config/database.php';
require_once 'includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get tournament details
$stmt = $db->prepare("SELECT t.*, u.username as owner_name, 
                    (SELECT COUNT(*) FROM tournament_participants WHERE tournament_id = t.tournament_id) as current_participants
                    FROM tournaments t 
                    JOIN users u ON t.owner_id = u.user_id 
                    WHERE t.tournament_id = ?");
$stmt->execute([$_GET['id']]);
$tournament = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tournament) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

// Handle join request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $transaction_id = isset($_POST['transaction_id']) ? trim($_POST['transaction_id']) : null;
    $team_name = isset($_POST['team_name']) ? trim($_POST['team_name']) : null;

    // Validate paid tournament requirements first
    if ($tournament['is_paid'] && empty($transaction_id)) {
        $error = "Transaction ID is required for paid tournaments";
    }

    // Validate team-based requirements
    if ($tournament['is_team_based'] && empty($team_name)) {
        $error = "Team name is required for team-based tournaments";
    }

    // Check if already joined
    if (empty($error)) {
        $stmt = $db->prepare("SELECT participant_id FROM tournament_participants 
                            WHERE tournament_id = ? AND user_id = ?");
        $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
        if ($stmt->rowCount() > 0) {
            $error = "You have already joined this tournament";
        } else {
            // Check if tournament is full
            if ($tournament['current_participants'] >= $tournament['max_players']) {
                $error = "This tournament is full";
            } else {
                $stmt = $db->prepare("INSERT INTO tournament_participants 
                                    (tournament_id, user_id, team_name, transaction_id, is_approved) 
                                    VALUES (?, ?, ?, ?, ?)");
                
                $is_approved = $tournament['auto_approval'] ? 1 : 0;
                
                if ($stmt->execute([$_GET['id'], $_SESSION['user_id'], $team_name, $transaction_id, $is_approved])) {
                    $success = "Successfully joined the tournament!";
                    // Refresh the page to update the participant count and status
                    header("Location: tournament_details.php?id=" . $_GET['id']);
                    exit();
                } else {
                    $error = "Failed to join tournament. Please try again.";
                }
            }
        }
    }
}

// Get participants
$stmt = $db->prepare("SELECT tp.*, u.username, 
                    CASE 
                        WHEN tp.status = 'approved' OR tp.is_approved = 1 THEN 'Approved'
                        WHEN tp.status = 'rejected' OR tp.is_approved = 0 THEN 'Pending'
                        ELSE 'Pending'
                    END as status
                    FROM tournament_participants tp 
                    JOIN users u ON tp.user_id = u.user_id 
                    WHERE tp.tournament_id = ?
                    ORDER BY CASE 
                        WHEN tp.status = 'approved' OR tp.is_approved = 1 THEN 1
                        ELSE 2
                    END, tp.joined_at ASC");
$stmt->execute([$_GET['id']]);
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if user has joined the tournament
$has_joined = false;
$is_approved = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("SELECT tp.is_approved, tp.status 
                        FROM tournament_participants tp
                        WHERE tp.tournament_id = ? AND tp.user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($participant) {
        $has_joined = true;
        $is_approved = ($participant['status'] === 'approved' || $participant['is_approved'] == 1);
    }
}
?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0"><?php echo htmlspecialchars($tournament['tournament_name']); ?></h4>
            </div>
            <div class="card-body">
                <div class="tournament-info mb-4">
                    <p><i class="fas fa-gamepad me-2"></i>Game: <?php echo htmlspecialchars($tournament['game_name']); ?></p>
                    <p><i class="fas fa-calendar me-2"></i>Date: <?php echo date('M d, Y H:i', strtotime($tournament['tournament_date'])); ?></p>
                    <p><i class="fas fa-users me-2"></i>Players: <?php echo $tournament['current_participants']; ?>/<?php echo $tournament['max_players']; ?></p>
                    <?php if ($tournament['is_team_based']): ?>
                        <p><i class="fas fa-users-cog me-2"></i>Team Size: <?php echo $tournament['team_size']; ?></p>
                        <p><i class="fas fa-layer-group me-2"></i>Max Teams: <?php echo $tournament['max_teams']; ?></p>
                    <?php endif; ?>
                    <?php if ($tournament['is_paid']): ?>
                        <p><i class="fas fa-trophy me-2"></i>Prize: ₹<?php echo number_format($tournament['winning_prize'], 2); ?></p>
                        <p><i class="fas fa-money-bill-wave me-2"></i>Registration Fee: ₹<?php echo number_format($tournament['registration_fee'], 2); ?></p>
                    <?php endif; ?>
                    <p><i class="fas fa-user me-2"></i>Organizer: <?php echo htmlspecialchars($tournament['owner_name']); ?></p>
                </div>

                <div class="contact-info mb-4">
                    <h5>Contact Information</h5>
                    <p><?php echo nl2br(htmlspecialchars($tournament['contact_info'])); ?></p>
                </div>

                <?php if ($tournament['is_paid']): ?>
                    <div class="payment-info mb-4">
                        <h5>Payment Information</h5>
                        <p><i class="fas fa-qrcode me-2"></i>UPI ID: <?php echo htmlspecialchars($tournament['upi_id']); ?></p>
                        <p>Please make the payment and enter the transaction ID when joining.</p>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $tournament['owner_id']): ?>
                    <?php if ($has_joined): ?>
                        <?php if ($is_approved): ?>
                            <div class="room-info mb-4">
                                <h5>Room Details</h5>
                                <div class="alert alert-info">
                                    <p><strong>Room ID:</strong> <?php echo htmlspecialchars($tournament['room_id']); ?></p>
                                    <p><strong>Room Password:</strong> <?php echo htmlspecialchars($tournament['room_password']); ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                Your participation is pending approval. Room details will be visible once approved.
                            </div>
                        <?php endif; ?>
                    <?php elseif ($tournament['current_participants'] < $tournament['max_players']): ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <?php if ($tournament['is_team_based']): ?>
                                <div class="mb-3">
                                    <label for="team_name" class="form-label">Team Name</label>
                                    <input type="text" class="form-control" id="team_name" name="team_name" required>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($tournament['is_paid']): ?>
                                <div class="mb-3">
                                    <label for="transaction_id" class="form-label">Transaction ID</label>
                                    <input type="text" class="form-control" id="transaction_id" name="transaction_id" required>
                                    <small class="text-muted">Please make the payment to UPI ID: <?php echo htmlspecialchars($tournament['upi_id']); ?> before joining</small>
                                </div>
                            <?php endif; ?>

                            <button type="submit" class="btn btn-primary">Join Tournament</button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning">This tournament is full.</div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Participants</h4>
            </div>
            <div class="card-body">
                <?php if (count($participants) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-dark">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <?php if ($tournament['is_team_based']): ?>
                                        <th>Team Name</th>
                                    <?php endif; ?>
                                    <th>Status</th>
                                    <th>Joined At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($participants as $participant): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($participant['username']); ?></td>
                                        <?php if ($tournament['is_team_based']): ?>
                                            <td><?php echo htmlspecialchars($participant['team_name']); ?></td>
                                        <?php endif; ?>
                                        <td>
                                            <span class="badge bg-<?php echo $participant['status'] == 'Approved' ? 'success' : 'warning'; ?>">
                                                <?php echo $participant['status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($participant['joined_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>No participants have joined yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $tournament['owner_id']): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Tournament Management</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="manage_participants.php?id=<?php echo $tournament['tournament_id']; ?>" 
                           class="btn btn-primary">Manage Participants</a>
                        <a href="announce_winners.php?id=<?php echo $tournament['tournament_id']; ?>" 
                           class="btn btn-success">Announce Winners</a>
                        <a href="end_tournament.php?id=<?php echo $tournament['tournament_id']; ?>" 
                           class="btn btn-danger">End Tournament</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $tournament['owner_id']): ?>
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Report Tournament</h4>
                </div>
                <div class="card-body">
                    <form action="report_tournament.php" method="POST">
                        <input type="hidden" name="tournament_id" value="<?php echo $tournament['tournament_id']; ?>">
                        <div class="mb-3">
                            <label for="report_reason" class="form-label">Reason for Report</label>
                            <textarea class="form-control" id="report_reason" name="report_reason" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger">Submit Report</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 