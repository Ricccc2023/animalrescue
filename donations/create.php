<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Add Donation";
$active = "donations";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['donor_name'];
    $amount = $_POST['amount'];
    $date = $_POST['donation_date'];
    $notes = $_POST['notes'];

    if ($amount <= 0) {
        die("Invalid amount.");
    }

    $stmt = $conn->prepare("
        INSERT INTO donations (donor_name, amount, donation_date, notes)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("sdss", $name, $amount, $date, $notes);
    $stmt->execute();

    header("Location: index.php");
    exit;
}

ob_start();
?>

<h2>Add Donation</h2>

<div class="card">

<form method="POST">

<div class="form-row">
<label>Donor Name</label>
<input type="text" name="donor_name" required>
</div>

<div class="form-row">
<label>Amount</label>
<input type="number" step="0.01" name="amount" required>
</div>

<div class="form-row">
<label>Date</label>
<input type="date" name="donation_date" required>
</div>

<div class="form-row">
<label>Notes</label>
<input type="text" name="notes">
</div>

<button class="btn-save">Save</button>

</form>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";