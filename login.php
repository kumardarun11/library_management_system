<?php
// login.php
require_once 'config.php';

// If already logged in
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'User name and password are mandatory.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && hash('sha256', $password) === $user['password_hash']) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid credentials.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Library Management System</title>
    <style>
        /* You can reuse CSS from header.php, kept minimal here */
        body{background:#f4f4f4;font-family:Arial,Helvetica,sans-serif;}
        #login-box{max-width:400px;margin:80px auto;background:#fff;padding:20px 25px;border-radius:5px;
            box-shadow:0 2px 4px rgba(0,0,0,0.1);}
        h2{margin-bottom:10px;}
        .form-row{margin-bottom:12px;}
        label{display:block;margin-bottom:4px;font-size:14px;}
        input[type="text"],input[type="password"]{width:100%;padding:7px 8px;border:1px solid #ccc;border-radius:3px;}
        .radio-group{display:flex;gap:15px;margin-top:5px;}
        button{background:#283593;color:#fff;border:none;padding:8px 16px;border-radius:3px;cursor:pointer;font-size:14px;}
        button:hover{background:#1a237e;}
        .error{color:#c62828;margin-top:5px;font-size:13px;}
        .info-box{background:#e3f2fd;border-left:4px solid #1e88e5;padding:10px 12px;font-size:13px;margin-bottom:12px;}
    </style>
</head>
<body>
<div id="login-box">
    <h2>Login</h2>
    <div class="info-box">
        Admin can access Maintenance, Reports and Transactions.<br>
        User can access Reports and Transactions only.<br>
        Passwords are hidden while typing.
    </div>
    <form method="post">
        <div class="form-row">
            <label>User Name</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-row">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-row">
            <!-- just info; role is taken from DB, not chosen here -->
            <small>Role is decided by system (admin / user).</small>
        </div>
        <button type="submit">Login</button>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
    </form>
</div>
</body>
</html>
