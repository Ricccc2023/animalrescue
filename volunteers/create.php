<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Add Volunteer";
$active = "volunteers";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("
        INSERT INTO volunteers (name, contact, role)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("sss", $name, $contact, $role);
    $stmt->execute();

    header("Location: index.php");
    exit;
}

ob_start();
?>

<h2>Add Volunteer</h2>

<div class="card">

<form method="POST">

<div class="form-row">
<label>Name</label>
<input type="text" name="name" required>
</div>

<div class="form-row">
<label>Contact</label>
<input type="text" name="contact">
</div>

<div class="form-row">
<label>Role</label>
<input type="text" name="role" placeholder="e.g. Rescuer, Driver">
</div>

<button class="btn-save">Save</button>

</form>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";