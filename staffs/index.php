<?php
include "../includes/auth.php";
include "../includes/config.php";

/* ADMIN SECURITY - BLOCK STAFF ACCESS */
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    
    echo "<script>
    alert('Access Denied. Admin only.');
    window.location='../dashboard.php';
    </script>";
    exit;

}

/* =========================
FILTERS
========================= */

$nameFilter = $_GET['fullname'] ?? '';
$roleFilter = $_GET['role'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$sql = "SELECT * FROM users WHERE role != 'admin'";

if($nameFilter != ""){
$sql .= " AND fullname LIKE '%$nameFilter%'";
}

if($roleFilter != ""){
$sql .= " AND role = '$roleFilter'";
}

$sql .= " ORDER BY id DESC";

$q = mysqli_query($conn,$sql);

include "../includes/header.php";
include "../includes/sidebar.php";
?>

<div class="main">

<div class="page-header">

<div class="page-title">
<h2>User Management</h2>
</div>

<div class="page-action">
<a href="create.php" class="btn-add">+ Add User</a>
<a href="archive.php" class="action-btn action-success">Archive</a>
</div>

</div>

<div class="card">

<form method="GET" class="filter-bar">

<input type="text" 
name="fullname" 
placeholder="Search Name"
value="<?= htmlspecialchars($nameFilter) ?>">

<select name="role">

<option value="">All Roles</option>

<option value="staff" <?= $roleFilter=="staff"?'selected':'' ?>>Staff</option>

<option value="admin" <?= $roleFilter=="admin"?'selected':'' ?>>Admin</option>

</select>

<select name="status">

<option value="">All Status</option>

<option value="IN" <?= $statusFilter=="IN"?'selected':'' ?>>On Duty</option>

<option value="OUT" <?= $statusFilter=="OUT"?'selected':'' ?>>Off Duty</option>

</select>

<button class="btn-search">Filter</button>

</form>

<div class="table-wrap">

<table>

<thead>

<tr>
<th>ID</th>
<th>Name</th>
<th>Role</th>
<th>Status</th>
<th>Actions</th>
</tr>

</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($q)) { ?>

<?php

$uid = $row['id'];

/* CHECK LAST ATTENDANCE */

$last = mysqli_query($conn,"
SELECT type
FROM attendance
WHERE user_id = $uid
ORDER BY time DESC
LIMIT 1
");

$lastType = "OUT";

if($last && mysqli_num_rows($last) > 0){
$r = mysqli_fetch_assoc($last);
$lastType = $r['type'];
}

/* STATUS FILTER */

if($statusFilter != "" && $lastType != $statusFilter){
continue;
}

?>

<tr>

<td><?= $row['id'] ?></td>

<td>
<strong><?= htmlspecialchars($row['fullname']) ?></strong>
</td>

<td><?= $row['role'] ?></td>

<td>

<?php if($lastType == "IN"){ ?>

<span style="color:#198754;font-weight:bold;">ON DUTY</span>

<?php } else { ?>

<span style="color:#dc3545;font-weight:bold;">OFF DUTY</span>

<?php } ?>

</td>

<td>

<div class="actions">

<?php if($lastType == "IN"){ ?>

<a href="time_out.php?id=<?= $row['id'] ?>"
class="action-btn action-danger"
onclick="return confirm('Time OUT this staff?')">
TIME OUT
</a>

<?php } else { ?>

<a href="time_in.php?id=<?= $row['id'] ?>"
class="action-btn action-success"
onclick="return confirm('Time IN this staff?')">
TIME IN
</a>

<?php } ?>

<a href="view.php?id=<?= $row['id'] ?>" class="action-btn action-secondary">View</a>

<a href="edit.php?id=<?= $row['id'] ?>" class="action-btn action-success">Edit</a>

<a href="delete.php?id=<?= $row['id'] ?>"
class="action-btn action-danger"
onclick="return confirm('Delete this user?')">Delete</a>

</div>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>