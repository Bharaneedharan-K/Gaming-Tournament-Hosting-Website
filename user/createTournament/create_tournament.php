<?php
  // This is a simple structure for demonstration purposes
  include '../user_id.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EpicClash</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Arial', sans-serif;
    }

    body {
      background-color: #121212;
      color: #ffffff;
      display: flex;
      min-height: 100vh;
      overflow-x: hidden;
    }

    .sidebar {
      position: fixed;
      top: 20px;
      left: 20px;
      height: calc(100% - 40px);
      width: 240px;
      background-color: #1e1e1e;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 20px;
      gap: 20px;
      box-shadow: 4px 4px 12px rgba(0, 0, 0, 0.5);
      border-radius: 16px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .sidebar:hover {
      box-shadow: 8px 8px 16px rgba(0, 0, 0, 0.7);
    }

    .sidebar .nav-links {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .sidebar .logo {
      font-size: 28px;
      font-weight: bold;
      color: #ff9800;
      text-decoration: none;
      cursor: pointer;
      padding: 10px 0;
      transition: color 0.3s;
    }

    .sidebar .logo:hover {
      color: #ffc107;
    }

    .sidebar a {
      text-decoration: none;
      color: #ffffff;
      padding: 10px 0;
      font-size: 18px;
      transition: color 0.3s, transform 0.3s;
    }

    .sidebar a:hover {
      color: #03dac5;
      transform: translateX(8px);
    }

    .logout-section {
      display: flex;
      flex-direction: column;
      position: relative;
      cursor: pointer;
    }

    .logout-toggle {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px;
      background: #333333;
      border-radius: 8px;
      transition: background-color 0.3s;
    }

    .logout-toggle:hover {
      background-color: #444444;
    }

    .logout-dropdown {
      display: none;
      flex-direction: column;
      background: #1e1e1e;
      padding: 10px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
      margin-top: 5px;
    }

    .logout-section:hover .logout-dropdown {
      display: flex;
    }

    .logout-option {
      color: #ffffff;
      cursor: pointer;
      padding: 5px;
      transition: color 0.3s;
    }

    .logout-option:hover {
      color: #ff5252;
    }

    .logout-icon {
      width: 24px;
      height: 24px;
      background: url('https://img.icons8.com/ios-filled/50/ffffff/logout-rounded-left.png') no-repeat center;
      background-size: cover;
    }

    .content {
      margin-left: 280px;
      padding: 40px;
      flex: 1;
      position: relative;
    }

    .create-tournament-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      background-color: #ff9800;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s;
    }

    .create-tournament-btn:hover {
      background-color: #e68900;
    }

    .scroll-top {
      display: none;
    }

    .create-tournament-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      background-color: #ff9800;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s;
    }

    .create-tournament-btn:hover {
      background-color: #e68900;
    }

    .modal {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: #1e1e1e;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
      width: 500px;
    }

    .modal input, .modal select {
      width: 100%;
      margin: 5px 0;
      padding: 8px;
      border-radius: 5px;
      border: none;
    }

    .modal button {
      width: 100%;
      padding: 10px;
      background: #ff9800;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .modal button:hover {
      background: #e68900;
    }
    .modal-content h2 {
    text-align: center;
    color: white;
  }`
  .form-container {
        display: flex;
        gap: 20px;
    }
    .form-column {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
  .form-left, .form-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .image-upload {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .upload-label {
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

.upload-label:hover {
  background-color: #444;
}

.upload-label img {
  width: 30px;
  height: 30px;
}

.image-preview-box {
  width: 150px;
  height: 150px;
  border: 2px dashed #ffffff;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  position: relative;
}

.image-preview-box img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: none;
}
.image-preview {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border: 2px dashed #ccc;
        margin-bottom: 10px;
    }

/* ----------- */

    .tournament-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: flex-start;
    margin-top: 20px;
}

.tournament-card {
    width: 250px;
    background-color: #1e1e1e;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
    transition: transform 0.3s, box-shadow 0.3s;
}

.tournament-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.7);
}

.tournament-img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.tournament-info {
    padding: 15px;
    text-align: center;
    color: white;
}

.tournament-info h2 {
    font-size: 18px;
    margin-bottom: 10px;
    color: #ff9800;
}

.tournament-info p {
    font-size: 14px;
    margin: 5px 0;
    color: #ccc;
}

.close-button {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        cursor: pointer;
    }
  
  </style>
</head>
<body>

  <div class="sidebar">
  <div class="nav-links">
    <a href="../home/index.php" class="logo" onclick="scrollToTop()">EpicClash</a>
    <a href="../home/index.php">Home</a>
    <a href="search_tournament.php">Search Tournament</a>
    <a href="my_tournaments.php">My Tournaments</a>
    <a href="create_tournament.php">Create Tournament</a>
    <a href="history.php">History</a>
  </div>

    <div class="logout-section">
      <div class="logout-toggle">
        <div class="logout-icon"></div>
        <span><?php echo htmlspecialchars($currentUserName); ?></span>
      </div>
      <div class="logout-dropdown">
        <div class="logout-option" onclick="handleLogout()">Logout</div>
      </div>
    </div>
  </div>

  <div class="content">
    <button class="create-tournament-btn" onclick="document.getElementById('tournament-modal').style.display='block'">+ Create Tournament</button>
    
    <div id="tournament-modal" class="modal">
        <div class="modal-content">
        <span class="close-button" onclick="closeModal()">&times;</span>
            <h2>Create Tournament</h2>
            <br>

            <form id="tournament-form" action="save_tournament.php" method="POST" enctype="multipart/form-data">
                <div class="form-container">
                    <div class="form-column">
                        <input type="text" name="tournament_name" placeholder="Tournament Name" required>
                        
                        <input type="date" name="tournament_date" required>
                        <input type="text" name="contact_info" placeholder="Contact Info (Mail/Phone)" required>
                        <input type="text" name="game_name" placeholder="Game Name" required>
                        <input type="number" name="num_players" placeholder="No. of Players" required min="1">
                        <label>Team Size</label>
                        <input type="number" name="team_size" value="1" required min="1">
                    </div>

                    <div class="form-column">
                        <div class="image-upload">
                            <label for="tournament_image" class="upload-label">
                                <img id="image-preview" src="placeholder.png" alt="Preview" class="image-preview">
                                <input type="file" id="tournament_image" name="tournament_image" accept="image/*" required onchange="previewImage(event)">
                            </label>
                        </div>
                        
                        <select name="fee_type" id="fee-type" required onchange="togglePrizeFields()">
                            <option value="" disabled selected>Select Fee Type</option>
                            <option value="free">Free</option>
                            <option value="paid">Paid</option>
                        </select>

                        <div id="prize-fields" style="display: none;">
                            <input type="text" name="top_1_prize" id="prize-1" placeholder="Top 1 Prize">
                            <input type="text" name="top_2_prize" id="prize-2" placeholder="Top 2 Prize">
                            <input type="text" name="top_3_prize" id="prize-3" placeholder="Top 3 Prize">
                        </div>
                        
                        <input type="text" name="upi_id" placeholder="UPI ID" required>
                        <button type="submit">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <br>
    <br>
    <?php include 'tournamentCard.php'; ?>
</div>
  <script>
    function scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function handleLogout() {
      window.location.href = '../../login/index.php';
    }

    function validateForm() {
        let requiredFields = document.querySelectorAll("#tournament-form input[required], #tournament-form select[required]");
        for (let field of requiredFields) {
            if (!field.value.trim()) {
                alert("Please fill all required fields.");
                field.focus();
                return false;
            }
        }
        return true;
    }

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('image-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
  function togglePrizeFields() {
    let feeType = document.getElementById("fee-type").value;
    let prizeFields = document.getElementById("prize-fields");
    
    if (feeType === "paid") {
        prizeFields.style.display = "block";
        document.getElementById("prize-1").setAttribute("required", "true");
        document.getElementById("prize-2").setAttribute("required", "true");
        document.getElementById("prize-3").setAttribute("required", "true");
    } else {
        prizeFields.style.display = "none";
        document.getElementById("prize-1").removeAttribute("required");
        document.getElementById("prize-2").removeAttribute("required");
        document.getElementById("prize-3").removeAttribute("required");
    }
}
function closeModal() {
    document.getElementById('tournament-modal').style.display = 'none';
  }
  </script>

</body>
</html>
