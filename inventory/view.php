<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "View Animal";
$active = "inventory";

// Get ID safely
$id = (int) ($_GET['id'] ?? 0);

// Fetch animal
$stmt = $conn->prepare("
    SELECT id, name, type, breed, gender, status, notes 
    FROM animals 
    WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$animal = $result->fetch_assoc();

ob_start();
?>

<h2>Animal Details</h2>
<p style="color:#666;margin-bottom:20px;">Full information</p>

<div class="card">

<?php if ($animal): ?>

<p><b>Name:</b> <?= htmlspecialchars($animal['name']) ?></p>

<p><b>Type:</b> <?= htmlspecialchars($animal['type']) ?></p>

<p><b>Breed:</b> <?= htmlspecialchars($animal['breed']) ?></p>

<!-- NEW GENDER DISPLAY -->
<p><b>Gender:</b>
<?php if ($animal['gender'] == 'male'): ?>
<span style="color:blue;">Male</span>
<?php else: ?>
<span style="color:pink;">Female</span>
<?php endif; ?>
</p>

<!-- STATUS -->
<p><b>Status:</b>
<?php if ($animal['status'] == 'available'): ?>
<span style="color:green;">Available</span>
<?php elseif ($animal['status'] == 'rescued'): ?>
<span style="color:orange;">Rescued</span>
<?php else: ?>
<span style="color:red;">Adopted</span>
<?php endif; ?>
</p>

<!-- NOTES -->
<p><b>Notes:</b><br>
<?= nl2br(htmlspecialchars($animal['notes'] ?? 'No notes available')) ?>
</p>

<?php else: ?>

<p style="color:red;">Animal not found.</p>

<?php endif; ?>

<a href="index.php" class="btn-save btn-sm" style="margin-top:10px;">
← Back
</a>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";