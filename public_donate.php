<?php
require_once __DIR__ . '/includes/config.php';

$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $name  = trim($_POST['donor_name'] ?? '');
    $amount = (float)($_POST['amount'] ?? 0);
    $date = date("Y-m-d");

    if(!$name || !$amount){
        $errors[] = "Please fill all required fields.";
    }

    if(empty($_FILES['gcash_receipt']['name'])){
        $errors[] = "GCash receipt is required.";
    }

    $fileName = null;

    if(!$errors){

        $ext = strtolower(pathinfo($_FILES['gcash_receipt']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png'];

        if(!in_array($ext, $allowed)){
            $errors[] = "Invalid file type. Only JPG, JPEG, PNG allowed.";
        } else {

            // UNIQUE FILE NAME
            $fileName = time() . "_" . rand(1000,9999) . "." . $ext;

            $uploadDir = __DIR__ . "/uploads/gcash_receipts/";

            // MAKE SURE FOLDER EXISTS
            if(!is_dir($uploadDir)){
                mkdir($uploadDir, 0777, true);
            }

            $uploadPath = $uploadDir . $fileName;

            if(!move_uploaded_file($_FILES['gcash_receipt']['tmp_name'], $uploadPath)){
                $errors[] = "Upload failed.";
            }
        }
    }

    if(!$errors){

        $stmt = $conn->prepare("INSERT INTO pending_donations 
            (donor_name, amount, gcash_receipt, donation_date)
            VALUES (?, ?, ?, ?)");

        $stmt->bind_param("sdss", $name, $amount, $fileName, $date);
        $stmt->execute();

        // ✅ REDIRECT TO THANK YOU PAGE
        header("Location: thankyoudonate.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Donate - Strays Worth Saving</title>

<link rel="stylesheet" href="/animalrescue_db/animalrescue/assets/css/main.css">

<style>
.public-wrapper {
    display: flex;
    justify-content: center;
    gap: 20px;
    padding: 40px;
    flex-wrap: wrap;
}

.public-card {
    width: 100%;
    max-width: 550px;
}

.side-card {
    width: 300px;
}
</style>

</head>

<body>

<div class="topbar">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <div>Strays Worth Saving Management System</div>
        <a href="index.php" class="admin-btn">Back to Page</a>
    </div>
</div>

<div class="public-wrapper">

    <!-- FORM -->
    <div class="card public-card">

        <div class="form-header">
            <h2>GCash Donation</h2>
            <p>Submit your donation and upload your receipt</p>
        </div>

        <?php if($errors): ?>
            <div class="error-box">
                <b>Error:</b><br>
                <?= implode("<br>", $errors) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="form-wrapper">

                <div class="form-row">
                    <label>Full Name *</label>
                    <input type="text" name="donor_name" required>
                </div>

                <div class="form-row">
                    <label>Amount *</label>
                    <input type="number" step="0.01" name="amount" required>
                </div>

                <div class="form-row">
                    <label>GCash Receipt *</label>
                    <input type="file" name="gcash_receipt" accept="image/*" required>
                </div>

                <div class="form-row">
                    <label></label>
                    <div class="actions">
                        <button type="submit" class="btn-save">Submit Donation</button>
                    </div>
                </div>

            </div>

        </form>
    </div>

    <!-- SIDE INFO -->
    <div class="card side-card">

        <h3>Instructions</h3>

        <ul style="font-size:13px; padding-left:18px;">
            <li>Send donation via GCash.</li>
            <li>Take screenshot of receipt.</li>
            <li>Upload the receipt here.</li>
            <li>Wait for admin approval.</li>
        </ul>

        <hr style="margin:10px 0;">

        <h4>Important</h4>

        <ul style="font-size:13px; padding-left:18px;">
            <li>Make sure amount is correct.</li>
            <li>Blurry images may be rejected.</li>
            <li>Fake receipts will be declined.</li>
        </ul>

    </div>

</div>

</body>
</html>