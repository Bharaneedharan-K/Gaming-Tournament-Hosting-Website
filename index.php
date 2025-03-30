<?php
require_once 'config/database.php';
require_once 'includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Define game images mapping with URLs
$game_images = [
    'among us' => 'https://www.innersloth.com/wp-content/uploads/2024/06/2024roles_nologo.png',
    'minecraft' => 'https://m.economictimes.com/thumb/msid-98433841,width-1600,height-900,resizemode-4,imgsize-12430/minecraft-mods-how-to-install.jpg',
    'free fire' => 'https://static-cdn.jtvnw.net/jtv_user_pictures/43aa2943-730e-427c-bcec-6cdef4d4c4d1-profile_banner-480.jpeg',
    'bgmi' => 'https://images.firstpost.com/wp-content/uploads/2022/07/Explained-Why-Google-and-Apple-removed-BGMI-from-their-respective-app-stores-2-years-after-PUBG-ban-2.jpg'
];

// Get featured tournaments
$stmt = $db->query("SELECT t.*, u.username as owner_name, 
                    (SELECT COUNT(*) FROM tournament_participants WHERE tournament_id = t.tournament_id) as current_participants
                    FROM tournaments t 
                    JOIN users u ON t.owner_id = u.user_id 
                    WHERE t.status = 'active' 
                    ORDER BY t.created_at DESC LIMIT 4");
$featured_tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user info if logged in
$user_info = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card bg-dark text-light">
                <div class="card-header bg-dark text-light">
                    <h4 class="mb-0">Featured Tournaments</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($featured_tournaments)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>No featured tournaments available at the moment.
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($featured_tournaments as $tournament): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 bg-dark text-light">
                                        <?php 
                                        $game_name = strtolower(trim($tournament['game_name']));
                                        // Debug output
                                        error_log("Game name from DB: " . $tournament['game_name']);
                                        error_log("Processed game name: " . $game_name);
                                        $image_path = isset($game_images[$game_name]) ? $game_images[$game_name] : 'https://via.placeholder.com/800x400?text=Game+Image';
                                        ?>
                                        <img src="<?php echo $image_path; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($tournament['game_name']); ?>" style="height: 150px; object-fit: cover;">
                                        <div class="card-body">
                                            <h5 class="card-title text-light"><?php echo htmlspecialchars($tournament['tournament_name']); ?></h5>
                                            <p class="card-text text-light">
                                                <i class="fas fa-gamepad me-2"></i><?php echo htmlspecialchars($tournament['game_name']); ?><br>
                                                <i class="fas fa-calendar me-2"></i><?php echo date('M d, Y', strtotime($tournament['tournament_date'])); ?><br>
                                                <i class="fas fa-users me-2"></i><?php echo $tournament['current_participants']; ?> participants
                                            </p>
                                            <?php if ($tournament['is_paid']): ?>
                                                <p class="card-text text-light">
                                                    <i class="fas fa-trophy me-2"></i>Prize: $<?php echo number_format($tournament['winning_prize'], 2); ?><br>
                                                    <i class="fas fa-ticket-alt me-2"></i>Entry: $<?php echo number_format($tournament['registration_fee'], 2); ?>
                                                </p>
                                            <?php endif; ?>
                                            <a href="tournament_details.php?id=<?php echo $tournament['tournament_id']; ?>" class="btn btn-primary">
                                                <i class="fas fa-info-circle me-2"></i>View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-4">
                            <a href="tournaments.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-list me-2"></i>See More Tournaments
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="card bg-dark text-light mb-4">
                    <div class="card-header bg-dark text-light">
                        <h4 class="mb-0">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h4>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            <i class="fas fa-coins me-2"></i>Points: <?php echo $user_info['points']; ?>
                        </p>
                        <a href="create_tournament.php" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-plus-circle me-2"></i>Create Tournament
                        </a>
                        <a href="my_tournaments.php" class="btn btn-outline-light w-100 mb-3">
                            <i class="fas fa-list me-2"></i>My Tournaments
                        </a>
                        <a href="profile.php" class="btn btn-outline-light w-100">
                            <i class="fas fa-user me-2"></i>My Profile
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="mb-0">Join EpicClash</h4>
                    </div>
                    <div class="card-body">
                        <p>Create an account to:</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Create tournaments</li>
                            <li><i class="fas fa-check text-success me-2"></i>Join tournaments</li>
                            <li><i class="fas fa-check text-success me-2"></i>Earn points</li>
                            <li><i class="fas fa-check text-success me-2"></i>Win prizes</li>
                        </ul>
                        <div class="d-grid gap-2">
                            <a href="register.php" class="btn btn-primary">Register Now</a>
                            <a href="login.php" class="btn btn-outline-primary">Login</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card bg-dark text-light">
                <div class="card-header bg-dark text-light">
                    <h4 class="mb-0">Quick Stats</h4>
                </div>
                <div class="card-body">
                    <?php
                    // Get total tournaments
                    $stmt = $db->query("SELECT COUNT(*) as total FROM tournaments WHERE status = 'active'");
                    $total_tournaments = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

                    // Get total users
                    $stmt = $db->query("SELECT COUNT(*) as total FROM users");
                    $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

                    // Get total participants
                    $stmt = $db->query("SELECT COUNT(*) as total FROM tournament_participants");
                    $total_participants = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                    ?>
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-dark text-light rounded">
                                <i class="fas fa-trophy fa-2x mb-2"></i>
                                <h5 class="mb-0 text-light"><?php echo $total_tournaments; ?></h5>
                                <small class="text-light">Active Tournaments</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-dark text-light rounded">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <h5 class="mb-0 text-light"><?php echo $total_users; ?></h5>
                                <small class="text-light">Total Users</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-dark text-light rounded">
                                <i class="fas fa-user-friends fa-2x mb-2"></i>
                                <h5 class="mb-0 text-light"><?php echo $total_participants; ?></h5>
                                <small class="text-light">Total Participants</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 