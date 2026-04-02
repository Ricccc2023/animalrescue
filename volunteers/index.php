<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Volunteers";
$active = "volunteers";

$result = $conn->query("SELECT * FROM volunteers ORDER BY id DESC");
$rows = $result->fetch_all(MYSQLI_ASSOC);

ob_start();
?>

<h2>Volunteers</h2>
<p style="color:#666;margin-bottom:15px;">Manage volunteer records</p>

<a href="create.php" class="btn-add" style="margin-bottom:10px;">+ Add Volunteer</a>

<div class="card">

<div class="table-wrap">
<table>

<tr>
<th>Name</th>
<th>Contact</th>
<th>Role</th>
<th>Actions</th>
</tr>

<?php foreach($rows as $r): ?>
<tr>
<td><b><?= htmlspecialchars($r['name']) ?></b></td>
<td><?= htmlspecialchars($r['contact']) ?></td>
<td><?= htmlspecialchars($r['role']) ?></td>

<td>
<div class="actions">
<a href="view.php?id=<?= $r['id'] ?>" class="btn-save btn-sm">View</a>
<a href="edit.php?id=<?= $r['id'] ?>" class="btn-save btn-sm">Edit</a>
<a href="archive.php?id=<?= $r['id'] ?>" class="btn-decline btn-sm"
onclick="return confirm('Delete this volunteer?')">Delete</a>
</div>
</td>
</tr>
<?php endforeach; ?>

<?php if(count($rows)==0): ?>
<tr><td colspan="4">No volunteers found.</td></tr>
<?php endif; ?>

</table>
</div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";