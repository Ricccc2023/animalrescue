<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$id = (int) $_GET['id'];

$result = $conn->query("SELECT * FROM volunteers WHERE id=$id");
$data = $result->fetch_assoc();

if(!$data){
    die("Volunteer not found");
}

ob_start();
?>

<h2>Volunteer Details</h2>

<div class="card">

<p><b>Name:</b> <?= $data['name'] ?></p>
<p><b>Contact:</b> <?= $data['contact'] ?></p>
<p><b>Role:</b> <?= $data['role'] ?></p>

<a href="index.php" class="btn-save btn-sm">← Back</a>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";