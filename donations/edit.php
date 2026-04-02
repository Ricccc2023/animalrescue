<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$id = (int) $_GET['id'];

$result = $conn->query("SELECT * FROM donations WHERE id=$id");
$data = $result->fetch_assoc();

if(!$data){
    die("Donation not found");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $conn->prepare("
        UPDATE donations
        SET donor_name=?, amount=?, donation_date=?, notes=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "sdssi",
        $_POST['donor_name'],
        $_POST['amount'],
        $_POST['donation_date'],
        $_POST['notes'],
        $id
    );

    $stmt->execute();

    header("Location: index.php");
    exit;
}

ob_start();
?>

<h2>Edit Donation</h2>

<div class="card">

<form method="POST">

<div class="form-row">
<label>Name</label>
<input type="text" name="donor_name" value="<?= $data['donor_name'] ?>">
</div>

<div class="form-row">
<label>Amount</label>
<input type="number" step="0.01" name="amount" value="<?= $data['amount'] ?>">
</div>

<div class="form-row">
<label>Date</label>
<input type="date" name="donation_date" value="<?= $data['donation_date'] ?>">
</div>

<div class="form-row">
<label>Notes</label>
<input type="text" name="notes" value="<?= $data['notes'] ?>">
</div>

<button class="btn-save">Update</button>

</form>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";