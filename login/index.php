<?php
require '../db_connection.php';

$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'register':
            $userId = $_POST['user_id'];
            $fullName = $_POST['full_name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            if ($password !== $confirmPassword) {
                $message = "Passwords do not match.";
                $messageType = "error";
                break;
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("INSERT INTO users (user_id, full_name, email, password) VALUES (?, ?, ?, ?)");
            try {
                $stmt->execute([$userId, $fullName, $email, $hashedPassword]);
                $message = "Registration successful!";
                $messageType = "success";
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = "User ID or Email already exists. Please choose a different one.";
                } else {
                    $message = "Error: " . $e->getMessage();
                }
                $messageType = "error";
            }
            break;

        case 'login':
            $userId = $_POST['user_id'];
            $password = $_POST['password'];

            $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                header("Location: home.html");
                exit();
            } else {
                $message = "Invalid user ID or password.";
                $messageType = "error";
            }
            break;

        case 'reset_password':
            $userId = $_POST['user_id'];
            $newPassword = $_POST['new_password'];
            $confirmNewPassword = $_POST['confirm_new_password'];

            if ($newPassword !== $confirmNewPassword) {
                $message = "Passwords do not match.";
                $messageType = "error";
                break;
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            try {
                $stmt->execute([$hashedPassword, $userId]);
                $message = "Password reset successful!";
                $messageType = "success";
            } catch (PDOException $e) {
                $message = "Error: " . $e->getMessage();
                $messageType = "error";
            }
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Flip Animation</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
    <style>
        /* Notification Popup Styling */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            font-size: 16px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s;
        }

        .notification.error {
            background-color: #f44336;
        }

        .notification.show {
            opacity: 1;
            visibility: visible;
        }
    </style>
    <script>
        // Show notification and auto-hide after 3 seconds
        function showNotification(message, type) {
            const notification = document.createElement("div");
            notification.className = `notification ${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.add("show");
            }, 10);

            setTimeout(() => {
                notification.classList.remove("show");
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Display server-side messages if available
        window.onload = function () {
            const message = "<?php echo $message ?? ''; ?>";
            const messageType = "<?php echo $messageType ?? ''; ?>";
            if (message) {
                showNotification(message, messageType);
            }
        };
    </script>
</head>
<body>

<div class="container">
    <!-- Login Card -->
    <div class="card" id="loginCard">
        <br></br>
        <br></br>
        <h2>LOGIN</h2>
        <form method="POST">
            <input type="hidden" name="action" value="login">
            <input type="text" class="input-box" name="user_id" placeholder="User ID" required>
            <input type="password" class="input-box" name="password" placeholder="Password" required>
            <button type="submit" class="submit-btn">Login</button>
        </form>
        <p>Forgot your password? <span onclick="showForgotPassword()">Reset Password</span></p>
        <p>Don't have an account? <span onclick="showRegister()">Register</span></p>
    </div>

    <!-- Register Card -->
    <div class="card" id="registerCard">
        <h2>REGISTER</h2>
        <form method="POST">
            <input type="hidden" name="action" value="register">
            <input type="text" class="input-box" name="user_id" placeholder="User ID" required>
            <input type="text" class="input-box" name="full_name" placeholder="Full Name" required>
            <input type="email" class="input-box" name="email" placeholder="Email ID" required>
            <input type="password" class="input-box" name="password" placeholder="Password" required>
            <input type="password" class="input-box" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit" class="submit-btn">Register</button>
        </form>
        <p>Already have an account? <span onclick="showLogin()">Login</span></p>
    </div>

    <!-- Forgot Password Card -->
    <div class="card" id="forgotPasswordCard">
    <br></br>
        <h2>FORGOT PASSWORD</h2>
        <form method="POST">
            <input type="hidden" name="action" value="reset_password">
            <input type="text" class="input-box" name="user_id" placeholder="User ID" required>
            <input type="password" class="input-box" name="new_password" placeholder="New Password" required>
            <input type="password" class="input-box" name="confirm_new_password" placeholder="Confirm New Password" required>
            <button type="submit" class="submit-btn">Reset Password</button>
        </form>
        <p><span onclick="showLogin()">Back to Login</span></p>
    </div>
</div>

</body>
</html>
