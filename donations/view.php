<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$id = (int) $_GET['id'];

$result = $conn->query("SELECT * FROM donations WHERE id=$id");
$data = $result->fetch_assoc();

if(!$data){
    die("Donation not found");
}

ob_start();
?>

<h2>Donation Details</h2>

<div class="card">

<p><b>Donor:</b> <?= $data['donor_name'] ?></p>
<p><b>Amount:</b> ₱ <?= number_format($data['amount'],2) ?></p>
<p><b>Date:</b> <?= $data['donation_date'] ?></p>
<p><b>Notes:</b> <?= $data['notes'] ?></p>

<a href="index.php" class="btn-save btn-sm">← Back</a>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";