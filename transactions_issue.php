<?php
include 'header.php';

// Fetch all BOOKS (only type = book)
$stmt = $pdo->prepare("SELECT id, title, author_director FROM items WHERE type = 'book' ORDER BY title ASC");
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all ACTIVE MEMBERS for dropdown
$stmt = $pdo->prepare("SELECT id, membership_no, name FROM members WHERE status = 'active' ORDER BY name ASC");
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Issue Submit
$issueError = $issueMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $bookId     = intval($_POST['book_id'] ?? 0);
    $memberNo   = trim($_POST['member_no'] ?? '');
    $issueDate  = $_POST['issue_date'] ?? '';
    $returnDate = $_POST['return_date'] ?? '';
    $remarks    = trim($_POST['remarks'] ?? '');

    // BASIC REQUIRED VALIDATION
    if (!$bookId || !$memberNo || !$issueDate || !$returnDate) {
        $issueError = "Please fill all mandatory fields and make a valid selection.";
    } else {

        $today = new DateTime(date('Y-m-d'));
        $idate = new DateTime($issueDate);
        $rdate = new DateTime($returnDate);

        // Issue date cannot be earlier than today
        if ($idate < $today) {
            $issueError = "Issue Date cannot be earlier than today.";
        } else {

            // Return Date cannot exceed Issue Date + 15 days
            $maxReturn = clone $idate;
            $maxReturn->modify('+15 days');

            if ($rdate > $maxReturn) {
                $issueError = "Return Date cannot be greater than 15 days after Issue Date.";
            } else {

                // Validate Member
                $stmt = $pdo->prepare("SELECT * FROM members WHERE membership_no = ? AND status = 'active'");
                $stmt->execute([$memberNo]);
                $member = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$member) {
                    $issueError = "Active member not found.";
                } else {
                    // Insert Issue Record
                    $stmt = $pdo->prepare("
                        INSERT INTO issues (item_id, member_id, issue_date, due_date, remarks)
                        VALUES (?,?,?,?,?)
                    ");
                    $stmt->execute([$bookId, $member['id'], $issueDate, $returnDate, $remarks]);

                    $issueMsg = "Book issued successfully.";
                }
            }
        }
    }
}
?>

<div class="container">
    <h2>Book/Movie Issue</h2>

    <div class="info-box">
        ✔ Name of book required<br>
        ✔ Author auto‑populated and non‑editable<br>
        ✔ Issue Date cannot be earlier than today<br>
        ✔ Return Date auto = 15 days ahead (editable earlier, not later)<br>
        ✔ Remarks optional<br>
        ✔ If form incomplete → error shown on same page
    </div>

    <form method="post">

        <!-- Book Dropdown -->
        <div class="form-row">
            <label>Select Book/Movie *</label>
            <select name="book_id" id="bookSelect" required>
                <option value="">-- Select Book/Movie --</option>
                <?php foreach ($books as $b): ?>
                    <option value="<?php echo $b['id']; ?>"
                            data-author="<?php echo htmlspecialchars($b['author_director']); ?>">
                        <?php echo htmlspecialchars($b['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Auto Author -->
        <div class="form-row">
            <label>Author (Auto) *</label>
            <input type="text" id="authorField" readonly>
        </div>

        <!-- Membership Dropdown -->
        <div class="form-row">
            <label>Select Member *</label>
            <select name="member_no" required>
                <option value="">-- Select Member --</option>
                <?php foreach ($members as $m): ?>
                    <option value="<?php echo $m['membership_no']; ?>">
                        <?php echo htmlspecialchars($m['membership_no'] . " - " . $m['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Issue Date -->
        <div class="form-row">
            <label>Issue Date *</label>
            <input type="date" name="issue_date" id="issueDate" required>
        </div>

        <!-- Return Date -->
        <div class="form-row">
            <label>Return Date *</label>
            <input type="date" name="return_date" id="returnDate" required>
        </div>

        <!-- Remarks Optional -->
        <div class="form-row">
            <label>Remarks (Optional)</label>
            <textarea name="remarks"></textarea>
        </div>

        <button type="submit">Issue Book</button>

        <?php if ($issueError): ?><div class="error"><?php echo $issueError; ?></div><?php endif; ?>
        <?php if ($issueMsg): ?><div class="success"><?php echo $issueMsg; ?></div><?php endif; ?>
    </form>
</div>

<script>
// Auto-fill author on book select
document.getElementById('bookSelect').addEventListener('change', function () {
    const author = this.options[this.selectedIndex].dataset.author || "";
    document.getElementById('authorField').value = author;
});

// Set Issue Date = Today
const today = new Date().toISOString().slice(0, 10);
document.getElementById('issueDate').value = today;

// Auto-set Return Date = Issue Date + 15 days
function updateReturnDate() {
    const issue = new Date(document.getElementById('issueDate').value);
    if (!isNaN(issue)) {
        const ret = new Date(issue);
        ret.setDate(ret.getDate() + 15);
        document.getElementById('returnDate').value = ret.toISOString().slice(0, 10);
    }
}
updateReturnDate();

// When Issue Date changes → update Return Date
document.getElementById('issueDate').addEventListener('change', updateReturnDate);
</script>

