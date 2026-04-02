<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Adoptions";
$active = "adoption";

$result = $conn->query("
SELECT a.*, an.name, an.type, an.breed
FROM adoptions a
JOIN animals an ON an.id = a.animal_id
ORDER BY a.id DESC
");

$rows = $result->fetch_all(MYSQLI_ASSOC);

ob_start();
?>

<h2>Adoption Records</h2>

<a href="create.php" class="btn-add">+ Add Adoption</a>

<div class="card">

<table>
<tr>
<th>Name</th>
<th>Type</th>
<th>Breed</th>
<th>Adopter</th>
<th>Date</th>
<th>Action</th>
</tr>

<?php foreach($rows as $r): ?>
<tr>
<td><?= $r['name'] ?></td>
<td><?= $r['type'] ?></td>
<td><?= $r['breed'] ?></td>
<td><?= $r['adopter_name'] ?></td>
<td><?= $r['adoption_date'] ?></td>

<td>
<a href="view.php?id=<?= $r['id'] ?>" class="btn-save btn-sm">View</a>
<a href="edit.php?id=<?= $r['id'] ?>" class="btn-save btn-sm">Edit</a>

</td>
</tr>
<?php endforeach; ?>

</table>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";