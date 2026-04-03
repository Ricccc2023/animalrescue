<?php
require_once __DIR__ . "/../includes/config.php";

// get id
$id = intval($_GET['id']);

// =============================
// GET ANIMAL ID FROM ADOPTION
// =============================
$res = mysqli_query($conn, "SELECT animal_id FROM adoptions WHERE id = $id");

if(!$res || mysqli_num_rows($res) == 0){
    die("Invalid adoption ID");
}

$row = mysqli_fetch_assoc($res);
$animal_id = $row['animal_id'];

// =============================
// APPROVE ADOPTION
// =============================
mysqli_query($conn, "UPDATE adoptions SET status='Approved' WHERE id = $id");

// =============================
// SET ANIMAL AS ADOPTED ✅
// =============================
mysqli_query($conn, "UPDATE animals SET status='adopted' WHERE id = $animal_id");

// =============================
// REDIRECT
// =============================
header("Location: index.php");
exit;