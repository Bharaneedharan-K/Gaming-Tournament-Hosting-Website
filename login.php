<?php
require_once 'config/database.php';
require_once 'includes/header.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        $stmt = $db->prepare("SELECT user_id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>

<style>
.login-container {
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    padding: 2rem;
}

.login-card {
    background: linear-gradient(145deg, #2d2d2d 0%, #1a1a1a 100%);
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    border: 2px solid #0d6efd;
    overflow: hidden;
    width: 100%;
    max-width: 400px;
    position: relative;
}

.login-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #0d6efd, #0a58ca);
}

.login-header {
    background: rgba(0, 0, 0, 0.3);
    padding: 2rem;
    text-align: center;
    border-bottom: 1px solid rgba(13, 110, 253, 0.2);
}

.login-header h2 {
    color: #fff;
    margin: 0;
    font-size: 2rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.login-body {
    padding: 2rem;
}

.form-control {
    background: rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(13, 110, 253, 0.3);
    color: #fff;
    height: 50px;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    transition: all 0.3s;
}

.form-control:focus {
    background: rgba(0, 0, 0, 0.3);
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

.btn-login {
    background: linear-gradient(145deg, #0d6efd 0%, #0a58ca 100%);
    border: none;
    height: 50px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    transition: all 0.3s;
}

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
}

.alert {
    background: rgba(220, 53, 69, 0.1);
    border: 1px solid rgba(220, 53, 69, 0.3);
    color: #fff;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.register-link {
    text-align: center;
    margin-top: 1.5rem;
    color: rgba(255, 255, 255, 0.7);
}

.register-link a {
    color: #0d6efd;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.register-link a:hover {
    color: #0a58ca;
    text-decoration: underline;
}

.input-group-text {
    background: rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(13, 110, 253, 0.3);
    color: #fff;
    border-radius: 8px 0 0 8px;
    padding: 0 1rem;
}

.input-group .form-control {
    border-radius: 0 8px 8px 0;
}
</style>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h2><i class="fas fa-gamepad me-2"></i>Welcome Back</h2>
            <p class="text-light mb-0">Sign in to continue your gaming journey</p>
        </div>
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-login w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>
            </form>
            
            <div class="register-link">
                Don't have an account? <a href="register.php">Register now</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 