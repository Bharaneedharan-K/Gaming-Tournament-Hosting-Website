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

// Get filter parameters
$game_filter = isset($_GET['game']) ? $_GET['game'] : '';
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$query = "SELECT t.*, u.username as owner_name, 
          (SELECT COUNT(*) FROM tournament_participants WHERE tournament_id = t.tournament_id) as current_participants
          FROM tournaments t 
          JOIN users u ON t.owner_id = u.user_id 
          WHERE t.status = 'active'";

$params = [];

if ($game_filter) {
    $query .= " AND t.game_name = ?";
    $params[] = $game_filter;
}

if ($type_filter) {
    if ($type_filter === 'paid') {
        $query .= " AND t.is_paid = 1";
    } else if ($type_filter === 'free') {
        $query .= " AND t.is_paid = 0";
    }
}

if ($search) {
    $query .= " AND (t.tournament_name LIKE ? OR t.game_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY t.tournament_date ASC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique games for filter
$stmt = $db->query("SELECT DISTINCT game_name FROM tournaments WHERE status = 'active' ORDER BY game_name");
$games = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<style>
.tournament-card {
    transition: transform 0.2s, box-shadow 0.2s;
    background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%);
    border: none !important;
    overflow: hidden;
}

.tournament-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
}

.tournament-image {
    height: 200px;
    object-fit: cover;
    width: 100%;
}

.tournament-info p {
    padding: 10px;
    border-radius: 8px;
    background: rgba(0, 0, 0, 0.3);
    margin-bottom: 12px !important;
    border: 1px solid rgba(13, 110, 253, 0.2);
    transition: all 0.3s;
}

.tournament-info p:hover {
    background: rgba(13, 110, 253, 0.1);
    border-color: rgba(13, 110, 253, 0.4);
}

.form-control, .form-select {
    height: 45px;
}

.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
    border-color: #0d6efd;
}

.btn-primary {
    background: linear-gradient(145deg, #0d6efd 0%, #0a58ca 100%);
    border: none;
    transition: all 0.3s;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
}

.card-header {
    background: linear-gradient(145deg, #212529 0%, #343a40 100%) !important;
    border-bottom: 2px solid #0d6efd;
    padding: 1.2rem;
}

.alert {
    background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%) !important;
    border: 1px solid #0d6efd;
    padding: 20px;
}

.input-group-text {
    min-width: 46px;
    justify-content: center;
}

.tournament-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: bold;
    z-index: 2;
}

.tournament-title {
    color: white;
    font-size: 1.4rem;
    margin-bottom: 10px;
    padding: 15px;
    background: linear-gradient(145deg, #212529 0%, #343a40 100%);
    border-bottom: 2px solid #0d6efd;
}

.filter-section {
    background: rgba(0, 0, 0, 0.3);
    padding: 20px;
    border-radius: 10px;
    border: 1px solid rgba(13, 110, 253, 0.2);
    margin-bottom: 30px;
}
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header bg-dark text-light">
                <h4 class="mb-0"><i class="fas fa-trophy text-warning me-2"></i>Available Tournaments</h4>
            </div>
            <div class="card-body bg-dark">
                <div class="filter-section">
                    <form method="GET" action="" class="mb-0">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-light border-primary">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control bg-dark text-light border-primary" name="search" 
                                           placeholder="Search tournaments..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-light border-primary">
                                        <i class="fas fa-gamepad"></i>
                                    </span>
                                    <select class="form-select bg-dark text-light border-primary" name="game">
                                        <option value="">All Games</option>
                                        <?php foreach ($games as $game): ?>
                                            <option value="<?php echo htmlspecialchars($game); ?>" 
                                                    <?php echo $game_filter === $game ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($game); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-light border-primary">
                                        <i class="fas fa-tag"></i>
                                    </span>
                                    <select class="form-select bg-dark text-light border-primary" name="type">
                                        <option value="">All Types</option>
                                        <option value="paid" <?php echo $type_filter === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                        <option value="free" <?php echo $type_filter === 'free' ? 'selected' : ''; ?>>Free</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <?php if (count($tournaments) > 0): ?>
                    <div class="row">
                        <?php foreach ($tournaments as $tournament): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card tournament-card h-100">
                                    <?php 
                                    $game_name = strtolower(trim($tournament['game_name']));
                                    $image_path = isset($game_images[$game_name]) ? $game_images[$game_name] : 'https://via.placeholder.com/800x400?text=Game+Image';
                                    ?>
                                    <img src="<?php echo $image_path; ?>" class="tournament-image" alt="<?php echo htmlspecialchars($tournament['game_name']); ?>">
                                    <?php if ($tournament['is_paid']): ?>
                                        <div class="tournament-badge bg-warning text-dark">
                                            <i class="fas fa-coins me-1"></i>Paid Tournament
                                        </div>
                                    <?php endif; ?>
                                    <h5 class="tournament-title">
                                        <i class="fas fa-trophy text-warning me-2"></i>
                                        <?php echo htmlspecialchars($tournament['tournament_name']); ?>
                                    </h5>
                                    <div class="card-body bg-dark text-light">
                                        <div class="tournament-info">
                                            <p class="mb-2">
                                                <i class="fas fa-gamepad me-2 text-primary"></i>
                                                <?php echo htmlspecialchars($tournament['game_name']); ?>
                                            </p>
                                            <p class="mb-2">
                                                <i class="fas fa-calendar me-2 text-primary"></i>
                                                <?php echo date('M d, Y H:i', strtotime($tournament['tournament_date'])); ?>
                                            </p>
                                            <p class="mb-2">
                                                <i class="fas fa-users me-2 text-primary"></i>
                                                <?php echo $tournament['current_participants']; ?>/<?php echo $tournament['max_players']; ?> Players
                                            </p>
                                            <?php if ($tournament['is_paid']): ?>
                                                <p class="mb-2">
                                                    <i class="fas fa-trophy me-2 text-warning"></i>
                                                    Prize: ₹<?php echo number_format($tournament['winning_prize'], 2); ?>
                                                </p>
                                                <p class="mb-2">
                                                    <i class="fas fa-money-bill-wave me-2 text-warning"></i>
                                                    Registration Fee: ₹<?php echo number_format($tournament['registration_fee'], 2); ?>
                                                </p>
                                            <?php endif; ?>
                                            <p class="mb-2">
                                                <i class="fas fa-user me-2 text-primary"></i>
                                                Organizer: <?php echo htmlspecialchars($tournament['owner_name']); ?>
                                            </p>
                                        </div>
                                        <a href="tournament_details.php?id=<?php echo $tournament['tournament_id']; ?>" 
                                           class="btn btn-primary w-100 mt-3">
                                            <i class="fas fa-eye me-2"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info bg-dark text-light border-primary">
                        <i class="fas fa-info-circle me-2"></i>No tournaments found matching your criteria.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 