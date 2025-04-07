<?php
require_once 'config/database.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$query = "SELECT t.*, 
          (SELECT COUNT(*) FROM tournament_participants WHERE tournament_id = t.tournament_id) as current_participants
          FROM tournaments t 
          WHERE t.owner_id = ?";

$params = [$_SESSION['user_id']];

if ($status_filter) {
    $query .= " AND t.status = ?";
    $params[] = $status_filter;
}

if ($search) {
    $query .= " AND (t.tournament_name LIKE ? OR t.game_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY t.created_at DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.card {
    background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%);
}

.card-header {
    background: linear-gradient(145deg, #212529 0%, #343a40 100%) !important;
    border-bottom: 2px solid #0d6efd;
    padding: 1.2rem;
}

.table-dark {
    background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%);
    border-radius: 10px;
    overflow: hidden;
}

.table-dark thead th {
    background: linear-gradient(145deg, #212529 0%, #343a40 100%);
    border-bottom: 2px solid #0d6efd;
    padding: 15px;
}

.table-dark tbody tr {
    transition: all 0.3s;
}

.table-dark tbody tr:hover {
    background: rgba(13, 110, 253, 0.1);
    transform: scale(1.01);
}

.btn-group .btn {
    margin: 0 2px;
    transition: all 0.3s;
    position: relative;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    z-index: 2;
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

.alert {
    background: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%) !important;
    border: 1px solid #0d6efd;
    padding: 20px;
}

.badge {
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: 500;
}

.filter-section {
    background: rgba(0, 0, 0, 0.3);
    padding: 20px;
    border-radius: 10px;
    border: 1px solid rgba(13, 110, 253, 0.2);
    margin-bottom: 30px;
}

.tournament-link {
    display: flex;
    align-items: center;
    text-decoration: none;
}

.btn-group .btn {
    padding: 8px 12px;
}

.btn-group .btn i {
    font-size: 1rem;
}

.input-group {
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.input-group-text {
    min-width: 46px;
    justify-content: center;
}

.table td {
    vertical-align: middle;
}

.status-active {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header bg-dark text-light d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-trophy text-warning me-2"></i>My Tournaments</h4>
                <a href="create_tournament.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create New Tournament
                </a>
            </div>
            <div class="card-body bg-dark">
                <div class="filter-section">
                    <form method="GET" action="" class="mb-0">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-light border-primary">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control bg-dark text-light border-primary" name="search" 
                                           placeholder="Search tournaments..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-light border-primary">
                                        <i class="fas fa-filter"></i>
                                    </span>
                                    <select class="form-select bg-dark text-light border-primary" name="status">
                                        <option value="">All Status</option>
                                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <?php if (count($tournaments) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead class="bg-dark">
                                <tr>
                                    <th class="text-light">Tournament Name</th>
                                    <th class="text-light">Game</th>
                                    <th class="text-light">Date</th>
                                    <th class="text-light">Status</th>
                                    <th class="text-light">Participants</th>
                                    <th class="text-light">Type</th>
                                    <th class="text-light">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tournaments as $tournament): ?>
                                    <tr>
                                        <td>
                                            <a href="tournament_details.php?id=<?php echo $tournament['tournament_id']; ?>" 
                                               class="text-light text-decoration-none tournament-link">
                                                <span>
                                                    <i class="fas fa-trophy text-warning me-2"></i>
                                                    <?php echo htmlspecialchars($tournament['tournament_name']); ?>
                                                </span>
                                            </a>
                                        </td>
                                        <td class="text-light">
                                            <i class="fas fa-gamepad me-2 text-primary"></i>
                                            <?php echo htmlspecialchars($tournament['game_name']); ?>
                                        </td>
                                        <td class="text-light">
                                            <i class="fas fa-calendar me-2 text-primary"></i>
                                            <?php echo date('M d, Y H:i', strtotime($tournament['tournament_date'])); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $tournament['status'] == 'active' ? 'success status-active' : 'secondary'; ?>">
                                                <i class="fas fa-circle me-1"></i>
                                                <?php echo ucfirst($tournament['status']); ?>
                                            </span>
                                        </td>
                                        <td class="text-light">
                                            <i class="fas fa-users me-2 text-primary"></i>
                                            <?php echo $tournament['current_participants']; ?>/<?php echo $tournament['max_players']; ?>
                                        </td>
                                        <td>
                                            <?php if ($tournament['is_paid']): ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-coins me-1"></i>Paid
                                                </span>
                                                <small class="d-block text-light mt-1">₹<?php echo number_format($tournament['registration_fee'], 2); ?></small>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark">
                                                    <i class="fas fa-gift me-1"></i>Free
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="tournament_details.php?id=<?php echo $tournament['tournament_id']; ?>" 
                                                   class="btn btn-sm btn-primary" title="View Details" data-bs-toggle="tooltip">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($tournament['status'] == 'active'): ?>
                                                    <a href="manage_participants.php?id=<?php echo $tournament['tournament_id']; ?>" 
                                                       class="btn btn-sm btn-success" title="Manage Participants" data-bs-toggle="tooltip">
                                                        <i class="fas fa-users"></i>
                                                    </a>
                                                    <a href="announce_winners.php?id=<?php echo $tournament['tournament_id']; ?>" 
                                                       class="btn btn-sm btn-warning" title="Announce Winners" data-bs-toggle="tooltip">
                                                        <i class="fas fa-trophy"></i>
                                                    </a>
                                                    <a href="end_tournament.php?id=<?php echo $tournament['tournament_id']; ?>" 
                                                       class="btn btn-sm btn-danger" title="End Tournament" data-bs-toggle="tooltip">
                                                        <i class="fas fa-flag-checkered"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info bg-dark text-light border-primary">
                        <i class="fas fa-info-circle me-2"></i>
                        You haven't created any tournaments yet. 
                        <a href="create_tournament.php" class="alert-link text-primary">Create your first tournament</a>!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>

<?php require_once 'includes/footer.php'; ?> 