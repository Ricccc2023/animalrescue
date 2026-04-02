<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$id = (int) $_GET['id'];

$result = $conn->query("SELECT * FROM adoptions WHERE id=$id");
$data = $result->fetch_assoc();

if (!$data) {
    die("Adoption not found");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $conn->prepare("
        UPDATE adoptions
        SET adopter_name=?, contact=?, address=?, adoption_date=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "ssssi",
        $_POST['adopter_name'],
        $_POST['contact'],
        $_POST['address'],
        $_POST['adoption_date'],
        $id
    );

    $stmt->execute();

    header("Location: index.php");
    exit;
}

ob_start();
?>

<h2>Edit Adoption</h2>

<div class="card">

<form method="POST">

<div class="form-row">
<label>Name</label>
<input type="text" name="adopter_name" value="<?= $data['adopter_name'] ?>">
</div>

<div class="form-row">
<label>Contact</label>
<input type="text" name="contact" value="<?= $data['contact'] ?>">
</div>

<div class="form-row">
<label>Address</label>
<input type="text" name="address" value="<?= $data['address'] ?>">
</div>

<div class="form-row">
<label>Date</label>
<input type="date" name="adoption_date" value="<?= $data['adoption_date'] ?>">
</div>

<button class="btn-save">Update</button>

</form>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";