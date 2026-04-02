<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Inventory";
$active = "inventory";

// Search
$q = $_GET['q'] ?? '';
$search = "%" . $q . "%";

// Query with gender included
$stmt = $conn->prepare("
    SELECT id, name, type, breed, gender, status 
    FROM animals
    WHERE name LIKE ?
    ORDER BY id DESC
");

$stmt->bind_param("s", $search);
$stmt->execute();

$result = $stmt->get_result();
$animals = $result->fetch_all(MYSQLI_ASSOC);

ob_start();
?>

<h2>Animal Inventory</h2>
<p style="color:#666;margin-bottom:20px;">
Manage rescued and adoptable animals
</p>

<div class="card" style="margin-bottom:20px;">
<form method="get" style="display:flex; gap:10px; flex-wrap:wrap;">

<input type="text" name="q"
value="<?= htmlspecialchars($q) ?>"
placeholder="Search animal name..."
style="max-width:300px;">

<button class="btn-add">Search</button>

<a href="create.php" class="btn-add">+</a>

</form>
</div>

<div class="card">

<h3>Animal List</h3>
<p style="color:#666;margin-bottom:10px;">
Total: <?= count($animals) ?>
</p>

<div class="table-wrap">

<table>
<tr>
<th>Name</th>
<th>Type</th>
<th>Breed</th>
<th>Gender</th>
<th>Status</th>
<th>Actions</th>
</tr>

<?php foreach ($animals as $a): ?>
<tr>

<td><b><?= htmlspecialchars($a['name']) ?></b></td>
<td><?= htmlspecialchars($a['type']) ?></td>
<td><?= htmlspecialchars($a['breed']) ?></td>

<!-- NEW GENDER COLUMN -->
<td>
<?php if ($a['gender'] == 'male'): ?>
<span style="color:blue;">Male</span>
<?php else: ?>
<span style="color:pink;">Female</span>
<?php endif; ?>
</td>

<!-- STATUS -->
<td>
<?php if ($a['status'] == 'available'): ?>
<span style="color:green;">Available</span>
<?php elseif ($a['status'] == 'rescued'): ?>
<span style="color:orange;">Rescued</span>
<?php else: ?>
<span style="color:red;">Adopted</span>
<?php endif; ?>
</td>

<td>
<div class="actions">
<a href="view.php?id=<?= $a['id'] ?>" class="btn-save btn-sm">View</a>
<a href="edit.php?id=<?= $a['id'] ?>" class="btn-save btn-sm">Edit</a>
</div>
</td>

</tr>
<?php endforeach; ?>

</table>

</div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";