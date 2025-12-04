<?php
// header.php
require_once 'config.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Management System</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:Arial,Helvetica,sans-serif;}
        body{background:#f4f4f4;color:#333;}
        header{background:#283593;color:#fff;padding:15px 25px;display:flex;justify-content:space-between;align-items:center;}
        header h1{font-size:22px;}
        header .chart-link a{color:#ffeb3b;text-decoration:underline;font-size:14px;}
        nav{background:#3949ab;color:#fff;display:flex;gap:15px;padding:10px 20px;}
        nav a{color:#fff;text-decoration:none;font-size:14px;padding:5px 10px;border-radius:3px;}
        nav a:hover, nav a.active{background:#1e88e5;}
        .container{max-width:1000px;margin:20px auto;background:#fff;padding:20px 25px;border-radius:5px;
            box-shadow:0 2px 4px rgba(0,0,0,0.1);}
        h2{margin-bottom:15px;font-size:20px;border-bottom:1px solid #ddd;padding-bottom:5px;}
        form{margin-top:10px;}
        .form-row{margin-bottom:12px;}
        label{display:block;margin-bottom:4px;font-size:14px;}
        input[type="text"],input[type="password"],input[type="date"],input[type="number"],select,textarea{
            width:100%;padding:7px 8px;border:1px solid #ccc;border-radius:3px;font-size:14px;}
        textarea{resize:vertical;min-height:70px;}
        .radio-group,.checkbox-group{display:flex;gap:15px;flex-wrap:wrap;}
        .radio-group label,.checkbox-group label{display:flex;align-items:center;gap:5px;margin-bottom:0;}
        button{background:#283593;color:#fff;border:none;padding:8px 16px;border-radius:3px;cursor:pointer;font-size:14px;}
        button:hover{background:#1a237e;}
        .error{color:#c62828;margin-top:5px;font-size:13px;}
        .success{color:#2e7d32;margin-top:5px;font-size:13px;}
        table{width:100%;border-collapse:collapse;margin-top:10px;font-size:14px;}
        th,td{border:1px solid #ddd;padding:8px;text-align:left;}
        th{background:#eee;}
        .info-box{background:#e3f2fd;border-left:4px solid #1e88e5;padding:10px 12px;font-size:13px;margin-bottom:12px;}
        .top-right{font-size:13px;}
    </style>
</head>
<body>
<header>
    <h1>Library Management System</h1>
    <div class="top-right">
        Logged in as: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
        (<?php echo htmlspecialchars($_SESSION['role']); ?>) |
        <a href="chart.php" style="color:#ffeb3b;text-decoration:underline;">Flow Chart</a> |
        <a href="logout.php" style="color:#ffeb3b;text-decoration:underline;">Logout</a>
    </div>
</header>

<nav>
    <a href="dashboard.php">Home</a>
    <a href="transactions_issue.php">Transactions Issue</a>
    <a href="transactions_return.php">Transactions Return</a>
    <a href="reports.php">Reports</a>
    <a href="books_list.php">All Books / Movies</a>
    <?php if (is_admin()): ?>
        <a href="maintenance_membership.php">Membership</a>
        <a href="maintenance_items.php">Books/Movies</a>
        <a href="user_management.php">User Management</a>
    <?php endif; ?>
</nav>
