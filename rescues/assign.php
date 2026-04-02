<?php
require_once __DIR__ . "/../includes/config.php";

$id = (int) $_GET['id'];

$volunteers = $conn->query("SELECT * FROM volunteers");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $conn->query("DELETE FROM rescue_volunteers WHERE rescue_id=$id");

    if (!empty($_POST['volunteers'])) {
        foreach($_POST['volunteers'] as $v){

            $stmt = $conn->prepare("
                INSERT INTO rescue_volunteers (rescue_id, volunteer_id)
                VALUES (?, ?)
            ");
            $stmt->bind_param("ii", $id, $v);
            $stmt->execute();
        }
    }

    header("Location: view.php?id=".$id);
    exit;
}

ob_start();
?>

<h2>Assign Volunteers</h2>

<div class="card">

<form method="POST">

<?php while($v = $volunteers->fetch_assoc()): ?>
<div>
<label>
<input type="checkbox" name="volunteers[]" value="<?= $v['id'] ?>">
<?= $v['name'] ?>
</label>
</div>
<?php endwhile; ?>

<button class="btn-save">Save</button>

</form>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";