<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Adoption Requests";
$active = "adoption";

/* =========================
FILTERS
========================= */

$search = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$dateFilter = $_GET['date'] ?? '';

$params = [];
$types  = "";

/* =========================
BASE QUERY
========================= */

$sql = "
SELECT 
    adoptions.*, 
    animals.name, 
    animals.type, 
    animals.breed
FROM adoptions
JOIN animals ON animals.id = adoptions.animal_id
WHERE 1=1
";

/* =========================
SEARCH
========================= */

if ($search !== '') {
    $sql .= " AND (
        animals.name LIKE ?
        OR animals.breed LIKE ?
        OR adoptions.adopter_name LIKE ?
        OR adoptions.contact LIKE ?
    )";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "ssss";
}

/* =========================
STATUS FILTER
========================= */

if ($statusFilter !== '') {
    $sql .= " AND adoptions.status = ?";
    $params[] = $statusFilter;
    $types .= "s";
}

/* =========================
DATE FILTER
========================= */

if ($dateFilter !== '') {
    $sql .= " AND adoptions.adoption_date = ?";
    $params[] = $dateFilter;
    $types .= "s";
}

/* =========================
CUSTOM SORTING
========================= */

$sql .= "
ORDER BY 
    CASE 
        WHEN adoptions.status = 'Pending' THEN 1
        WHEN adoptions.status = 'Approved' THEN 2
        WHEN adoptions.status = 'Rejected' THEN 3
        ELSE 4
    END,
    adoptions.id DESC
";

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

<div class="page-header">
    <div class="page-title">
        <h2>Adoption Requests</h2>
    </div>
</div>

<?php if(isset($_GET['success'])): ?>
    <div class="card" style="border-left:4px solid #28a745;">
        <b style="color:#28a745;">✅ Request submitted successfully!</b>
    </div>
<?php endif; ?>

<div class="card">

<form method="GET" class="filter-bar">

<input type="text" 
       name="search" 
       placeholder="Search pet or adopter"
       value="<?= htmlspecialchars($search) ?>">

<select name="status">
    <option value="">All Status</option>
    <option value="Pending" <?= $statusFilter=='Pending'?'selected':'' ?>>Pending</option>
    <option value="Approved" <?= $statusFilter=='Approved'?'selected':'' ?>>Approved</option>
    <option value="Rejected" <?= $statusFilter=='Rejected'?'selected':'' ?>>Rejected</option>
</select>

<input type="date" name="date" value="<?= htmlspecialchars($dateFilter) ?>">

<button class="btn-search">Filter</button>

</form>

<div class="table-wrap">

<table>
<tr>
<th>Pet</th>
<th>Type</th>
<th>Breed</th>
<th>Adopter</th>
<th>Contact</th>
<th>Status</th>
<th>Date</th>
<th>Actions</th>
</tr>

<?php if(!$rows): ?>
<tr><td colspan="8">No records found.</td></tr>
<?php else: ?>

<?php foreach($rows as $r): ?>
<tr>

<td><b><?= htmlspecialchars($r['name'] ?? '') ?></b></td>
<td><?= htmlspecialchars($r['type'] ?? '') ?></td>
<td><?= htmlspecialchars($r['breed'] ?? '') ?></td>
<td><?= htmlspecialchars($r['adopter_name'] ?? '') ?></td>
<td><?= htmlspecialchars($r['contact'] ?? '') ?></td>

<td>
<?php 
$status = $r['status'] ?? 'Pending';

if($status == 'Pending'): ?>
    <span style="color:orange; font-weight:600;">Pending</span>

<?php elseif($status == 'Approved'): ?>
    <span style="color:green; font-weight:600;">Approved</span>

<?php else: ?>
    <span style="color:#dc3545; font-weight:600;">Rejected</span>
<?php endif; ?>
</td>

<td><?= htmlspecialchars($r['adoption_date'] ?? '') ?></td>

<td>
<div class="actions">

<?php if($status == 'Pending'): ?>

    <a href="approve.php?id=<?= $r['id'] ?>" 
       class="action-btn action-success">
       Approve
    </a>

    <a href="reject.php?id=<?= $r['id'] ?>" 
       class="action-btn action-danger">
       Reject
    </a>

<?php else: ?>

    <span style="font-size:12px; color:#888;">No Actions</span>

<?php endif; ?>

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