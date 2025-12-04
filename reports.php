<?php
include 'header.php';

// ---- 1. Books Issued Today ----
$today = date('Y-m-d');
$stmt = $pdo->prepare("
    SELECT i.id, it.title, it.author_director, m.name AS member_name, i.issue_date, i.due_date
    FROM issues i
    JOIN items it ON it.id = i.item_id
    JOIN members m ON m.id = i.member_id
    WHERE i.issue_date = ?
");
$stmt->execute([$today]);
$issuedToday = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ---- 2. Overdue Books (not returned) ----
$stmt = $pdo->prepare("
    SELECT i.id, it.title, it.author_director, m.name AS member_name,
           i.issue_date, i.due_date, i.return_date, i.fine_amount
    FROM issues i
    JOIN items it ON i.item_id = it.id
    JOIN members m ON i.member_id = m.id
    WHERE i.return_date IS NULL
      AND i.due_date < CURDATE()
");
$stmt->execute();
$overdue = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ---- 3a. All active memberships ----
$stmt = $pdo->prepare("
    SELECT membership_no, name, start_date, expiry_date, status
    FROM members
    WHERE status = 'active'
    ORDER BY expiry_date ASC
");
$stmt->execute();
$activeMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ---- 3b. Memberships expiring THIS month (subset) ----
$stmt = $pdo->prepare("
    SELECT membership_no, name, start_date, expiry_date, status
    FROM members
    WHERE status = 'active'
      AND MONTH(expiry_date) = MONTH(CURDATE())
      AND YEAR(expiry_date)  = YEAR(CURDATE())
    ORDER BY expiry_date ASC
");
$stmt->execute();
$expiring = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Reports</h2>

    <!-- REPORT 1: Books issued today -->
    <h3 style="margin-top:25px;">📘 Books Issued Today (<?php echo $today; ?>)</h3>
    <?php if ($issuedToday): ?>
        <table>
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Author</th>
                    <th>Member</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($issuedToday as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['author_director']); ?></td>
                    <td><?php echo htmlspecialchars($row['member_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['issue_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No books were issued today.</p>
    <?php endif; ?>

    <!-- REPORT 2: Overdue books -->
    <h3 style="margin-top:35px;">⚠️ Overdue Books (Not Returned)</h3>
    <?php if ($overdue): ?>
        <table>
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Author</th>
                    <th>Member</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Fine (₹)</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($overdue as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['author_director']); ?></td>
                    <td><?php echo htmlspecialchars($row['member_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['issue_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['fine_amount']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No overdue books right now.</p>
    <?php endif; ?>

    <!-- REPORT 3a: All active memberships -->
    <h3 style="margin-top:35px;">👥 All Active Memberships</h3>
    <?php if ($activeMembers): ?>
        <table>
            <thead>
                <tr>
                    <th>Membership No</th>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($activeMembers as $m): ?>
                <tr>
                    <td><?php echo htmlspecialchars($m['membership_no']); ?></td>
                    <td><?php echo htmlspecialchars($m['name']); ?></td>
                    <td><?php echo htmlspecialchars($m['start_date']); ?></td>
                    <td><?php echo htmlspecialchars($m['expiry_date']); ?></td>
                    <td><?php echo htmlspecialchars($m['status']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No active memberships found.</p>
    <?php endif; ?>

    <!-- REPORT 3b: Memberships expiring this month -->
    <h3 style="margin-top:35px;">⏳ Memberships Expiring This Month</h3>
    <?php if ($expiring): ?>
        <table>
            <thead>
                <tr>
                    <th>Membership No</th>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>Expiry Date</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($expiring as $m): ?>
                <tr>
                    <td><?php echo htmlspecialchars($m['membership_no']); ?></td>
                    <td><?php echo htmlspecialchars($m['name']); ?></td>
                    <td><?php echo htmlspecialchars($m['start_date']); ?></td>
                    <td><?php echo htmlspecialchars($m['expiry_date']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No memberships are expiring this month.</p>
    <?php endif; ?>

</div>

