<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$id = (int) $_GET['id'];

$result = $conn->query("SELECT * FROM volunteers WHERE id=$id");
$data = $result->fetch_assoc();

if(!$data){
    die("Volunteer not found");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $conn->prepare("
        UPDATE volunteers
        SET name=?, contact=?, role=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "sssi",
        $_POST['name'],
        $_POST['contact'],
        $_POST['role'],
        $id
    );

    $stmt->execute();

    header("Location: index.php");
    exit;
}

ob_start();
?>

<h2>Edit Volunteer</h2>

<div class="card">

<form method="POST">

<div class="form-row">
<label>Name</label>
<input type="text" name="name" value="<?= $data['name'] ?>">
</div>

<div class="form-row">
<label>Contact</label>
<input type="text" name="contact" value="<?= $data['contact'] ?>">
</div>

<div class="form-row">
<label>Role</label>
<input type="text" name="role" value="<?= $data['role'] ?>">
</div>

<button class="btn-save">Update</button>

</form>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";