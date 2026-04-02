<?php
require_once __DIR__ . "/../includes/config.php";

$id = (int) $_GET['id'];

$rescue = $conn->query("
SELECT r.*, a.name
FROM rescues r
JOIN animals a ON a.id = r.animal_id
WHERE r.id=$id
")->fetch_assoc();

/* GET VOLUNTEERS */
$vols = $conn->query("
SELECT v.name
FROM rescue_volunteers rv
JOIN volunteers v ON v.id = rv.volunteer_id
WHERE rv.rescue_id = $id
");

ob_start();
?>

<h2>Rescue Details</h2>

<div class="card">

<p><b>Animal:</b> <?= $rescue['name'] ?></p>
<p><b>Date:</b> <?= $rescue['rescue_date'] ?></p>
<p><b>Location:</b> <?= $rescue['location'] ?></p>
<p><b>Status:</b> <?= $rescue['status'] ?></p>

<p><b>Volunteers:</b></p>
<ul>
<?php while($v = $vols->fetch_assoc()): ?>
<li><?= $v['name'] ?></li>
<?php endwhile; ?>
</ul>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";