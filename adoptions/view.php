<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$id = (int) $_GET['id'];

$result = $conn->query("
SELECT a.*, an.name, an.type, an.breed
FROM adoptions a
JOIN animals an ON an.id = a.animal_id
WHERE a.id=$id
");

$data = $result->fetch_assoc();

ob_start();
?>

<h2>Adoption Details</h2>

<div class="card">

<p><b>Name:</b> <?= $data['name'] ?></p>
<p><b>Type:</b> <?= $data['type'] ?></p>
<p><b>Breed:</b> <?= $data['breed'] ?></p>

<p><b>Adopter:</b> <?= $data['adopter_name'] ?></p>
<p><b>Contact:</b> <?= $data['contact'] ?></p>
<p><b>Address:</b> <?= $data['address'] ?></p>
<p><b>Date:</b> <?= $data['adoption_date'] ?></p>

<a href="index.php" class="btn-save btn-sm">← Back</a>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";