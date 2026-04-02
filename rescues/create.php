<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$animals = $conn->query("SELECT * FROM animals");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $conn->prepare("
        INSERT INTO rescues (animal_id, rescue_date, location, status, notes)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issss",
        $_POST['animal_id'],
        $_POST['rescue_date'],
        $_POST['location'],
        $_POST['status'],
        $_POST['notes']
    );

    $stmt->execute();

    header("Location: index.php");
    exit;
}

ob_start();
?>

<h2>Create Rescue</h2>

<div class="card">

<form method="POST">

<div class="form-row">
<label>Animal</label>
<select name="animal_id">
<?php while($a = $animals->fetch_assoc()): ?>
<option value="<?= $a['id'] ?>">
<?= $a['name'] ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="form-row">
<label>Date</label>
<input type="date" name="rescue_date" required>
</div>

<div class="form-row">
<label>Location</label>
<input type="text" name="location">
</div>

<div class="form-row">
<label>Status</label>
<select name="status">
<option value="ongoing">Ongoing</option>
<option value="completed">Completed</option>
<option value="failed">Failed</option>
</select>
</div>

<div class="form-row">
<label>Notes</label>
<input type="text" name="notes">
</div>

<button class="btn-save">Save</button>

</form>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";