<?php
require '../../db_connection.php'; // Include the database connection file

$showSuccessPopup = false;

// Handle form submission for game uploads
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['game_name'])) {
    $game_name = $_POST['game_name'];
    $game_image = file_get_contents($_FILES['game_image']['tmp_name']);

    $sql = "INSERT INTO game_list (game_name, game_image) VALUES (:game_name, :game_image)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':game_name', $game_name);
    $stmt->bindParam(':game_image', $game_image, PDO::PARAM_LOB);

    if ($stmt->execute()) {
        header("Location: game_list.php?success=1");
        exit;
    } else {
        echo "<script>alert('Error adding game.');</script>";
    }
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $showSuccessPopup = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game List</title>
    <link rel="stylesheet" href="game_list.css">
    <script>
        function openPopup() {
            document.querySelector('.popup-overlay').style.display = 'flex';
        }

        function closePopup() {
            document.querySelector('.popup-overlay').style.display = 'none';
        }

        function showSuccessNotification() {
            const notification = document.querySelector('.success-notification');
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 2000);
        }

        window.onload = function () {
            <?php if ($showSuccessPopup) echo "showSuccessNotification();"; ?>
        };
    </script>
</head>
<body>

<div class="sidebar">
    <h2>Game Menu</h2>
    <ul>
        <li><a href="../index.php">Home</a></li>
        <li><a href="game_list.php">Game List</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="header-bar">
        <h1>Game List</h1>
        <button class="add-btn" onclick="openPopup()">+ Add Game</button>
    </div>

    <div class="card-container">
        <?php
        $sql = "SELECT * FROM game_list";
        $stmt = $pdo->query($sql);
        $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($games) {
            foreach ($games as $game) {
                echo "<div class='game-card'>";
                echo "<img src='data:image/jpeg;base64," . base64_encode($game['game_image']) . "' alt='Game Image'>";
                echo "<h3>" . htmlspecialchars($game['game_name']) . "</h3>";
                echo "</div>";
            }
        } else {
            echo "<p>No games found.</p>";
        }
        ?>
    </div>
</div>

<!-- Popup Form -->
<div class="popup-overlay" onclick="closePopup()">
    <div class="popup-card" onclick="event.stopPropagation()">
        <h2>Add a New Game</h2>
        <form action="game_list.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="game_name" placeholder="Enter game name" required>
            <input type="file" name="game_image" accept="image/*" required>
            <button type="submit">Submit</button>
        </form>
    </div>
</div>

<!-- Success Notification -->
<div class="success-notification">Game added successfully!</div>

</body>
</html>
