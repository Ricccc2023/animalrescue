<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Donations";
$active = "donations";

/* =========================
FILTERS
========================= */

$search = trim($_GET['search'] ?? '');
$dateFilter = $_GET['date'] ?? '';
$order = ($_GET['sort'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

$params = [];
$types  = "";

/* =========================
BASE QUERY
========================= */

$sql = "SELECT * FROM donations WHERE 1=1";

/* =========================
SEARCH
========================= */

if ($search !== '') {
    $sql .= " AND donor_name LIKE ?";
    $like = "%$search%";
    $params[] = $like;
    $types .= "s";
}

/* =========================
DATE FILTER
========================= */

if ($dateFilter !== '') {
    $sql .= " AND donation_date = ?";
    $params[] = $dateFilter;
    $types .= "s";
}

$sql .= " ORDER BY id $order";

/* =========================
EXECUTE
========================= */

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);

/* =========================
TOTAL (FILTERED)
========================= */

$totalSql = "SELECT SUM(amount) as total FROM donations WHERE 1=1";

if ($search !== '') {
    $totalSql .= " AND donor_name LIKE ?";
}

if ($dateFilter !== '') {
    $totalSql .= " AND donation_date = ?";
}

$totalStmt = $conn->prepare($totalSql);

if (!empty($params)) {
    $totalStmt->bind_param($types, ...$params);
}

$totalStmt->execute();
$total = $totalStmt->get_result()->fetch_assoc()['total'] ?? 0;

/* =========================
PENDING
========================= */

$pendingQuery = $conn->query("SELECT COUNT(*) as total FROM pending_donations");
$pendingData = $pendingQuery->fetch_assoc();
$hasPending = $pendingData['total'] > 0;
$pendingCount = $pendingData['total'];

ob_start();
?>

<h2>Donations</h2>
<p style="color:#666;margin-bottom:15px;">Track all donations</p>

<div class="card" style="margin-bottom:15px;">
    <b>Total Donations:</b> ₱ <?= number_format($total,2) ?>
</div>

<!-- BUTTONS -->
<a href="create.php" class="btn-add" style="margin-bottom:10px;">
    + Add Donation
</a>

<?php if($hasPending): ?>
    <a href="../pending_donations/index.php" class="btn-add" style="margin-bottom:10px;">
        New Donation (<?= $pendingCount ?>)
    </a>
<?php endif; ?>

<div class="card">

<form method="GET" class="filter-bar">

<input type="text" 
       name="search" 
       placeholder="Search donor"
       value="<?= htmlspecialchars($search) ?>">

<input type="date" name="date" value="<?= htmlspecialchars($dateFilter) ?>">

<select name="sort">
    <option value="DESC" <?= $order==='DESC'?'selected':'' ?>>Newest</option>
    <option value="ASC" <?= $order==='ASC'?'selected':'' ?>>Oldest</option>
</select>

<button class="btn-search">Filter</button>

</form>

<div class="table-wrap">
<table>

<tr>
    <th>Donor</th>
    <th>Amount</th>
    <th>Date</th>
    <th>Actions</th>
</tr>

<?php if(!$rows): ?>
<tr>
    <td colspan="4">No donations found.</td>
</tr>
<?php else: ?>

<?php foreach($rows as $r): ?>
<tr>
    <td><b><?= htmlspecialchars($r['donor_name']) ?></b></td>
    <td>₱ <?= number_format($r['amount'],2) ?></td>
    <td><?= $r['donation_date'] ?></td>

    <td>
        <div class="actions">
            <a href="view.php?id=<?= $r['id'] ?>" class="btn-save btn-sm">View</a>
            <a href="edit.php?id=<?= $r['id'] ?>" class="btn-save btn-sm">Edit</a>
            <a href="archive.php?id=<?= $r['id'] ?>" class="btn-decline btn-sm"
            onclick="return confirm('Delete this record?')">
            Delete
            </a>
        </div>
    </td>
</tr>
<?php endforeach; ?>

<?php endif; ?>

</table>
</div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";
?>