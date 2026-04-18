<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Add Animal";
$active = "inventory";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name   = trim($_POST['name']);
    $type   = trim($_POST['type']);
    $breed  = trim($_POST['breed']);
    $gender = $_POST['gender'];
    $status = $_POST['status'];
    $notes  = trim($_POST['notes']);

    // IMAGE UPLOAD
    $imageName = null;

    if (!empty($_FILES['image']['name'])) {

        $uploadDir = __DIR__ . "/../uploads/animals/";
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $target = $uploadDir . $imageName;

        // create folder if not exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    // INSERT with image
    $stmt = $conn->prepare("
        INSERT INTO animals (name, type, breed, gender, status, notes, image)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("sssssss", $name, $type, $breed, $gender, $status, $notes, $imageName);
    $stmt->execute();

    header("Location: index.php");
    exit;
}

ob_start();
?>

<h2>Add Animal</h2>
<p style="color:#666;margin-bottom:20px;">Create new record</p>

<div class="card">

<form method="POST" enctype="multipart/form-data">

<div class="form-row">
<label>Name</label>
<input type="text" name="name" required>
</div>

<div class="form-row">
    <label>Type</label>
    <select name="type" required>
        <option value="" disabled selected>--Select--</option>
        <option value="Dog">Dog</option>
        <option value="Cat">Cat</option>
    </select>
</div>

<div class="form-row">
<label>Breed</label>
<input type="text" name="breed">
</div>

<div class="form-row">
<label>Gender</label>
<select name="gender" required>
<option value="male">Male</option>
<option value="female">Female</option>
</select>
</div>

<div class="form-row">
<label>Status</label>
<select name="status">
<option value="available">Available</option>
<option value="rescued">Rescued</option>
</select>
</div>

<div class="form-row">
<label>Image</label>
<input type="file" name="image" accept="image/*">
</div>

<div class="form-row">
<label>Notes</label>
<textarea name="notes" rows="3"></textarea>
</div>

<button class="btn-save">Save</button>

</form>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";