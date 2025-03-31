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