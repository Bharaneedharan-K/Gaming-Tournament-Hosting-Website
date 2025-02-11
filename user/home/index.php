<?php include '../user_id.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
</head>
<body>
    <h1>Welcome, <?php echo isset($currentUserName) ? $currentUserName : 'Guest'; ?>!</h1>
</body>
</html>
