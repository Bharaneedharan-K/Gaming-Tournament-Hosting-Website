<?php
require_once 'config/database.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get user's points
$stmt = $db->prepare("SELECT points FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_points = $stmt->fetch(PDO::FETCH_ASSOC)['points'];

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tournament_name = trim($_POST['tournament_name']);
    $game_name = $_POST['game_name'];
    $tournament_date = $_POST['tournament_date'];
    $max_players = (int)$_POST['max_players'];
    $is_team_based = isset($_POST['is_team_based']) ? 1 : 0;
    $team_size = $is_team_based ? (int)$_POST['team_size'] : null;
    $max_teams = $is_team_based ? (int)$_POST['max_teams'] : null;
    $room_id = trim($_POST['room_id']);
    $room_password = trim($_POST['room_password']);
    $is_paid = isset($_POST['is_paid']) ? 1 : 0;
    $registration_fee = $is_paid ? (float)$_POST['registration_fee'] : null;
    $winning_prize = $is_paid ? (float)$_POST['winning_prize'] : null;
    $upi_id = $is_paid ? trim($_POST['upi_id']) : null;
    $contact_info = trim($_POST['contact_info']);
    $auto_approval = isset($_POST['auto_approval']) ? 1 : 0;

    // Validate input
    if (empty($tournament_name) || empty($game_name) || empty($tournament_date) || empty($max_players)) {
        $error = "Required fields cannot be empty";
    } elseif ($is_team_based && (empty($team_size) || empty($max_teams))) {
        $error = "Team size and max teams are required for team-based tournaments";
    } elseif ($is_paid) {
        if ($user_points < 1000) {
            $error = "You need at least 1000 points to create a paid tournament";
        } elseif (empty($registration_fee) || empty($winning_prize) || empty($upi_id)) {
            $error = "Registration fee, winning prize, and UPI ID are required for paid tournaments";
        } else {
            // Insert tournament
            $stmt = $db->prepare("INSERT INTO tournaments (owner_id, tournament_name, game_name, tournament_date, 
                max_players, is_team_based, team_size, max_teams, room_id, room_password, is_paid, 
                registration_fee, winning_prize, upi_id, contact_info, auto_approval) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$_SESSION['user_id'], $tournament_name, $game_name, $tournament_date, 
                $max_players, $is_team_based, $team_size, $max_teams, $room_id, $room_password, 
                $is_paid, $registration_fee, $winning_prize, $upi_id, $contact_info, $auto_approval])) {
                $success = "Tournament created successfully!";
            } else {
                $error = "Failed to create tournament. Please try again.";
            }
        }
    } else {
        // Insert tournament for free tournaments
        $stmt = $db->prepare("INSERT INTO tournaments (owner_id, tournament_name, game_name, tournament_date, 
            max_players, is_team_based, team_size, max_teams, room_id, room_password, is_paid, 
            registration_fee, winning_prize, upi_id, contact_info, auto_approval) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$_SESSION['user_id'], $tournament_name, $game_name, $tournament_date, 
            $max_players, $is_team_based, $team_size, $max_teams, $room_id, $room_password, 
            $is_paid, $registration_fee, $winning_prize, $upi_id, $contact_info, $auto_approval])) {
            $success = "Tournament created successfully!";
        } else {
            $error = "Failed to create tournament. Please try again.";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Create New Tournament</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="tournament_name" class="form-label">Tournament Name</label>
                        <input type="text" class="form-control" id="tournament_name" name="tournament_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="game_name" class="form-label">Game Name</label>
                        <select class="form-select" id="game_name" name="game_name" required>
                            <option value="">Select Game</option>
                            <option value="Among Us">Among Us</option>
                            <option value="Minecraft">Minecraft</option>
                            <option value="Free Fire">Free Fire</option>
                            <option value="BGMI">BGMI</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tournament_date" class="form-label">Tournament Date</label>
                        <input type="datetime-local" class="form-control" id="tournament_date" name="tournament_date" required>
                    </div>

                    <div class="mb-3">
                        <label for="max_players" class="form-label">Maximum Players</label>
                        <input type="number" class="form-control" id="max_players" name="max_players" min="2" required>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_team_based" name="is_team_based">
                            <label class="form-check-label" for="is_team_based">Team-based Tournament</label>
                        </div>
                    </div>

                    <div id="team_fields" style="display: none;">
                        <div class="mb-3">
                            <label for="team_size" class="form-label">Team Size</label>
                            <input type="number" class="form-control" id="team_size" name="team_size" min="2">
                        </div>
                        <div class="mb-3">
                            <label for="max_teams" class="form-label">Maximum Teams</label>
                            <input type="number" class="form-control" id="max_teams" name="max_teams" min="2">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="room_id" class="form-label">Room ID</label>
                        <input type="text" class="form-control" id="room_id" name="room_id" required>
                    </div>

                    <div class="mb-3">
                        <label for="room_password" class="form-label">Room Password</label>
                        <input type="text" class="form-control" id="room_password" name="room_password" required>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_paid" name="is_paid" <?php echo $user_points < 1000 ? 'disabled' : ''; ?>>
                            <label class="form-check-label" for="is_paid">Paid Tournament</label>
                            <small class="text-muted d-block">(Requires 1000+ points)</small>
                            <?php if ($user_points < 1000): ?>
                                <div class="alert alert-warning mt-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Your current points (<?php echo $user_points; ?>) are less than 1000. You need at least 1000 points to create paid tournaments.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div id="paid_fields" style="display: none;">
                        <div class="mb-3">
                            <label for="registration_fee" class="form-label">Registration Fee (₹)</label>
                            <input type="number" class="form-control" id="registration_fee" name="registration_fee" min="0" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label for="winning_prize" class="form-label">Winning Prize (₹)</label>
                            <input type="number" class="form-control" id="winning_prize" name="winning_prize" min="0" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label for="upi_id" class="form-label">UPI ID</label>
                            <input type="text" class="form-control" id="upi_id" name="upi_id">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="contact_info" class="form-label">Contact Information</label>
                        <textarea class="form-control" id="contact_info" name="contact_info" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="auto_approval" name="auto_approval">
                            <label class="form-check-label" for="auto_approval">Auto-approve Participants</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Tournament</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('is_team_based').addEventListener('change', function() {
    document.getElementById('team_fields').style.display = this.checked ? 'block' : 'none';
});

const userPoints = <?php echo $user_points; ?>;
const isPaidCheckbox = document.getElementById('is_paid');

isPaidCheckbox.addEventListener('change', function() {
    document.getElementById('paid_fields').style.display = this.checked ? 'block' : 'none';
});

if (userPoints < 1000) {
    isPaidCheckbox.addEventListener('click', function(e) {
        if (!this.disabled) {
            e.preventDefault();
            alert('Your points (' + userPoints + ') are less than 1000. You need at least 1000 points to create paid tournaments.');
        }
    });
}
</script>

<?php require_once 'includes/footer.php'; ?> 