<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Add Pending Donation";
$active = "pending_donations";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $_POST['donor_name'];
    $amount = $_POST['amount'];
    $date = $_POST['donation_date'];

    $fileName = null;

    if (!empty($_FILES['gcash_receipt']['name'])) {

        $ext = strtolower(pathinfo($_FILES['gcash_receipt']['name'], PATHINFO_EXTENSION));

        $allowed = ['jpg','jpeg','png'];
        if (!in_array($ext, $allowed)) {
            die("Invalid file type");
        }

        $fileName = time() . "_" . rand(1000,9999) . "." . $ext;

        $uploadPath = __DIR__ . "/../uploads/gcash_receipts/" . $fileName;

        if (!move_uploaded_file($_FILES['gcash_receipt']['tmp_name'], $uploadPath)) {
            die("Upload failed");
        }
    }

    $stmt = $conn->prepare("INSERT INTO pending_donations 
        (donor_name, amount, gcash_receipt, donation_date)
        VALUES (?, ?, ?, ?)");

    $stmt->bind_param("sdss", $name, $amount, $fileName, $date);
    $stmt->execute();

    header("Location: index.php");
}
?>

<div class="page-header">
    <div class="page-title">
        <h2>Add Pending Donation</h2>
        <p class="sub">Enter donation details below</p>
    </div>
</div>

<div class="card">
    <div class="form-wrapper">

        <form method="POST" enctype="multipart/form-data">

            <div class="form-row">
                <label>Donor Name</label>
                <input type="text" name="donor_name" required>
            </div>

            <div class="form-row">
                <label>Amount</label>
                <input type="number" step="0.01" name="amount" required>
            </div>

            <div class="form-row">
                <label>GCash Receipt</label>
                <input type="file" name="gcash_receipt" accept="image/*">
            </div>

            <div class="form-row">
                <label>Date</label>
                <input type="date" name="donation_date" required>
            </div>

            <div class="form-row">
                <label></label>
                <div class="actions">
                    <button type="submit" class="btn-save">Save</button>
                    <a href="index.php" class="btn-decline">Cancel</a>
                </div>
            </div>

        </form>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";