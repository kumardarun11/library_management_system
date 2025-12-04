<?php
include 'config.php';

$serial = $_GET['serial'] ?? '';
$member = $_GET['member'] ?? '';

header('Content-Type: application/json');

if ($serial === '' || $member === '') {
    echo json_encode(["status" => "error", "message" => "Enter Serial No & Membership No"]);
    exit;
}

// Fetch Item
$stmt = $pdo->prepare("SELECT * FROM items WHERE serial_no = ?");
$stmt->execute([$serial]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo json_encode(["status" => "error", "message" => "No book found with this Serial No"]);
    exit;
}

// Fetch Member
$stmt = $pdo->prepare("SELECT * FROM members WHERE membership_no = ?");
$stmt->execute([$member]);
$mem = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mem) {
    echo json_encode(["status" => "error", "message" => "Member Not Found"]);
    exit;
}

// Fetch Active Issue
$stmt = $pdo->prepare("
    SELECT * FROM issues
    WHERE item_id = ? AND member_id = ? AND return_date IS NULL
    ORDER BY id DESC LIMIT 1
");
$stmt->execute([$item['id'], $mem['id']]);
$issue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
    echo json_encode(["status" => "error", "message" => "No active issue found for this member"]);
    exit;
}

// SUCCESS — Return Data
echo json_encode([
    "status"      => "success",
    "issue_id"    => $issue['id'],
    "book_title"  => $item['title'],
    "author"      => $item['author_director'],
    "issue_date"  => $issue['issue_date'],
    "due_date"    => $issue['due_date']
]);
exit;
?>
