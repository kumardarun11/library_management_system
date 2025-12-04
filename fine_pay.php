<?php
// fine_pay.php
include 'header.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    echo '<div class="container"><p class="error">Invalid transaction.</p></div>';
    include 'footer.php';
    exit;
}

// Load issue with joins
$stmt = $pdo->prepare("SELECT i.*, it.title, it.author_director, m.name AS member_name
                       FROM issues i
                       JOIN items it ON i.item_id = it.id
                       JOIN members m ON i.member_id = m.id
                       WHERE i.id = ?");
$stmt->execute([$id]);
$issue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
    echo '<div class="container"><p class="error">Transaction not found.</p></div>';
    include 'footer.php';
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $finePaid = isset($_POST['fine_paid']) ? 1 : 0;
    $remarks = trim($_POST['remarks'] ?? '');

    if ($issue['fine_amount'] > 0 && !$finePaid) {
        $error = 'Pending fine exists. Please check "Fine Paid" to complete the transaction.';
    } else {
        $stmt = $pdo->prepare("UPDATE issues SET fine_paid = ?, remarks = ? WHERE id = ?");
        $stmt->execute([$finePaid, $remarks, $id]);
        $success = 'Return book transaction completed successfully.';
        // Reload updated issue
        $stmt = $pdo->prepare("SELECT * FROM issues WHERE id = ?");
        $stmt->execute([$id]);
        $issue = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<div class="container">
    <h2>Pay Fine</h2>
    <div class="info-box">
        All the fields are populated except <b>Fine Paid</b> and <b>Remarks</b>.<br>
        If there is no calculated fine, user can press Confirm and transaction completes.<br>
        For a pending fine, the <b>Fine Paid</b> checkbox must be selected before completion.<br>
        If form is submitted without these details, an error is displayed and book will not be returned till then.
    </div>

    <form method="post">
        <div class="form-row">
            <label>Book / Movie Name</label>
            <input type="text" value="<?php echo htmlspecialchars($issue['title']); ?>" readonly>
        </div>
        <div class="form-row">
            <label>Member Name</label>
            <input type="text" value="<?php echo htmlspecialchars($issue['member_name']); ?>" readonly>
        </div>
        <div class="form-row">
            <label>Fine Amount</label>
            <input type="number" value="<?php echo htmlspecialchars($issue['fine_amount']); ?>" readonly>
        </div>
        <div class="form-row checkbox-group">
            <label><input type="checkbox" name="fine_paid" <?php if($issue['fine_paid']) echo 'checked'; ?>> Fine Paid</label>
        </div>
        <div class="form-row">
            <label>Remarks</label>
            <textarea name="remarks"><?php echo htmlspecialchars($issue['remarks'] ?? ''); ?></textarea>
        </div>
        <button type="submit">Confirm Return</button>
        <?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <?php if ($success): ?><div class="success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    </form>
</div>
