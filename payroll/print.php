<?php
require_once "../includes/config.php";

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

/* ATTENDANCE (DATE RANGE FIXED) */
$q = mysqli_query($conn,"
SELECT DATE(time) as day
FROM attendance
WHERE user_id=$id
AND type='IN'
AND DATE(time) BETWEEN '$from' AND '$to'
GROUP BY DATE(time)
");

$daysWorked = mysqli_num_rows($q);

/* PAYROLL */
$perDay = $user['per_day'];
$grossPay = $perDay * $daysWorked;

/* OPTIONAL DEDUCTION */
$deduction = $_GET['deduction'] ?? 0;

$netPay = $grossPay - $deduction;

/* PERIOD LABEL (FIXED) */
$periodLabel = date("M d, Y", strtotime($from)) . " - " . date("M d, Y", strtotime($to));
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Payslip</title>

<style>

body{
font-family:Arial, sans-serif;
background:#fff;
}

.wrapper{
width:520px;
margin:20px auto;
border:1px solid #000;
padding:20px;
}

.header{
display:flex;
align-items:center;
border-bottom:1px solid #000;
padding-bottom:10px;
margin-bottom:15px;
}

.logo{
width:60px;
height:60px;
margin-right:15px;
}

.logo img{
width:100%;
height:100%;
object-fit:contain;
}

.company-info{
font-size:13px;
}

.company-info h2{
margin:0;
font-size:16px;
}

.payslip-title{
text-align:center;
font-weight:bold;
margin:15px 0;
font-size:14px;
}

.section{
margin-bottom:15px;
}

.section-title{
font-weight:bold;
border-bottom:1px solid #000;
font-size:12px;
padding-bottom:4px;
margin-bottom:6px;
}

table{
width:100%;
border-collapse:collapse;
font-size:12px;
}

td{
padding:4px 0;
}

.right{
text-align:right;
}

.net{
font-size:14px;
font-weight:bold;
border-top:2px solid #000;
padding-top:6px;
}

.footer{
margin-top:30px;
font-size:12px;
}

.signature{
margin-top:40px;
}

.print-btn{
text-align:center;
margin-bottom:15px;
}

.print-green{
background:#198754;
color:#fff;
border:none;
padding:8px 16px;
cursor:pointer;
font-size:13px;
font-weight:600;
}

.print-green:hover{
background:#157347;
}

@media print{
.print-btn{
display:none;
}
body{
margin:0;
}
}

</style>

</head>

<body>

<div class="print-btn">
<button onclick="window.print()" class="print-green">
🖨 Print Payslip
</button>
</div>

<div class="wrapper">

<div class="header">

<div class="logo">
<img src="logo.png" alt="Company Logo">
</div>

<div class="company-info">

<h2>Strays Worth Saving Management System</h2>

<div>Santo Tomas, Philippines, 4234</div>

<div>Contact: <strong>09854002367</strong></div>

</div>

</div>

<div class="payslip-title">
PAYSLIP
</div>

<div class="section">

<table>

<tr>
<td><b>Employee:</b></td>
<td><strong><?= htmlspecialchars($user['fullname']) ?></strong></td>
</tr>

<tr>
<td><b>Period:</b></td>
<td><?= $periodLabel ?></td>
</tr>

<tr>
<td><b>Date Generated:</b></td>
<td><?= date("M d, Y") ?></td>
</tr>

</table>

</div>

<div class="section">

<div class="section-title">
EARNINGS
</div>

<table>

<tr>
<td>Per Day Salary</td>
<td class="right">₱<?= number_format($perDay,2) ?></td>
</tr>

<tr>
<td>Days Worked</td>
<td class="right"><?= $daysWorked ?></td>
</tr>

<tr>
<td>Gross Pay</td>
<td class="right">₱<?= number_format($grossPay,2) ?></td>
</tr>

</table>

</div>

<div class="section">

<div class="section-title">
DEDUCTIONS
</div>

<table>

<tr>
<td>Manual Deduction</td>
<td class="right">₱<?= number_format($deduction,2) ?></td>
</tr>

</table>

</div>

<div class="section">

<table>

<tr class="net">
<td>NET PAY</td>
<td class="right">₱<?= number_format($netPay,2) ?></td>
</tr>

</table>

</div>

<div class="footer">

<div class="signature">
___________________________ <br>
Employee Signature
</div>

</div>

</div>

</body>
</html>