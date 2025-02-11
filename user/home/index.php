<?php
  // This is a simple structure for demonstration purposes
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
      padding: 20px;
      gap: 20px;
      box-shadow: 4px 4px 12px rgba(0, 0, 0, 0.5);
      border-radius: 16px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .sidebar:hover {
      box-shadow: 8px 8px 16px rgba(0, 0, 0, 0.7);
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

    .content {
      margin-left: 280px;
      padding: 40px;
      flex: 1;
    }

    .scroll-top {
      display: none;
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <a href="#" class="logo" onclick="scrollToTop()">EpicClash</a>
    <a href="#">Home</a>
    <a href="#">Search Tournament</a>
    <a href="#">My Tournaments</a>
    <a href="#">Create Tournament</a>
  </div>

  <div class="content">
    <h1>Welcome to EpicClash</h1>
    <p>This is a sample content section to demonstrate a floating sidebar with navigation links.</p>
    <p>Scroll to test the "back to top" feature by clicking the EpicClash logo.</p>
  </div>

  <script>
    function scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  </script>

</body>
</html>