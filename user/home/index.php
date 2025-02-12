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
    }

    .banner {
      width: 95%; /* Adjust the width percentage or use a fixed value */
      max-width: 1400px; /* Reduce from 1200px to 900px */
      height: 250px; /* Reduce from 400px to 300px */
      overflow: hidden;
      position: relative;
      border-radius: 16px;
      box-shadow: 4px 4px 12px rgba(0, 0, 0, 0.5);
    }


    .banner img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      position: absolute;
      opacity: 0;
      transition: opacity 1s ease-in-out;
    }

    .banner img.active {
      opacity: 1;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="nav-links">
      <a href="index.php" class="logo" onclick="scrollToTop()">EpicClash</a>
      <a href="index.php">Home</a>
      <a href="search_tournament.php">Search Tournament</a>
      <a href="my_tournaments.php">My Tournaments</a>
      <a href="../createTournament/create_tournament.php">Create Tournament</a>
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
    
    <div class="banner">
    <img src="src/amoung_us.png" class="active" alt="Banner 1">
      <img src="src/freefire.png" alt="Banner 2">
      <img src="src/mindcraft.png" alt="Banner 3">
      <img src="src/bgmi.png" alt="Banner 4">
    </div>
  </div>

  <script>
    function scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function handleLogout() {
      window.location.href = '../../login/index.php';
    }

    let currentIndex = 0;
    const images = document.querySelectorAll('.banner img');
    
    function showNextImage() {
      images[currentIndex].classList.remove('active');
      currentIndex = (currentIndex + 1) % images.length;
      images[currentIndex].classList.add('active');
    }
    
    setInterval(showNextImage, 3000);
  </script>
</body>
</html>
