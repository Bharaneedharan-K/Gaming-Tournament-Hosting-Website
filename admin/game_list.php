<?php
require '../db_connection.php'; // Include the database connection file

// Handle form submission for game uploads
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['game_name'])) {
    $game_name = $_POST['game_name'];
    $game_image = file_get_contents($_FILES['game_image']['tmp_name']);

    $sql = "INSERT INTO game_list (game_name, game_image) VALUES (:game_name, :game_image)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':game_name', $game_name);
    $stmt->bindParam(':game_image', $game_image, PDO::PARAM_LOB);

    if ($stmt->execute()) {
        echo "<script>alert('Game added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding game.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f9f9f9;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .add-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }
        .add-btn:hover {
            background-color: #2980b9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }
        /* Popup form styles */
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .popup-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        .popup-card h2 {
            margin-bottom: 15px;
        }
        .popup-card form {
            display: flex;
            flex-direction: column;
        }
        .popup-card input[type="text"],
        .popup-card input[type="file"] {
            margin-bottom: 15px;
            padding: 10px;
            font-size: 14px;
        }
        .popup-card button {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }
        .popup-card button:hover {
            background-color: #1e8449;
        }
    </style>
    <script>
        function openPopup() {
            document.querySelector('.popup-overlay').style.display = 'flex';
        }

        function closePopup() {
            document.querySelector('.popup-overlay').style.display = 'none';
        }
    </script>
</head>
<body>

    <div class="header">
        <h1>Game List</h1>
        <button class="add-btn" onclick="openPopup()">+ Add Game</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Game Name</th>
                <th>Game Image</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM game_list";
            $stmt = $pdo->query($sql);
            $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($games) {
                foreach ($games as $game) {
                    echo "<tr>";
                    echo "<td>" . $game['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($game['game_name']) . "</td>";
                    echo "<td><img src='data:image/jpeg;base64," . base64_encode($game['game_image']) . "' alt='Game Image'></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No games found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

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

</body>
</html>
