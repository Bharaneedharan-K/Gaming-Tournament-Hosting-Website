<?php
require_once 'config/database.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get user info
$stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get tournaments created by user
$stmt = $db->prepare("SELECT t.*, 
                    (SELECT COUNT(*) FROM tournament_participants WHERE tournament_id = t.tournament_id) as current_participants
                    FROM tournaments t 
                    WHERE t.owner_id = ? 
                    ORDER BY t.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$created_tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get tournaments joined by user
$stmt = $db->prepare("SELECT t.*, tp.is_approved, tp.team_name, tp.transaction_id,
                    (SELECT COUNT(*) FROM tournament_participants WHERE tournament_id = t.tournament_id) as current_participants
                    FROM tournaments t 
                    JOIN tournament_participants tp ON t.tournament_id = tp.tournament_id 
                    WHERE tp.user_id = ? 
                    ORDER BY t.tournament_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$joined_tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get tournaments won by user
$stmt = $db->prepare("SELECT t.*, tw.position, tp.team_name
                    FROM tournaments t 
                    JOIN tournament_winners tw ON t.tournament_id = tw.tournament_id 
                    JOIN tournament_participants tp ON tw.user_id = tp.user_id AND tw.tournament_id = tp.tournament_id
                    WHERE tw.user_id = ? 
                    ORDER BY t.tournament_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$won_tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0">Profile Information</h4>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-user-circle fa-5x text-primary"></i>
                </div>
                <div class="list-group">
                    <div class="list-group-item bg-dark text-light">
                        <div class="d-flex justify-content-between">
                            <span>Username</span>
                            <span><?php echo htmlspecialchars($user['username']); ?></span>
                        </div>
                    </div>
                    <div class="list-group-item bg-dark text-light">
                        <div class="d-flex justify-content-between">
                            <span>Email</span>
                            <span><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                    </div>
                    <div class="list-group-item bg-dark text-light">
                        <div class="d-flex justify-content-between">
                            <span>Points</span>
                            <span><?php echo $user['points']; ?></span>
                        </div>
                    </div>
                    <div class="list-group-item bg-dark text-light">
                        <div class="d-flex justify-content-between">
                            <span>Member Since</span>
                            <span><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0">Tournaments Created</h4>
            </div>
            <div class="card-body">
                <?php if (count($created_tournaments) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-dark">
                            <thead>
                                <tr>
                                    <th>Tournament</th>
                                    <th>Game</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Participants</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($created_tournaments as $tournament): ?>
                                    <tr>
                                        <td>
                                            <a href="tournament_details.php?id=<?php echo $tournament['tournament_id']; ?>" 
                                               class="text-light text-decoration-none">
                                                <?php echo htmlspecialchars($tournament['tournament_name']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($tournament['game_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($tournament['tournament_date'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $tournament['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($tournament['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $tournament['current_participants']; ?>/<?php echo $tournament['max_players']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>You haven't created any tournaments yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0">Tournaments Joined</h4>
            </div>
            <div class="card-body">
                <?php if (count($joined_tournaments) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-dark">
                            <thead>
                                <tr>
                                    <th>Tournament</th>
                                    <th>Game</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Team</th>
                                    <th>Approval</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($joined_tournaments as $tournament): ?>
                                    <tr>
                                        <td>
                                            <a href="tournament_details.php?id=<?php echo $tournament['tournament_id']; ?>" 
                                               class="text-light text-decoration-none">
                                                <?php echo htmlspecialchars($tournament['tournament_name']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($tournament['game_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($tournament['tournament_date'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $tournament['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($tournament['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($tournament['is_team_based']): ?>
                                                <?php echo htmlspecialchars($tournament['team_name']); ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($tournament['is_approved']): ?>
                                                <span class="badge bg-success">Approved</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>You haven't joined any tournaments yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Tournaments Won</h4>
            </div>
            <div class="card-body">
                <?php if (count($won_tournaments) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-dark">
                            <thead>
                                <tr>
                                    <th>Tournament</th>
                                    <th>Game</th>
                                    <th>Date</th>
                                    <th>Position</th>
                                    <th>Team</th>
                                    <th>Prize</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($won_tournaments as $tournament): ?>
                                    <tr>
                                        <td>
                                            <a href="tournament_details.php?id=<?php echo $tournament['tournament_id']; ?>" 
                                               class="text-light text-decoration-none">
                                                <?php echo htmlspecialchars($tournament['tournament_name']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($tournament['game_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($tournament['tournament_date'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $tournament['position'] == 1 ? 'warning' : 
                                                ($tournament['position'] == 2 ? 'secondary' : 'danger'); ?>">
                                                <?php echo $tournament['position']; ?> Place
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($tournament['is_team_based']): ?>
                                                <?php echo htmlspecialchars($tournament['team_name']); ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($tournament['is_paid']): ?>
                                                ₹<?php echo number_format($tournament['winning_prize'], 2); ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>You haven't won any tournaments yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 