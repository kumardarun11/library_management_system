<?php
// maintenance_items.php
include 'header.php';

if (!is_admin()) {
    echo '<div class="container"><p class="error">Only admin can access Maintenance.</p></div>';
    include 'footer.php';
    exit;
}

$addError = $addMsg = $updError = $updMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['form'] === 'add') {
        $type = $_POST['type'] ?? 'book';
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $serial = trim($_POST['serial'] ?? '');
        $category = trim($_POST['category'] ?? '');

        if ($title === '' || $author === '' || $serial === '') {
            $addError = 'All fields are mandatory.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO items (type, title, author_director, serial_no, category)
                                   VALUES (?,?,?,?,?)");
            try {
                $stmt->execute([$type, $title, $author, $serial, $category]);
                $addMsg = 'Item added successfully.';
            } catch (PDOException $e) {
                $addError = 'Error: ' . $e->getMessage();
            }
        }
    }

    if ($_POST['form'] === 'update') {
        $type = $_POST['type'] ?? 'book';
        $serial = trim($_POST['serial'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');

        if ($serial === '' || $title === '' || $author === '') {
            $updError = 'All fields are mandatory.';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM items WHERE serial_no = ? AND type = ?");
            $stmt->execute([$serial, $type]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$item) {
                $updError = 'Item not found.';
            } else {
                $stmt = $pdo->prepare("UPDATE items SET title = ?, author_director = ? WHERE id = ?");
                $stmt->execute([$title, $author, $item['id']]);
                $updMsg = 'Item updated successfully.';
            }
        }
    }
}
?>
<div class="container">
    <h2>Maintenance - Books / Movies</h2>

    <h3>Add Book / Movie</h3>
    <div class="info-box">
        One of the options Movie or Book must be selected (default Book).<br>
        All fields mandatory. Error message appears if user submits incomplete form.
    </div>
    <form method="post">
        <input type="hidden" name="form" value="add">
        <div class="form-row">
            <label>Type *</label>
            <div class="radio-group">
                <label><input type="radio" name="type" value="book" checked> Book</label>
                <label><input type="radio" name="type" value="movie"> Movie</label>
            </div>
        </div>
        <div class="form-row">
            <label>Title *</label>
            <input type="text" name="title" required>
        </div>
        <div class="form-row">
            <label>Author / Director *</label>
            <input type="text" name="author" required>
        </div>
        <div class="form-row">
            <label>Serial No *</label>
            <input type="text" name="serial" required>
        </div>
        <div class="form-row">
            <label>Category</label>
            <input type="text" name="category">
        </div>
        <button type="submit">Add</button>
        <?php if ($addError): ?><div class="error"><?php echo htmlspecialchars($addError); ?></div><?php endif; ?>
        <?php if ($addMsg): ?><div class="success"><?php echo htmlspecialchars($addMsg); ?></div><?php endif; ?>
    </form>

    <h3 style="margin-top:25px;">Update Book / Movie</h3>
    <div class="info-box">
        One of the options Movie or Book must be selected (default Book).<br>
        All fields mandatory. Error thrown if user submits incomplete form.
    </div>
    <form method="post">
        <input type="hidden" name="form" value="update">
        <div class="form-row">
            <label>Type *</label>
            <div class="radio-group">
                <label><input type="radio" name="type" value="book" checked> Book</label>
                <label><input type="radio" name="type" value="movie"> Movie</label>
            </div>
        </div>
        <div class="form-row">
            <label>Serial No (to Search) *</label>
            <input type="text" name="serial" required>
        </div>
        <div class="form-row">
            <label>New Title *</label>
            <input type="text" name="title" required>
        </div>
        <div class="form-row">
            <label>New Author / Director *</label>
            <input type="text" name="author" required>
        </div>
        <button type="submit">Update</button>
        <?php if ($updError): ?><div class="error"><?php echo htmlspecialchars($updError); ?></div><?php endif; ?>
        <?php if ($updMsg): ?><div class="success"><?php echo htmlspecialchars($updMsg); ?></div><?php endif; ?>
    </form>
</div>
