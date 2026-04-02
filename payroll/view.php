<?php
require_once "../includes/config.php";
require_once "../includes/auth.php";

/* GET USER */
$id = intval($_GET['id']);

$user = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT *
FROM users
WHERE id=$id
"));

if(!$user){
exit("User not found");
}

/* DATE RANGE FILTER */
$from = $_GET['from'] ?? date("Y-m-01");
$to   = $_GET['to'] ?? date("Y-m-d");

/* ATTENDANCE DATA (DATE RANGE) */
$attendance = [];

$q = mysqli_query($conn,"
SELECT DATE(time) as day
FROM attendance
WHERE user_id=$id
AND type='IN'
AND DATE(time) BETWEEN '$from' AND '$to'
GROUP BY DATE(time)
");

while($row=mysqli_fetch_assoc($q)){
$attendance[$row['day']] = true;
}

/* DAYS WORKED */
$daysWorked = count($attendance);

/* SALARY */
$perDay = $user['per_day'];
$totalSalary = $daysWorked * $perDay;

/* CALENDAR BASE (GAMIT START DATE MONTH) */
$month = date("m", strtotime($from));
$year  = date("Y", strtotime($from));

$daysInMonth = cal_days_in_month(CAL_GREGORIAN,$month,$year);
$firstDay = date('w', strtotime("$year-$month-01"));
$monthName = date("F", mktime(0,0,0,$month,1));

/* PERIOD LABEL */
$periodLabel = date("M d, Y", strtotime($from)) . " - " . date("M d, Y", strtotime($to));

include "../includes/header.php";
include "../includes/sidebar.php";
?>

<div class="main">

<div class="page-header">

<div class="page-title">
<h2><?= htmlspecialchars($user['fullname']) ?> Payroll</h2>
</div>

<div class="page-action">
<a href="index.php" class="btn-add">Back</a>
</div>

</div>

<!-- FILTER -->
<div class="card">

<form method="GET" class="filter-bar">

<input type="hidden" name="id" value="<?= $id ?>">

<label>From:</label>
<input type="date" name="from" value="<?= $from ?>">

<label>To:</label>
<input type="date" name="to" value="<?= $to ?>">

<button class="btn-search">Filter</button>

</form>

</div>

<!-- SUMMARY -->
<div class="card" style="margin-bottom:20px;">

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">

<h3 style="margin:0;">Payroll Summary</h3>
</div>

<table>

<tr>
<td>Per Day Salary</td>
<td>₱<?= number_format($perDay,2) ?></td>
</tr>

<tr>
<td>Days Worked</td>
<td><?= $daysWorked ?></td>
</tr>

<tr>
<td><strong>Total Salary</strong></td>
<td>
<strong style="color:#198754;font-size:18px;">
₱<?= number_format($totalSalary,2) ?>
</strong>
</td>
</tr>

<tr>
<td>Period</td>
<td><?= $periodLabel ?></td>
</tr>

</table>

</div>

</div>


<!-- CALENDAR (IBINALIK) -->
<div style="width:350px;">

<div class="card">

<h3 style="margin-bottom:10px;">
Attendance — <?= $monthName ?> <?= $year ?>
</h3>

<table style="text-align:center; width:100%;">

<thead>
<tr>
<th>Sun</th>
<th>Mon</th>
<th>Tue</th>
<th>Wed</th>
<th>Thu</th>
<th>Fri</th>
<th>Sat</th>
</tr>
</thead>

<tbody>
<tr>

<?php

$counter = 0;

/* EMPTY CELLS */
for($i=0;$i<$firstDay;$i++){
echo "<td></td>";
$counter++;
}

/* DAYS LOOP */
for($d=1;$d<=$daysInMonth;$d++){

$dateCheck = "$year-$month-".str_pad($d,2,'0',STR_PAD_LEFT);

$bg="#ffffff";
$color="#000";

/* CONDITION:
- dapat present
- dapat pasok sa selected range
*/
if(
isset($attendance[$dateCheck]) &&
$dateCheck >= $from &&
$dateCheck <= $to
){
$bg="#198754";
$color="#fff";
}

echo "<td style='background:$bg;color:$color;'>$d</td>";

$counter++;

if($counter % 7 == 0){
echo "</tr><tr>";
}

}

?>

</tr>
</tbody>

</table>

</div>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>