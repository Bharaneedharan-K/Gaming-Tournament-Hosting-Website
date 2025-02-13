<?php
include '../user_id.php'; // Fetch $currentUserId and $currentUserName
include '../../db_connection.php'; // Use the PDO connection

// Function to generate a unique tournament ID
function generateUniqueTournamentId($pdo) {
    do {
        $tournament_id = rand(1000000, 9999999); // Generate a 7-digit random number
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tournaments WHERE tournament_id = :tournament_id");
        $stmt->bindParam(':tournament_id', $tournament_id, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();
    } while ($count > 0); // Repeat if the ID already exists

    return $tournament_id;
}

// Fetch form data
$tournament_name = $_POST['tournament_name'];
$tournament_date = $_POST['tournament_date'];
$contact_info = $_POST['contact_info'];
$game_name = $_POST['game_name'];
$num_players = $_POST['num_players'];
$team_size = $_POST['team_size'];
$fee_type = $_POST['fee_type'];
$top_1_prize = $_POST['top_1_prize'] ?? null;
$top_2_prize = $_POST['top_2_prize'] ?? null;
$top_3_prize = $_POST['top_3_prize'] ?? null;
$upi_id = $_POST['upi_id'];

// Generate a unique tournament ID
$tournament_id = generateUniqueTournamentId($pdo);

// Handle Image Upload
$image_data = null;
if (!empty($_FILES['tournament_image']['tmp_name'])) {
    $image_data = file_get_contents($_FILES['tournament_image']['tmp_name']); // Read image file content
}

// Prepare the SQL statement using PDO
$sql = "INSERT INTO tournaments 
        (user_id, user_name, tournament_id, tournament_name, tournament_date, contact_info, game_name, num_players, team_size, fee_type, top_1_prize, top_2_prize, top_3_prize, upi_id, tournament_image) 
        VALUES 
        (:user_id, :user_name, :tournament_id, :tournament_name, :tournament_date, :contact_info, :game_name, :num_players, :team_size, :fee_type, :top_1_prize, :top_2_prize, :top_3_prize, :upi_id, :tournament_image)";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $currentUserId);
    $stmt->bindParam(':user_name', $currentUserName);
    $stmt->bindParam(':tournament_id', $tournament_id, PDO::PARAM_INT);
    $stmt->bindParam(':tournament_name', $tournament_name);
    $stmt->bindParam(':tournament_date', $tournament_date);
    $stmt->bindParam(':contact_info', $contact_info);
    $stmt->bindParam(':game_name', $game_name);
    $stmt->bindParam(':num_players', $num_players, PDO::PARAM_INT);
    $stmt->bindParam(':team_size', $team_size, PDO::PARAM_INT);
    $stmt->bindParam(':fee_type', $fee_type);
    $stmt->bindParam(':top_1_prize', $top_1_prize);
    $stmt->bindParam(':top_2_prize', $top_2_prize);
    $stmt->bindParam(':top_3_prize', $top_3_prize);
    $stmt->bindParam(':upi_id', $upi_id);
    $stmt->bindParam(':tournament_image', $image_data, PDO::PARAM_LOB);

    $stmt->execute();
    echo "<script>alert('Tournament created successfully!'); window.location.href='create_tournament.php';</script>";
} catch (PDOException $e) {
    echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
}
?>
