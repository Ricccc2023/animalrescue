<?php
include "../includes/auth.php";
include "../includes/config.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$id = intval($_GET['id']);

/* USER INFO */

$userQuery = mysqli_query($conn,"SELECT * FROM users WHERE id=$id");

if(!$userQuery){
die(mysqli_error($conn));
}

$user = mysqli_fetch_assoc($userQuery);


/* CALENDAR SETTINGS */

$month = date('m');
$year  = date('Y');

$monthName = date('F');

$daysInMonth = cal_days_in_month(CAL_GREGORIAN,$month,$year);
$firstDay = date('w', strtotime("$year-$month-01"));


/* GET ATTENDANCE DAYS */

$attQuery = mysqli_query($conn,"
SELECT DATE(time) as att_date
FROM attendance
WHERE user_id = $id
AND MONTH(time) = $month
AND YEAR(time) = $year
");

$attendance = [];

if($attQuery){

while($r = mysqli_fetch_assoc($attQuery)){
$attendance[$r['att_date']] = true;
}

}


/* RECENT LOGS */

$logs = mysqli_query($conn,"
SELECT *
FROM attendance
WHERE user_id=$id
ORDER BY time DESC
LIMIT 20
");
?>

<div class="main">

<div class="page-header">

<div class="page-title">
<h2><?= htmlspecialchars($user['fullname']) ?></h2>
</div>

<div class="page-action">
<a href="index.php" class="btn-decline">Back</a>
</div>

</div>


<div style="display:flex;gap:20px;align-items:flex-start;">


<div style="flex:1;">


<div class="card">

<h3>User Information</h3>

<table class="result-table">

<tr>
<th>ID</th>
<td><?= $user['id'] ?></td>
</tr>

<tr>
<th>Full Name</th>
<td><?= htmlspecialchars($user['fullname']) ?></td>
</tr>

<tr>
<th>Username</th>
<td><?= htmlspecialchars($user['username']) ?></td>
</tr>

<tr>
<th>Role</th>
<td><?= $user['role'] ?></td>
</tr>

</table>

</div>


<div class="card">

<h3>Recent Attendance Logs</h3>

<table class="result-table">

<tr>
<th>Date</th>
<th>Time</th>
<th>Status</th>
</tr>

<?php if(!$logs || mysqli_num_rows($logs)==0){ ?>

<tr>
<td colspan="3">No attendance records yet</td>
</tr>

<?php } else { ?>

<?php while($a = mysqli_fetch_assoc($logs)){ ?>

<tr>

<td><?= date("M d, Y", strtotime($a['time'])) ?></td>

<td><?= date("h:i A", strtotime($a['time'])) ?></td>

<td>

<?php if($a['type']=="IN"){ ?>

<span style="color:#198754;font-weight:bold;">IN</span>

<?php } else { ?>

<span style="color:#dc3545;font-weight:bold;">OUT</span>

<?php } ?>

</td>

</tr>

<?php } ?>

<?php } ?>

</table>

</div>

</div>


<div style="width:350px;">

<div class="card">

<h3 style="margin-bottom:10px;">
Attendance — <?= $monthName ?> <?= $year ?>
</h3>

<table style="text-align:center;width:100%;">

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

$counter=0;

for($i=0;$i<$firstDay;$i++){
echo "<td></td>";
$counter++;
}

for($d=1;$d<=$daysInMonth;$d++){

$dateCheck="$year-$month-".str_pad($d,2,'0',STR_PAD_LEFT);

$color="#ffffff";
$text="#000";

if(isset($attendance[$dateCheck])){
$color="#198754";
$text="#fff";
}

echo "<td style='background:$color;color:$text;font-weight:600;'>$d</td>";

$counter++;

if($counter%7==0){
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