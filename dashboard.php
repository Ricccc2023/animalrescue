<?php
require_once "includes/config.php";
require_once "includes/auth.php";

date_default_timezone_set("Asia/Manila");

$today = date("Y-m-d");

/*
|--------------------------------------------------------------------------
| TOTAL ANIMALS
|--------------------------------------------------------------------------
*/
$q = mysqli_query($conn,"SELECT COUNT(*) as total FROM animals");
$row = mysqli_fetch_assoc($q);
$total_animals = $row['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| AVAILABLE ANIMALS
|--------------------------------------------------------------------------
*/
$q = mysqli_query($conn,"SELECT COUNT(*) as total FROM animals WHERE status='available'");
$row = mysqli_fetch_assoc($q);
$available_animals = $row['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| ADOPTED ANIMALS
|--------------------------------------------------------------------------
*/
$q = mysqli_query($conn,"SELECT COUNT(*) as total FROM animals WHERE status='adopted'");
$row = mysqli_fetch_assoc($q);
$adopted_animals = $row['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| TOTAL DONATIONS
|--------------------------------------------------------------------------
*/
$q = mysqli_query($conn,"
SELECT SUM(amount) as total 
FROM donations
");
$row = mysqli_fetch_assoc($q);
$total_donations = $row['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| RESCUES TODAY
|--------------------------------------------------------------------------
*/
$q = mysqli_query($conn,"
SELECT COUNT(*) as total 
FROM rescues 
WHERE rescue_date = '$today'
");
$row = mysqli_fetch_assoc($q);
$rescues_today = $row['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| LOW SUPPLIES
|--------------------------------------------------------------------------
*/
$low_supplies = [];

$q = mysqli_query($conn,"
SELECT item_name, quantity 
FROM supplies 
WHERE quantity <= 5
ORDER BY quantity ASC
LIMIT 5
");

while($row = mysqli_fetch_assoc($q)){
    $low_supplies[] = $row;
}


/*
|--------------------------------------------------------------------------
| DONATION TREND (LAST 7 DAYS)
|--------------------------------------------------------------------------
*/
$donation_labels = [];
$donation_data = [];

for($i=6;$i>=0;$i--){
    $date = date("Y-m-d",strtotime("-$i days"));

    $q = mysqli_query($conn,"
    SELECT SUM(amount) as total 
    FROM donations 
    WHERE donation_date='$date'
    ");

    $r = mysqli_fetch_assoc($q);

    $donation_labels[] = date("M d",strtotime($date));
    $donation_data[] = $r['total'] ?? 0;
}
?>

<?php include "includes/header.php"; ?>
<?php include "includes/sidebar.php"; ?>

<div class="main">

<div class="page-header">

</div>

<div class="dashboard-grid">

<!-- TOTAL ANIMALS -->
<div class="card stat-card">
<h3>Total Animals</h3>
<div class="stat-value"><?= $total_animals ?></div>
</div>

<!-- AVAILABLE -->
<div class="card stat-card">
<h3>Available Animals</h3>
<div class="stat-value"><?= $available_animals ?></div>
</div>

<!-- ADOPTED -->
<div class="card stat-card">
<h3>Adopted Animals</h3>
<div class="stat-value"><?= $adopted_animals ?></div>
</div>

<!-- DONATIONS -->
<div class="card stat-card">
<h3>Total Donations</h3>
<div class="stat-value">
₱<?= number_format($total_donations,2) ?>
</div>
</div>

<!-- RESCUES TODAY -->
<div class="card stat-card">
<h3>Rescues Today</h3>
<div class="stat-value"><?= $rescues_today ?></div>
</div>

<!-- LOW SUPPLIES -->
<div class="card stat-card">
<h3>Low Supplies</h3>

<div style="text-align:left; margin-top:10px;">

<?php if(count($low_supplies) > 0): ?>
<?php foreach($low_supplies as $item): ?>

<div style="margin-bottom:8px;">
<strong><?= $item['item_name'] ?></strong><br>
<small style="color:red;">Qty: <?= $item['quantity'] ?></small>
</div>

<?php endforeach; ?>
<?php else: ?>
<div>No low supplies</div>
<?php endif; ?>

</div>
</div>

<!-- DONATION TREND -->
<div class="card">
<h3>Donation Trend (7 Days)</h3>
<canvas id="donationChart"></canvas>
</div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById('donationChart'),{
type:'line',
data:{
labels: <?= json_encode($donation_labels) ?>,
datasets:[{
data: <?= json_encode($donation_data) ?>,
borderColor:'#409fcf',
backgroundColor:'rgba(40,167,69,0.1)',
fill:true
}]
},
options:{plugins:{legend:{display:false}}}
});
</script>

<style>

.dashboard-grid{
display:grid;
grid-template-columns: repeat(3, 1fr);
gap:20px;
}

.card{
padding:10px;
background:#fff;
border:1px solid #e5e5e5;
border-radius:6px;
}

.stat-card{
text-align:center;
}

.stat-value{
font-size:28px;
font-weight:bold;
color:#409fcf;
}

.dashboard-grid .card:nth-child(7){
grid-column: span 3;
}

canvas{
width:100% !important;
height:320px !important;
}

@media(max-width:1200px){
.dashboard-grid{
grid-template-columns:1fr;
}
}

</style>

<?php include "includes/footer.php"; ?>