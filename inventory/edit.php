<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Edit Animal";
$active = "inventory";

// Get ID
$id = (int) ($_GET['id'] ?? 0);

// Fetch existing data
$stmt = $conn->prepare("
    SELECT id, name, type, breed, gender, status, notes 
    FROM animals 
    WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$animal = $result->fetch_assoc();

// If not found
if (!$animal) {
    die("Animal not found.");
}

// Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name   = trim($_POST['name']);
    $type   = trim($_POST['type']);
    $breed  = trim($_POST['breed']);
    $gender = $_POST['gender']; // NEW
    $status = $_POST['status'];
    $notes  = trim($_POST['notes']);

    $stmt = $conn->prepare("
        UPDATE animals
        SET name=?, type=?, breed=?, gender=?, status=?, notes=?
        WHERE id=?
    ");

    $stmt->bind_param("ssssssi", $name, $type, $breed, $gender, $status, $notes, $id);
    $stmt->execute();

    header("Location: index.php");
    exit;
}

ob_start();
?>

<h2>Edit Animal</h2>
<p style="color:#666;margin-bottom:20px;">Update record</p>

<div class="card">

<form method="POST">

<div class="form-row">
<label>Name</label>
<input type="text" name="name"
value="<?= htmlspecialchars($animal['name']) ?>" required>
</div>

<div class="form-row">
<label>Type</label>
<input type="text" name="type"
value="<?= htmlspecialchars($animal['type']) ?>" required>
</div>

<div class="form-row">
<label>Breed</label>
<input type="text" name="breed"
value="<?= htmlspecialchars($animal['breed']) ?>">
</div>

<!-- NEW GENDER FIELD -->
<div class="form-row">
<label>Gender</label>
<select name="gender" required>
<option value="male" <?= $animal['gender']=='male' ? 'selected' : '' ?>>Male</option>
<option value="female" <?= $animal['gender']=='female' ? 'selected' : '' ?>>Female</option>
</select>
</div>

<div class="form-row">
<label>Status</label>
<select name="status">
<option value="available" <?= $animal['status']=='available'?'selected':'' ?>>Available</option>
<option value="rescued" <?= $animal['status']=='rescued'?'selected':'' ?>>Rescued</option>
<option value="adopted" <?= $animal['status']=='adopted'?'selected':'' ?>>Adopted</option>
</select>
</div>

<div class="form-row">
<label>Notes</label>
<textarea name="notes" rows="3"><?= htmlspecialchars($animal['notes']) ?></textarea>
</div>

<button class="btn-save">Update</button>

</form>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";