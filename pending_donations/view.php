<?php
require_once __DIR__ . "/../includes/config.php";

$id = $_GET['id'];
$data = $conn->query("SELECT * FROM pending_donations WHERE id=$id")->fetch_assoc();
?>

<h2><?= htmlspecialchars($data['donor_name']) ?></h2>

<p>Amount: ₱ <?= number_format($data['amount'],2) ?></p>

<?php if($data['gcash_receipt']): ?>
    <img src="/animalrescue_db/uploads/gcash_receipts/<?= $data['gcash_receipt'] ?>" width="300">
<?php else: ?>
    <p>No receipt uploaded</p>
<?php endif; ?>