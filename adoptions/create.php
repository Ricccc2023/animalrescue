<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Add Adoption";
$active = "adoption";

/* GET UNIQUE TYPES */
$types = $conn->query("SELECT DISTINCT type FROM animals WHERE status='available'");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $type = $_POST['type'];
    $name = $_POST['adopter_name'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $date = $_POST['adoption_date'];

    /* GET FIRST AVAILABLE ANIMAL OF THAT TYPE */
    $stmt = $conn->prepare("SELECT * FROM animals WHERE type=? AND status='available' LIMIT 1");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $animal = $stmt->get_result()->fetch_assoc();

    if (!$animal) {
        die("No available animal for this type.");
    }

    $animal_id = $animal['id'];

    /* INSERT ADOPTION */
    $stmt = $conn->prepare("
        INSERT INTO adoptions (animal_id, adopter_name, contact, address, adoption_date)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issss", $animal_id, $name, $contact, $address, $date);
    $stmt->execute();

    /* UPDATE STATUS */
    $stmt = $conn->prepare("UPDATE animals SET status='adopted' WHERE id=?");
    $stmt->bind_param("i", $animal_id);
    $stmt->execute();

    header("Location: index.php");
    exit;
}

ob_start();
?>

<h2>Add Adoption</h2>

<div class="card">

<form method="POST">

<div class="form-row">
<label>Select Animal Type</label>
<select name="type" required>

<?php while($t = $types->fetch_assoc()): ?>
<option value="<?= $t['type'] ?>">
<?= ucfirst($t['type']) ?>
</option>
<?php endwhile; ?>

</select>
</div>

<div class="form-row">
<label>Adopter Name</label>
<input type="text" name="adopter_name" required>
</div>

<div class="form-row">
<label>Contact</label>
<input type="text" name="contact">
</div>

<div class="form-row">
<label>Address</label>
<input type="text" name="address">
</div>

<div class="form-row">
<label>Date</label>
<input type="date" name="adoption_date" required>
</div>

<button class="btn-save">Save</button>

</form>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";