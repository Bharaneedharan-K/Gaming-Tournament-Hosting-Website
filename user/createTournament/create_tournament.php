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
  }
  .form-container {
    display: flex;
    gap: 15px;
  }
  .form-left, .form-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .image-upload label {
    color: white;
  }
  #image-preview {
    width: 100%;
    border-radius: 8px;
    margin-top: 10px;
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
    <h2>Create Tournament</h2>
    <div class="form-container">
      <div class="form-left">
        <input type="text" placeholder="Tournament Name">
        <input type="text" placeholder="Tournament ID">
        <input type="date" placeholder="Date">
        <input type="text" placeholder="Contact Info (Mail/Phone)">
        <input type="text" placeholder="Game Name">
        <input type="number" placeholder="No. of Players">
        <label>Team Size</label>
        <input type="number" value="1">
      </div>
      <div class="form-right">
        <div class="image-upload">
          <label for="tournament-image">Upload Image</label>
          <input type="file" id="tournament-image" accept="image/*" onchange="previewImage(event)">
          <img id="image-preview" src="#" alt="Image Preview" style="display:none;">
        </div>
        <select id="fee-type" onchange="togglePrizeFields()">
          <option value="free">Free</option>
          <option value="paid">Paid</option>
        </select>
        <div id="prize-fields" style="display: none;">
          <input type="text" placeholder="Top 1 Prize">
          <input type="text" placeholder="Top 2 Prize">
          <input type="text" placeholder="Top 3 Prize">
        </div>
        <input type="text" placeholder="UPI ID">
      </div>
    </div>
    <button onclick="closeModal()">Create</button>
  </div>
</div>

  </div>

  <script>
    function scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function handleLogout() {
      window.location.href = '../../login/index.php';
    }
    function previewImage(event) {
    const preview = document.getElementById('image-preview');
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
      };
      reader.readAsDataURL(file);
    }
  }
  function togglePrizeFields() {
    const feeType = document.getElementById('fee-type').value;
    document.getElementById('prize-fields').style.display = feeType === 'paid' ? 'block' : 'none';
  }
  function closeModal() {
    document.getElementById('tournament-modal').style.display = 'none';
  }
  </script>

</body>
</html>
