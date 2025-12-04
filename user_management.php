<?php
// user_management.php
include 'header.php';

if (!is_admin()) {
    echo '<div class="container"><p class="error">Only admin can access User Management.</p></div>';
    include 'footer.php';
    exit;
}

$error = $msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? 'new';
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if ($name === '') {
        $error = 'Name is mandatory.';
    } else {
        if ($type === 'new') {
            if ($password === '') {
                $error = 'Password required for new user.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?,?,?)");
                try {
                    $stmt->execute([$name, hash('sha256', $password), $role]);
                    $msg = 'New user created.';
                } catch (PDOException $e) {
                    $error = 'Error: ' . $e->getMessage();
                }
            }
        } else {
            // existing: simple demo (no real update)
            $msg = 'Existing user operation simulated (no DB change).';
        }
    }
}
?>
<div class="container">
    <h2>User Management</h2>
    <div class="info-box">
        One of the options New User or Existing must be selected (default New).<br>
        Name is mandatory.
    </div>
    <form method="post">
        <div class="form-row">
            <label>User Type *</label>
            <div class="radio-group">
                <label><input type="radio" name="type" value="new" checked> New User</label>
                <label><input type="radio" name="type" value="existing"> Existing User</label>
            </div>
        </div>
        <div class="form-row">
            <label>User Name *</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-row">
            <label>Password (for new user)</label>
            <input type="password" name="password">
        </div>
        <div class="form-row">
            <label>Role</label>
            <select name="role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit">Save User</button>
        <?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <?php if ($msg): ?><div class="success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    </form>
</div>
