<?php
require_once "../includes/config.php";
require_once "../includes/auth.php";

/* ADMIN SECURITY */
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
echo "<script>
alert('Access Denied. Admin only.');
window.location='../dashboard.php';
</script>";
exit;
}

/* FILTER */
$month = $_GET['month'] ?? date("m");
$year  = $_GET['year'] ?? date("Y");

/* UPDATE PER DAY */
if(isset($_POST['update_salary'])){
$id = intval($_POST['user_id']);
$salary = floatval($_POST['per_day']);

mysqli_query($conn,"
UPDATE users
SET per_day = '$salary'
WHERE id = $id
");
}

/* FETCH STAFF */
$q = mysqli_query($conn,"
SELECT *
FROM users
WHERE role='staff'
ORDER BY fullname ASC
");

include "../includes/header.php";
include "../includes/sidebar.php";
?>

<div class="main">

<div class="page-header">

<div class="page-title">
<h2>Payroll Management</h2>
</div>

</div>

<!-- FILTER -->
<div class="card">

<form method="GET" class="filter-bar">

<select name="month">
<?php for($m=1;$m<=12;$m++): ?>
<option value="<?= $m ?>" <?= $month==$m?'selected':'' ?>>
<?= date("F",mktime(0,0,0,$m,1)) ?>
</option>
<?php endfor; ?>
</select>

<select name="year">
<?php for($y=date("Y");$y>=2024;$y--): ?>
<option value="<?= $y ?>" <?= $year==$y?'selected':'' ?>>
<?= $y ?>
</option>
<?php endfor; ?>
</select>

<button class="btn-search">Filter</button>

</form>

</div>

<div class="card">

<div class="table-wrap">

<table>

<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Per Day</th>
<th>Days Worked</th>
<th>Total Salary</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($q)): ?>

<?php
/* ATTENDANCE COUNT */
$userId = $row['id'];

$attendance = [];

$res = mysqli_query($conn,"
SELECT DATE(time) as day
FROM attendance
WHERE user_id=$userId
AND type='IN'
AND MONTH(time)='$month'
AND YEAR(time)='$year'
");

while($r=mysqli_fetch_assoc($res)){
$attendance[$r['day']] = true;
}

$daysWorked = count($attendance);

/* SALARY */
$perDay = $row['per_day'];
$totalSalary = $daysWorked * $perDay;
?>

<tr>

<td><?= $row['id'] ?></td>

<td>
<strong><?= htmlspecialchars($row['fullname']) ?></strong>
</td>

<td>

<form method="POST" style="display:flex;gap:5px;">

<input type="hidden" name="user_id" value="<?= $row['id'] ?>">

<input type="number"
name="per_day"
value="<?= $row['per_day'] ?>"
step="0.01"
style="width:100px;">

<button name="update_salary"
class="action-btn action-success">
Save
</button>

</form>

</td>

<td><?= $daysWorked ?></td>

<td>
<strong style="color:#198754;">
₱<?= number_format($totalSalary,2) ?>
</strong>
</td>

<td>

<div class="actions">

<a href="view.php?id=<?= $row['id'] ?>&month=<?= $month ?>&year=<?= $year ?>"
class="action-btn action-success">
View
</a>

<a href="print.php?id=<?= $row['id'] ?>&month=<?= $month ?>&year=<?= $year ?>"
class="action-btn action-secondary"
target="_blank">
Print
</a>

</div>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>