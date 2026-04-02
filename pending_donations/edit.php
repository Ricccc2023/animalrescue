<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$id = $_GET['id'];
$data = $conn->query("SELECT * FROM pending_donations WHERE id=$id")->fetch_assoc();

$title = "Edit Pending Donation";
$active = "pending_donations";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $_POST['donor_name'];
    $amount = $_POST['amount'];
    $date = $_POST['donation_date'];

    $fileName = $data['gcash_receipt'];

    if (!empty($_FILES['gcash_receipt']['name'])) {
        $ext = pathinfo($_FILES['gcash_receipt']['name'], PATHINFO_EXTENSION);
        $fileName = time() . "." . $ext;

        move_uploaded_file(
            $_FILES['gcash_receipt']['tmp_name'],
            __DIR__ . "/../uploads/gcash_receipts/" . $fileName
        );
    }

    $stmt = $conn->prepare("UPDATE pending_donations 
        SET donor_name=?, amount=?, gcash_receipt=?, donation_date=? 
        WHERE id=?");

    $stmt->bind_param("sdssi", $name, $amount, $fileName, $date, $id);
    $stmt->execute();

    header("Location: index.php");
}

ob_start();
?>

<div class="page-header">
    <div class="page-title">
        <h2>Edit Pending Donation</h2>
        <p class="sub">Update donation details</p>
    </div>
</div>

<div class="card">
    <div class="form-wrapper">

        <form method="POST" enctype="multipart/form-data">

            <div class="form-row">
                <label>Donor Name</label>
                <input type="text" name="donor_name" value="<?= htmlspecialchars($data['donor_name']) ?>" required>
            </div>

            <div class="form-row">
                <label>Amount</label>
                <input type="number" step="0.01" name="amount" value="<?= $data['amount'] ?>" required>
            </div>

            <div class="form-row">
                <label>Replace Receipt</label>
                <input type="file" name="gcash_receipt" accept="image/*">
            </div>

            <?php if($data['gcash_receipt']): ?>
            <div class="form-row">
                <label>Current Receipt</label>
                <div>
                    <img src="../uploads/gcash_receipts/<?= $data['gcash_receipt'] ?>" width="120">
                </div>
            </div>
            <?php endif; ?>

            <div class="form-row">
                <label>Date</label>
                <input type="date" name="donation_date" value="<?= $data['donation_date'] ?>" required>
            </div>

            <div class="form-row">
                <label></label>
                <div class="actions">
                    <button type="submit" class="btn-save">Update</button>
                    <a href="index.php" class="btn-decline">Cancel</a>
                </div>
            </div>

        </form>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";