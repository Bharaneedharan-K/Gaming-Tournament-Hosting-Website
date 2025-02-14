<?php
include '../../db_connection.php'; // Adjust path if needed

try {
    $sql = "SELECT tournament_id, tournament_name, tournament_date, tournament_image FROM tournaments ORDER BY tournament_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($tournaments) {
        echo "<div class='tournament-container'>";
        foreach ($tournaments as $row) {
            $tournamentId = htmlspecialchars($row['tournament_id']);
            $tournamentName = htmlspecialchars($row['tournament_name']);
            $tournamentDate = htmlspecialchars($row['tournament_date']);
            $imageData = base64_encode($row['tournament_image']);
            $imageSrc = !empty($row['tournament_image']) ? "data:image/jpeg;base64,$imageData" : "placeholder.png";

            echo "
                <div class='tournament-card'>
                    <img src='$imageSrc' alt='Tournament Image' class='tournament-img'>
                    <div class='tournament-info'>
                        <h2>$tournamentName</h2>
                        <p>ID: $tournamentId</p>
                        <p>Date: $tournamentDate</p>
                    </div>
                </div>
            ";
        }
        echo "</div>";
    } else {
        echo "<p style='text-align: center; color: white;'>No tournaments available.</p>";
    }
} catch (PDOException $e) {
    echo "Error fetching tournaments: " . $e->getMessage();
}
?>
