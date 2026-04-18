<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title  = "Adoption Requests";
$active = "adoption";

/* ==================================================
IMPORTANT:
Siguraduhin meron kang table na adoptions
at may column na status.
================================================== */

/*
RUN THIS SQL IF WALA PA:

CREATE TABLE adoptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT NOT NULL,
    adopter_name VARCHAR(150) NOT NULL,
    contact VARCHAR(100) NOT NULL,
    status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
    adoption_date DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
*/

/* ==================================================
FILTERS
================================================== */

$search       = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$dateFilter   = $_GET['date'] ?? '';

$params = [];
$types  = "";

/* ==================================================
QUERY
Pending / Approved / Rejected lahat lalabas
================================================== */

$sql = "
SELECT
    adoptions.id,
    adoptions.animal_id,
    adoptions.adopter_name,
    adoptions.contact,
    adoptions.status,
    adoptions.adoption_date,
    adoptions.created_at,

    animals.name,
    animals.type,
    animals.breed

FROM adoptions
LEFT JOIN animals ON animals.id = adoptions.animal_id
WHERE 1=1
";

/* ==================================================
SEARCH
================================================== */

if ($search !== '') {
    $sql .= " AND (
        animals.name LIKE ?
        OR animals.breed LIKE ?
        OR adoptions.adopter_name LIKE ?
        OR adoptions.contact LIKE ?
    )";

    $like = "%{$search}%";

    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;

    $types .= "ssss";
}

/* ==================================================
STATUS FILTER
================================================== */

if ($statusFilter !== '') {
    $sql .= " AND adoptions.status = ?";
    $params[] = $statusFilter;
    $types .= "s";
}

/* ==================================================
DATE FILTER
================================================== */

if ($dateFilter !== '') {
    $sql .= " AND DATE(adoptions.created_at) = ?";
    $params[] = $dateFilter;
    $types .= "s";
}

/* ==================================================
SORTING
Pending top
Approved middle
Rejected bottom
================================================== */

$sql .= "
ORDER BY
CASE
    WHEN adoptions.status='Pending'  THEN 1
    WHEN adoptions.status='Approved' THEN 2
    WHEN adoptions.status='Rejected' THEN 3
    ELSE 4
END,
adoptions.id DESC
";

/* ==================================================
EXECUTE
================================================== */

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$rows   = $result->fetch_all(MYSQLI_ASSOC);

ob_start();
?>

<div class="page-header">
    <div class="page-title">
        <h2>Adoption Requests</h2>
    </div>
</div>

<div class="card">

<form method="GET" class="filter-bar">

<input type="text"
       name="search"
       placeholder="Search pet or adopter"
       value="<?= htmlspecialchars($search) ?>">

<select name="status">
    <option value="">All Status</option>
    <option value="Pending"  <?= $statusFilter=='Pending'?'selected':'' ?>>Pending</option>
    <option value="Approved" <?= $statusFilter=='Approved'?'selected':'' ?>>Approved</option>
    <option value="Rejected" <?= $statusFilter=='Rejected'?'selected':'' ?>>Rejected</option>
</select>

<input type="date"
       name="date"
       value="<?= htmlspecialchars($dateFilter) ?>">

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

<tr>
<td colspan="8">No records found.</td>
</tr>

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
$status = $r['status'];

if($status == 'Pending'){
    echo '<span style="color:orange;font-weight:600;">Pending</span>';
}
elseif($status == 'Approved'){
    echo '<span style="color:green;font-weight:600;">Approved</span>';
}
else{
    echo '<span style="color:#dc3545;font-weight:600;">Rejected</span>';
}
?>
</td>

<td>
<?= htmlspecialchars($r['adoption_date'] ?? $r['created_at']) ?>
</td>

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

<span style="font-size:12px;color:#888;">
No Actions
</span>

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
?>