<?php
require_once __DIR__ . "/../includes/config.php";

// get id
$id = intval($_GET['id']);

// =============================
// GET FULL ADOPTION DATA
// =============================
$res = mysqli_query($conn, "SELECT * FROM adoptions WHERE id = $id");

if(!$res || mysqli_num_rows($res) == 0){
    die("Invalid adoption ID");
}

$data = mysqli_fetch_assoc($res);

$animal_id = $data['animal_id'];
$name = mysqli_real_escape_string($conn, $data['adopter_name']);
$contact = mysqli_real_escape_string($conn, $data['contact']);
$address = mysqli_real_escape_string($conn, $data['address']);

// =============================
// INSERT INTO REJECTED LOG TABLE
// =============================
mysqli_query($conn, "
    INSERT INTO adoption_rejected_logs 
    (adoption_id, animal_id, adopter_name, contact, address)
    VALUES 
    ('$id', '$animal_id', '$name', '$contact', '$address')
");

// =============================
// UPDATE STATUS → REJECTED
// =============================
mysqli_query($conn, "UPDATE adoptions SET status='Rejected' WHERE id = $id");

// =============================
// IBALIK SA AVAILABLE ANG ANIMAL
// =============================
mysqli_query($conn, "UPDATE animals SET status='available' WHERE id = $animal_id");

// =============================
header("Location: index.php");
exit;