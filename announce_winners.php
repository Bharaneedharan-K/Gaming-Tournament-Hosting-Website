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
$stmt = $db->prepare("SELECT * FROM tournaments WHERE tournament_id = ? AND owner_id = ?");
$stmt->execute([$_GET['id'], $_SESSION['user_id']]);
$tournament = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tournament) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

// Handle winner announcement
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $winner_id = $_POST['winner_id'];
    $position = $_POST['position'];

    // Check if position is already taken
    $stmt = $db->prepare("SELECT winner_id FROM tournament_winners 
                        WHERE tournament_id = ? AND position = ?");
    $stmt->execute([$_GET['id'], $position]);
    if ($stmt->rowCount() > 0) {
        $error = "This position has already been assigned";
    } else {
        // Check if player is already a winner
        $stmt = $db->prepare("SELECT winner_id FROM tournament_winners 
                            WHERE tournament_id = ? AND user_id = ?");
        $stmt->execute([$_GET['id'], $winner_id]);
        if ($stmt->rowCount() > 0) {
            $error = "This player has already been assigned a position";
        } else {
            // Get participant details
            $stmt = $db->prepare("SELECT team_name FROM tournament_participants 
                                WHERE tournament_id = ? AND user_id = ?");
            $stmt->execute([$_GET['id'], $winner_id]);
            $participant = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Insert winner
            $stmt = $db->prepare("INSERT INTO tournament_winners 
                                (tournament_id, user_id, team_name, position) 
                                VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$_GET['id'], $winner_id, $participant['team_name'], $position])) {
                $success = "Winner announced successfully!";
            } else {
                $error = "Failed to announce winner.";
            }
        }
    }
}

// Get approved participants who are not already winners
$stmt = $db->prepare("SELECT tp.*, u.username, u.user_id
                    FROM tournament_participants tp 
                    JOIN users u ON tp.user_id = u.user_id 
                    WHERE tp.tournament_id = ? 
                    AND (tp.status = 'approved' OR tp.is_approved = 1)
                    AND NOT EXISTS (
                        SELECT 1 FROM tournament_winners tw 
                        WHERE tw.tournament_id = tp.tournament_id 
                        AND tw.user_id = tp.user_id
                    )
                    ORDER BY u.username ASC");
$stmt->execute([$_GET['id']]);
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get current winners (ensure unique winners)
$stmt = $db->prepare("SELECT DISTINCT tw.position, tw.user_id, u.username, tp.team_name 
                    FROM tournament_winners tw 
                    JOIN tournament_participants tp ON tw.user_id = tp.user_id AND tp.tournament_id = tw.tournament_id
                    JOIN users u ON tw.user_id = u.user_id 
                    WHERE tw.tournament_id = ? 
                    ORDER BY tw.position");
$stmt->execute([$_GET['id']]);
$winners = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0">Announce Winners - <?php echo htmlspecialchars($tournament['tournament_name']); ?></h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if (count($participants) > 0): ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="winner_id" class="form-label">Select Winner</label>
                            <select class="form-select" id="winner_id" name="winner_id" required>
                                <option value="">Choose a participant</option>
                                <?php foreach ($participants as $participant): ?>
                                    <option value="<?php echo $participant['user_id']; ?>">
                                        <?php echo htmlspecialchars($participant['username']); ?>
                                        <?php if ($tournament['is_team_based'] && !empty($participant['team_name'])): ?>
                                            (<?php echo htmlspecialchars($participant['team_name']); ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="position" class="form-label">Position</label>
                            <select class="form-select" id="position" name="position" required>
                                <option value="">Select position</option>
                                <option value="1">1st Place</option>
                                <option value="2">2nd Place</option>
                                <option value="3">3rd Place</option>
                                <option value="4">4th Place</option>
                                <option value="5">5th Place</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Announce Winner</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>No approved participants available to select as winners.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Current Winners</h4>
            </div>
            <div class="card-body">
                <?php if (count($winners) > 0): ?>
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
                <?php else: ?>
                    <p>No winners announced yet.</p>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="tournament_details.php?id=<?php echo $_GET['id']; ?>" class="btn btn-primary">Back to Tournament</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 