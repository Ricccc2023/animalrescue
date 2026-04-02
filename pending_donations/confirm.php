<?php
require_once __DIR__ . "/../includes/config.php";

$id = $_GET['id'] ?? 0;

$data = $conn->query("SELECT * FROM pending_donations WHERE id=$id")->fetch_assoc();

if(!$data){
    die("Record not found");
}

// FILE PATHS
$oldPath = __DIR__ . "/../uploads/gcash_receipts/" . $data['gcash_receipt'];
$newPath = __DIR__ . "/../confirmed_gcashreceipts/" . $data['gcash_receipt'];

// MOVE FILE
if(file_exists($oldPath)){
    rename($oldPath, $newPath);
}

// INSERT TO donations
$stmt = $conn->prepare("INSERT INTO donations 
    (donor_name, amount, donation_date, gcash_receipt)
    VALUES (?, ?, ?, ?)");

$stmt->bind_param("sdss",
    $data['donor_name'],
    $data['amount'],
    $data['donation_date'],
    $data['gcash_receipt']
);

$stmt->execute();

// DELETE FROM pending
$conn->query("DELETE FROM pending_donations WHERE id=$id");

header("Location: index.php");
exit;