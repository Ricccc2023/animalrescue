<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Rescue Operations";
$active = "rescues";

$result = $conn->query("
SELECT r.*, a.name 
FROM rescues r
JOIN animals a ON a.id = r.animal_id
ORDER BY r.id DESC
");

$rows = $result->fetch_all(MYSQLI_ASSOC);

ob_start();
?>

<h2>Rescue Operations</h2>

<a href="create.php" class="btn-add">+ New Rescue</a>

<div class="card">
<table>

<tr>
<th>Animal</th>
<th>Date</th>
<th>Location</th>
<th>Status</th>
<th>Actions</th>
</tr>

<?php foreach($rows as $r): ?>
<tr>
<td><?= $r['name'] ?></td>
<td><?= $r['rescue_date'] ?></td>
<td><?= $r['location'] ?></td>

<td>
<?php if($r['status']=='ongoing'): ?>
<span style="color:orange;">Ongoing</span>
<?php elseif($r['status']=='completed'): ?>
<span style="color:green;">Completed</span>
<?php else: ?>
<span style="color:red;">Failed</span>
<?php endif; ?>
</td>

<td>
<a href="view.php?id=<?= $r['id'] ?>" class="btn-save btn-sm">View</a>
<a href="edit.php?id=<?= $r['id'] ?>" class="btn-save btn-sm">Edit</a>
<a href="assign.php?id=<?= $r['id'] ?>" class="btn-save btn-sm">Assign</a>
</td>
</tr>
<?php endforeach; ?>

</table>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";