<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Records";
$active = "records";

$total_animals = $conn->query("SELECT COUNT(*) c FROM animals")->fetch_assoc()['c'];
$total_adopted = $conn->query("SELECT COUNT(*) c FROM animals WHERE status='adopted'")->fetch_assoc()['c'];
$total_donations = $conn->query("SELECT SUM(amount) t FROM donations")->fetch_assoc()['t'] ?? 0;

// determine page
$page = $_GET['page'] ?? '';

ob_start();
?>

<h2>Record Management</h2>

<div class="stats-grid">

<div class="card">
<b>Total Animals</b><br><?= $total_animals ?>
</div>

<div class="card">
<b>Adopted</b><br><?= $total_adopted ?>
</div>

<div class="card">
<b>Total Donations</b><br>₱ <?= number_format($total_donations,2) ?>
</div>

</div>

<div class="card">

<h3>Reports</h3>

<a href="?page=animals" class="btn-save">Animals</a>
<a href="?page=adoptions" class="btn-save">Adoption Requests</a>
<a href="?page=donations" class="btn-save">Donations</a>
<a href="?page=rescues" class="btn-save">Rescues</a>

</div>

<?php if($page == 'animals'): ?>

<div class="card">
<h3>Animal Records</h3>

<form method="GET" style="margin-bottom:10px;">
<input type="hidden" name="page" value="animals">
<select name="status">
<option value="">All</option>
<option value="available">Available</option>
<option value="rescued">Rescued</option>
<option value="adopted">Adopted</option>
</select>

<button class="btn-search">Filter</button>
</form>

<?php
$status = $_GET['status'] ?? '';
$query = "SELECT * FROM animals";

if ($status) {
    $query .= " WHERE status='$status'";
}

$result = $conn->query($query);
$data = $result->fetch_all(MYSQLI_ASSOC);
?>

<table>
<tr>
<th>Name</th>
<th>Type</th>
<th>Status</th>
</tr>

<?php foreach($data as $d): ?>
<tr>
<td><?= htmlspecialchars($d['name']) ?></td>
<td><?= ucfirst($d['type']) ?></td>
<td><?= ucfirst($d['status']) ?></td>
</tr>
<?php endforeach; ?>

</table>

</div>

<?php elseif($page == 'adoptions'): ?>

<div class="card">
<h3>Adoption Records</h3>

<?php
$result = $conn->query("
SELECT a.*, an.name
FROM adoptions a
JOIN animals an ON an.id = a.animal_id
");

$data = $result->fetch_all(MYSQLI_ASSOC);
?>

<table>
<tr>
<th>Animal</th>
<th>Adopter</th>
<th>Date</th>
</tr>

<?php foreach($data as $d): ?>
<tr>
<td><?= $d['name'] ?></td>
<td><?= $d['adopter_name'] ?></td>
<td><?= $d['adoption_date'] ?></td>
</tr>
<?php endforeach; ?>

</table>

</div>

<?php elseif($page == 'donations'): ?>

<div class="card">
<h3>Donation Records</h3>

<?php
$result = $conn->query("SELECT * FROM donations");
$data = $result->fetch_all(MYSQLI_ASSOC);

$total = $conn->query("SELECT SUM(amount) t FROM donations")->fetch_assoc()['t'];
?>

<b>Total: ₱ <?= number_format($total,2) ?></b>

<table>
<tr>
<th>Donor</th>
<th>Amount</th>
<th>Date</th>
</tr>

<?php foreach($data as $d): ?>
<tr>
<td><?= $d['donor_name'] ?></td>
<td>₱ <?= number_format($d['amount'],2) ?></td>
<td><?= $d['donation_date'] ?></td>
</tr>
<?php endforeach; ?>

</table>

</div>

<?php elseif($page == 'rescues'): ?>

<div class="card">
<h3>Rescue Records</h3>

<?php
$result = $conn->query("
SELECT r.*, a.name
FROM rescues r
JOIN animals a ON a.id = r.animal_id
");

$data = $result->fetch_all(MYSQLI_ASSOC);
?>

<table>
<tr>
<th>Animal</th>
<th>Date</th>
<th>Status</th>
</tr>

<?php foreach($data as $d): ?>
<tr>
<td><?= $d['name'] ?></td>
<td><?= $d['rescue_date'] ?></td>
<td><?= ucfirst($d['status']) ?></td>
</tr>
<?php endforeach; ?>

</table>

</div>

<?php endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";