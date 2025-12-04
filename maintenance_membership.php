<?php
// maintenance_membership.php
include 'header.php';
// Fetch all membership numbers for dropdown
$membersListStmt = $pdo->query("SELECT membership_no, name, status FROM members ORDER BY membership_no ASC");
$membersList = $membersListStmt->fetchAll(PDO::FETCH_ASSOC);

if (!is_admin()) {
    echo '<div class="container"><p class="error">Only admin can access Maintenance.</p></div>';
    include 'footer.php';
    exit;
}

$addError = $addMsg = $updError = $updMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['form'] === 'add') {
        $name = trim($_POST['name'] ?? '');
        $duration = $_POST['duration'] ?? '6m';

        if ($name === '') {
            $addError = 'All fields are mandatory.';
        } else {
            // create membership number simple: M + time()
            $membershipNo = 'M' . time();
            $start = new DateTime();
            $expiry = clone $start;
            if ($duration === '1y') $expiry->modify('+1 year');
            elseif ($duration === '2y') $expiry->modify('+2 years');
            else $expiry->modify('+6 months');

            // IMPORTANT: insert status = 'active'
            $stmt = $pdo->prepare("
                INSERT INTO members (membership_no, name, start_date, expiry_date, status)
                VALUES (?,?,?,?,?)
            ");
            $stmt->execute([
                $membershipNo,
                $name,
                $start->format('Y-m-d'),
                $expiry->format('Y-m-d'),
                'active'
            ]);
            $addMsg = "Membership added. Number: $membershipNo";
        }
    }

    if ($_POST['form'] === 'update') {
        $no = trim($_POST['membership_no'] ?? '');
        $action = $_POST['action'] ?? 'extend';

        if ($no === '') {
            $updError = 'Membership Number is mandatory.';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM members WHERE membership_no = ?");
            $stmt->execute([$no]);
            $mem = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$mem) {
                $updError = 'Membership not found.';
            } else {
                if ($action === 'cancel') {
                    $stmt = $pdo->prepare("UPDATE members SET status = 'cancelled' WHERE id = ?");
                    $stmt->execute([$mem['id']]);
                    $updMsg = 'Membership cancelled.';
                } else {
                    // extend 6 months by default
                    $expiry = new DateTime($mem['expiry_date']);
                    $expiry->modify('+6 months');
                    $stmt = $pdo->prepare("UPDATE members SET expiry_date = ? WHERE id = ?");
                    $stmt->execute([$expiry->format('Y-m-d'), $mem['id']]);
                    $updMsg = 'Membership extended by 6 months.';
                }
            }
        }
    }
}
?>
<div class="container">
    <h2>Maintenance - Membership</h2>

    <h3>Add Membership</h3>
    <div class="info-box">
        All fields mandatory and the user needs to select one option: 6 months / 1 year / 2 years.
        By default 6 months is selected.
    </div>
    <form method="post">
        <input type="hidden" name="form" value="add">
        <div class="form-row">
            <label>Member Name *</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-row">
            <label>Membership Duration *</label>
            <div class="radio-group">
                <label><input type="radio" name="duration" value="6m" checked> 6 Months</label>
                <label><input type="radio" name="duration" value="1y"> 1 Year</label>
                <label><input type="radio" name="duration" value="2y"> 2 Years</label>
            </div>
        </div>
        <button type="submit">Add Membership</button>
        <?php if ($addError): ?><div class="error"><?php echo htmlspecialchars($addError); ?></div><?php endif; ?>
        <?php if ($addMsg): ?><div class="success"><?php echo htmlspecialchars($addMsg); ?></div><?php endif; ?>
    </form>

    <h3 style="margin-top:25px;">Update Membership</h3>
<div class="info-box">
    Membership Number is mandatory; user can extend (default 6 months) or cancel membership.
</div>

<form method="post">
    <input type="hidden" name="form" value="update">

    <!-- Membership dropdown -->
    <div class="form-row">
        <label>Membership Number *</label>
        <select name="membership_no" required>
            <option value="">-- Select Membership No --</option>

            <?php foreach ($membersList as $m): ?>
                <option value="<?php echo htmlspecialchars($m['membership_no']); ?>">
                    <?php echo htmlspecialchars($m['membership_no'] . " - " . $m['name'] . " (" . $m['status'] . ")"); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Action -->
    <div class="form-row">
        <label>Action *</label>
        <div class="radio-group">
            <label><input type="radio" name="action" value="extend" checked> Extend (6 Months)</label>
            <label><input type="radio" name="action" value="cancel"> Cancel Membership</label>
        </div>
    </div>

    <button type="submit">Update Membership</button>

    <?php if ($updError): ?><div class="error"><?php echo htmlspecialchars($updError); ?></div><?php endif; ?>
    <?php if ($updMsg): ?><div class="success"><?php echo htmlspecialchars($updMsg); ?></div><?php endif; ?>
</form>

</div>

