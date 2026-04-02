<?php
require_once __DIR__ . "/../includes/config.php";

$id = (int) $_GET['id'];

$data = $conn->query("SELECT * FROM rescues WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $conn->prepare("
        UPDATE rescues
        SET rescue_date=?, location=?, status=?, notes=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "ssssi",
        $_POST['rescue_date'],
        $_POST['location'],
        $_POST['status'],
        $_POST['notes'],
        $id
    );

    $stmt->execute();

    header("Location: index.php");
    exit;
}

ob_start();
?>

<h2>Edit Rescue</h2>

<div class="card">

<form method="POST">

<input type="date" name="rescue_date" value="<?= $data['rescue_date'] ?>">
<input type="text" name="location" value="<?= $data['location'] ?>">

<select name="status">
<option value="ongoing">Ongoing</option>
<option value="completed">Completed</option>
<option value="failed">Failed</option>
</select>

<input type="text" name="notes" value="<?= $data['notes'] ?>">

<button class="btn-save">Update</button>

</form>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";