<?php
require_once 'config/database.php';
require_once 'includes/header.php';



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