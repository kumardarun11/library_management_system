<?php
// transactions_return.php
include 'header.php';

// -------------------------------
// Build dropdown data: active issues only
// -------------------------------
$activeIssuesStmt = $pdo->query("
    SELECT i.id AS issue_id,
           it.id AS item_id,
           it.serial_no,
           it.title,
           it.author_director,
           m.id AS member_id,
           m.membership_no,
           m.name,
           i.issue_date,
           i.due_date
    FROM issues i
    JOIN items it   ON i.item_id   = it.id
    JOIN members m  ON i.member_id = m.id
    WHERE i.return_date IS NULL
    ORDER BY it.serial_no, m.membership_no
");
$activeIssues = $activeIssuesStmt->fetchAll(PDO::FETCH_ASSOC);

// unique serials and memberships for dropdown
$serialOptions = [];
$memberOptions = [];
foreach ($activeIssues as $row) {
    if (!isset($serialOptions[$row['serial_no']])) {
        $serialOptions[$row['serial_no']] =
            $row['serial_no'] . ' - ' . $row['title'];
    }
    if (!isset($memberOptions[$row['membership_no']])) {
        $memberOptions[$row['membership_no']] =
            $row['membership_no'] . ' - ' . $row['name'];
    }
}

// -------------------------------
// Initial Variables
// -------------------------------
$returnError    = '';
$issueData      = null;
$step           = $_POST['step'] ?? 'load';
$returnDate     = $_POST['return_date'] ?? date('Y-m-d');
$selectedSerial = $_POST['serial_no'] ?? '';
$selectedMember = $_POST['member_no'] ?? '';

// -------------------------------
// When form is submitted
// -------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $serial   = trim($selectedSerial);
    $memberNo = trim($selectedMember);

    // Validate mandatory fields
    if ($serial === '' || $memberNo === '') {
        $returnError = 'Serial No and Membership Number are mandatory.';
    } else {

        // Fetch the Book by Serial No (must be one of issued)
        $stmt = $pdo->prepare("SELECT * FROM items WHERE serial_no = ?");
        $stmt->execute([$serial]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            $returnError = 'No book exists with this Serial Number.';
        } else {

            // Fetch Member
            $stmt = $pdo->prepare("SELECT * FROM members WHERE membership_no = ?");
            $stmt->execute([$memberNo]);
            $member = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$member) {
                $returnError = 'Member not found.';
            } else {

                // Find active issue for this book & member
                $stmt = $pdo->prepare("
                    SELECT * FROM issues
                    WHERE item_id = ?
                      AND member_id = ?
                      AND return_date IS NULL
                    ORDER BY id DESC LIMIT 1
                ");
                $stmt->execute([$item['id'], $member['id']]);
                $issue = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$issue) {
                    $returnError = 'No active (unreturned) issue found for this book and member.';
                } else {

                    // Populate details for display
                    $issueData = [
                        'issue_id'    => $issue['id'],
                        'book_title'  => $item['title'],
                        'author'      => $item['author_director'],
                        'issue_date'  => $issue['issue_date'],
                        'due_date'    => $issue['due_date'],
                        'member_name' => $member['name'],
                    ];

                    // First load → auto-set return date to due date
                    if ($step === 'load') {
                        $returnDate = $issue['due_date'];
                    }

                    // Confirm step
                    elseif ($step === 'confirm') {
                        if ($returnDate === '') {
                            $returnError = 'Return Date is mandatory.';
                        } else {

                            // Fine Calculation – Rs.5 per late day
                            $due = new DateTime($issue['due_date']);
                            $ret = new DateTime($returnDate);

                            $fine = 0;
                            if ($ret > $due) {
                                $daysLate = $due->diff($ret)->days;
                                $fine = $daysLate * 5;
                            }

                            // Update Issue Record
                            $stmt = $pdo->prepare("
                                UPDATE issues
                                SET return_date = ?,
                                    fine_amount  = ?
                                WHERE id = ?
                            ");
                            $stmt->execute([$returnDate, $fine, $issue['id']]);

                            // Redirect to Fine Payment Page (always)
                            header("Location: fine_pay.php?id=" . $issue['id']);
                            exit;
                        }
                    }
                }
            }
        }
    }
}
?>

<!-- UI Section -->
<div class="container">
    <h2>Return Book</h2>

    <div class="info-box">
        Name of Book / Movie – required (auto).<br>
        Author / Director name – automatically populated and non editable.<br>
        Serial No of the book – mandatory (select from issued books).<br>
        Membership Number – mandatory (select from members who have issues).<br>
        Issue Date – automatically populated and non editable.<br>
        Return Date – automatically populated to the due date. It can be edited to a date earlier or later than that.<br>
        With the Confirm option, the user is taken to the Pay Fine page, irrespective of whether fine is there or not.
    </div>

    <form method="post">
        <input type="hidden" name="step" value="<?php echo $issueData ? 'confirm' : 'load'; ?>">

        <!-- Serial Number (dropdown of issued books) -->
        <div class="form-row">
            <label>Serial No *</label>
            <select name="serial_no" required>
                <option value="">-- Select Issued Book / Movie --</option>
                <?php foreach ($serialOptions as $sn => $label): ?>
                    <option value="<?php echo htmlspecialchars($sn); ?>"
                        <?php if ($selectedSerial === $sn) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Membership No (dropdown of members who have an issue) -->
        <div class="form-row">
            <label>Membership Number *</label>
            <select name="member_no" required>
                <option value="">-- Select Member --</option>
                <?php foreach ($memberOptions as $mn => $label): ?>
                    <option value="<?php echo htmlspecialchars($mn); ?>"
                        <?php if ($selectedMember === $mn) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ($issueData): ?>
            <!-- Auto-filled Fields -->
            <div class="form-row">
                <label>Name of Book / Movie *</label>
                <input type="text" value="<?php echo htmlspecialchars($issueData['book_title']); ?>" readonly>
            </div>

            <div class="form-row">
                <label>Author *</label>
                <input type="text" value="<?php echo htmlspecialchars($issueData['author']); ?>" readonly>
            </div>

            <div class="form-row">
                <label>Issue Date *</label>
                <input type="date" value="<?php echo htmlspecialchars($issueData['issue_date']); ?>" readonly>
            </div>

            <div class="form-row">
                <label>Return Date *</label>
                <input type="date" name="return_date"
                       value="<?php echo htmlspecialchars($returnDate); ?>" required>
            </div>

            <button type="submit" class="btn-primary">Confirm &amp; Go to Pay Fine</button>
        <?php else: ?>
            <!-- First step: load details -->
            <button type="submit" class="btn-primary">Load Issue Details</button>
        <?php endif; ?>

        <?php if ($returnError): ?>
            <div class="error-box"><?php echo htmlspecialchars($returnError); ?></div>
        <?php endif; ?>
    </form>
</div>
