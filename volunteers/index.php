<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Volunteers";
$active = "volunteers";

/* =========================
FILTERS
========================= */

$search = trim($_GET['search'] ?? '');
$role   = $_GET['role'] ?? '';
$order  = ($_GET['sort'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

$params = [];
$types  = "";

/* =========================
BASE QUERY
========================= */

$sql = "SELECT * FROM volunteers WHERE 1=1";

/* =========================
SEARCH
========================= */

if ($search !== '') {
    $sql .= " AND (
        name LIKE ?
        OR contact LIKE ?
        OR role LIKE ?
    )";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";
}

/* =========================
ROLE FILTER
========================= */

if ($role !== '') {
    $sql .= " AND role = ?";
    $params[] = $role;
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

ob_start();
?>

<h2>Volunteers</h2>
<p style="color:#666;margin-bottom:15px;">Manage volunteer records</p>

<a href="create.php" class="btn-add" style="margin-bottom:10px;">+ Add Volunteer</a>

<div class="card">

<form method="GET" class="filter-bar">

<input type="text" 
       name="search" 
       placeholder="Search volunteer"
       value="<?= htmlspecialchars($search) ?>">

<input type="text" 
       name="role" 
       placeholder="Filter by role"
       value="<?= htmlspecialchars($role) ?>">

<select name="sort">
    <option value="DESC" <?= $order==='DESC'?'selected':'' ?>>Newest</option>
    <option value="ASC" <?= $order==='ASC'?'selected':'' ?>>Oldest</option>
</select>

<button class="btn-search">Filter</button>

</form>

<div class="table-wrap">
<table>

<tr>
<th>Name</th>
<th>Contact</th>
<th>Role</th>
<th>Actions</th>
</tr>

<?php if(!$rows): ?>
<tr><td colspan="4">No volunteers found.</td></tr>
<?php else: ?>

<?php foreach($rows as $r): ?>
<tr>
<td><b><?= htmlspecialchars($r['name']) ?></b></td>
<td><?= htmlspecialchars($r['contact']) ?></td>
<td><?= htmlspecialchars($r['role']) ?></td>

<td>
<div class="actions">
<a href="view.php?id=<?= $r['id'] ?>" class="btn-save btn-sm">View</a>
<a href="edit.php?id=<?= $r['id'] ?>" class="btn-save btn-sm">Edit</a>
<a href="archive.php?id=<?= $r['id'] ?>" class="btn-decline btn-sm"
onclick="return confirm('Delete this volunteer?')">Delete</a>
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