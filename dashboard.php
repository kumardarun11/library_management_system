<?php
include 'header.php';

// -----------------------------
//  DASHBOARD STATISTICS
// -----------------------------

// Total books
$totalBooks = $pdo->query("SELECT COUNT(*) FROM items WHERE type='book'")->fetchColumn();

// Total movies
$totalMovies = $pdo->query("SELECT COUNT(*) FROM items WHERE type='movie'")->fetchColumn();

// Books currently issued
$booksIssued = $pdo->query("
    SELECT COUNT(*) FROM issues 
    WHERE return_date IS NULL
")->fetchColumn();

// Active members
$activeMembers = $pdo->query("
    SELECT COUNT(*) FROM members 
    WHERE status='active'
")->fetchColumn();

// -----------------------------
//  BOOK SEARCH
// -----------------------------
$searchQuery = $_GET['q'] ?? '';
$searchResults = [];

if ($searchQuery !== '') {
    $stmt = $pdo->prepare("
        SELECT * FROM items 
        WHERE title LIKE ? 
           OR author_director LIKE ? 
           OR serial_no LIKE ?
        ORDER BY title ASC
    ");
    $like = "%$searchQuery%";
    $stmt->execute([$like, $like, $like]);
    $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container">
    <h2>Dashboard</h2>

    <p>Welcome to the Library Management System.</p>

    <!-- -----------------------------------------
         STATISTICS CARDS
    ------------------------------------------ -->
    <div style="display:flex;flex-wrap:wrap;gap:20px;margin:20px 0;">

        <div style="flex:1;min-width:200px;padding:15px;background:#e8f0ff;border-radius:8px;">
            <h3><?php echo $totalBooks; ?></h3>
            <p>Total Books</p>
        </div>

        <div style="flex:1;min-width:200px;padding:15px;background:#fff3cd;border-radius:8px;">
            <h3><?php echo $totalMovies; ?></h3>
            <p>Total Movies</p>
        </div>

        <div style="flex:1;min-width:200px;padding:15px;background:#ffe8e8;border-radius:8px;">
            <h3><?php echo $booksIssued; ?></h3>
            <p>Books Currently Issued</p>
        </div>

        <div style="flex:1;min-width:200px;padding:15px;background:#d4edda;border-radius:8px;">
            <h3><?php echo $activeMembers; ?></h3>
            <p>Active Members</p>
        </div>

    </div>

    <!-- -----------------------------------------
         SEARCH BAR
    ------------------------------------------ -->
    <div class="info-box">
        Search any book or movie by title, author/director, or serial number.
    </div>

    <form method="get" style="margin:20px 0;">
        <input 
            type="text" 
            name="q" 
            placeholder="Search books / movies..." 
            value="<?php echo htmlspecialchars($searchQuery); ?>"
            style="width:70%;padding:10px;border:1px solid #ccc;border-radius:5px;"
        >
        <button type="submit" style="padding:10px 20px;">Search</button>
    </form>

    <!-- -----------------------------------------
         SEARCH RESULTS TABLE
    ------------------------------------------ -->
    <?php if ($searchQuery !== ''): ?>
        <h3>Search Results</h3>

        <?php if (count($searchResults) > 0): ?>

            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Title</th>
                        <th>Author / Director</th>
                        <th>Serial No</th>
                        <th>Category</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($searchResults as $item): ?>

                        <?php
                        // Check if this item is currently issued
                        $stmt = $pdo->prepare("
                            SELECT * FROM issues 
                            WHERE item_id = ? AND return_date IS NULL
                        ");
                        $stmt->execute([$item['id']]);
                        $issued = $stmt->fetch(PDO::FETCH_ASSOC);

                        $status = $issued ? "Issued" : "Available";
                        ?>

                        <tr>
                            <td><?php echo ucfirst($item['type']); ?></td>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><?php echo htmlspecialchars($item['author_director']); ?></td>
                            <td><?php echo htmlspecialchars($item['serial_no']); ?></td>
                            <td><?php echo htmlspecialchars($item['category']); ?></td>
                            <td>
                                <?php if ($issued): ?>
                                    <span style="color:red;">Issued</span>
                                <?php else: ?>
                                    <span style="color:green;">Available</span>
                                <?php endif; ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>
            <p>No matching books or movies found.</p>
        <?php endif; ?>

    <?php endif; ?>


    <!-- -----------------------------------------
         MENU GUIDANCE
    ------------------------------------------ -->
    <ul style="margin-top:30px;margin-left:20px;font-size:14px;">
        <li>Use <strong>Transactions</strong> for Book Available, Issue, Return, Fine Pay.</li>
        <li>Use <strong>Reports</strong> to view issued, overdue, and membership reports.</li>

        <?php if (is_admin()): ?>
            <li>Use <strong>Membership</strong> and <strong>Books/Movies</strong> for Maintenance.</li>
            <li>Use <strong>User Management</strong> to manage library users.</li>
        <?php endif; ?>
    </ul>

</div>

