<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Rescue Operations";
$active = "rescues";

/* =========================
FILTERS
========================= */

$search     = trim($_GET['search'] ?? '');
$status     = $_GET['status'] ?? '';
$dateFilter = $_GET['date'] ?? '';
$order      = ($_GET['sort'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

$params = [];
$types  = "";

/* =========================
BASE QUERY
========================= */

$sql = "
SELECT r.*, a.name 
FROM rescues r
JOIN animals a ON a.id = r.animal_id
WHERE 1=1
";

/* =========================
SEARCH
========================= */

if ($search !== '') {
    $sql .= " AND (
        a.name LIKE ?
        OR r.location LIKE ?
    )";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}

/* =========================
STATUS FILTER
========================= */

if ($status !== '') {
    $sql .= " AND r.status = ?";
    $params[] = $status;
    $types .= "s";
}

/* =========================
DATE FILTER
========================= */

if ($dateFilter !== '') {
    $sql .= " AND r.rescue_date = ?";
    $params[] = $dateFilter;
    $types .= "s";
}

$sql .= " ORDER BY r.id $order";

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

ob_start();
?>

<h2>Rescue Operations</h2>

<a href="create.php" class="btn-add">+ New Rescue</a>

<div class="card">

<form method="GET" class="filter-bar">

<input type="text" 
       name="search" 
       placeholder="Search animal or location"
       value="<?= htmlspecialchars($search) ?>">

<select name="status">
    <option value="">All Status</option>
    <option value="ongoing" <?= $status=='ongoing'?'selected':'' ?>>Ongoing</option>
    <option value="completed" <?= $status=='completed'?'selected':'' ?>>Completed</option>
    <option value="failed" <?= $status=='failed'?'selected':'' ?>>Failed</option>
</select>

<input type="date" name="date" value="<?= htmlspecialchars($dateFilter) ?>">

<select name="sort">
    <option value="DESC" <?= $order==='DESC'?'selected':'' ?>>Newest</option>
    <option value="ASC" <?= $order==='ASC'?'selected':'' ?>>Oldest</option>
</select>

<button class="btn-search">Filter</button>

</form>

<table>

<tr>
<th>Animal</th>
<th>Date</th>
<th>Location</th>
<th>Status</th>
<th>Actions</th>
</tr>

<?php if(!$rows): ?>
<tr>
<td colspan="5">No records found.</td>
</tr>
<?php else: ?>

<?php foreach($rows as $r): ?>
<tr>
<td><?= $r['name'] ?></td>
<td><?= $r['rescue_date'] ?></td>
<td><?= $r['location'] ?></td>

<td>
<?php if($r['status']=='ongoing'): ?>
<span style="color:orange;">Ongoing</span>
<?php elseif($r['status']=='completed'): ?>
<span style="color:green;">Completed</span>
<?php else: ?>
<span style="color:red;">Failed</span>
<?php endif; ?>
</td>

<td>
<a href="view.php?id=<?= $r['id'] ?>" class="btn-save btn-sm">View</a>
<a href="edit.php?id=<?= $r['id'] ?>" class="btn-save btn-sm">Edit</a>
<a href="assign.php?id=<?= $r['id'] ?>" class="btn-save btn-sm">Assign</a>
</td>
</tr>
<?php endforeach; ?>

<?php endif; ?>

</table>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";