<?php
include 'header.php';

if (!is_admin()) {
    echo '<div class="container"><p class="error">Only admin can view all books.</p></div>';
    include 'footer.php';
    exit;
}

$stmt = $pdo->query("SELECT * FROM items ORDER BY created_at DESC");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>All Books / Movies</h2>

    <div class="info-box">
        This page displays every book or movie entered through the <b>Add Book</b> or <b>Update Book</b> form.
    </div>

    <?php if ($items): ?>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Title</th>
                    <th>Author / Director</th>
                    <th>Serial No</th>
                    <th>Category</th>
                    <th>Date Added</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $i): ?>
                    <tr>
                        <td><?php echo ucfirst($i['type']); ?></td>
                        <td><?php echo htmlspecialchars($i['title']); ?></td>
                        <td><?php echo htmlspecialchars($i['author_director']); ?></td>
                        <td><?php echo htmlspecialchars($i['serial_no']); ?></td>
                        <td><?php echo htmlspecialchars($i['category']); ?></td>
                        <td><?php echo $i['created_at']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No books or movies added yet.</p>
    <?php endif; ?>
</div>
