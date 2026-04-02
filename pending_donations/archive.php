<?php
require_once __DIR__ . "/../includes/config.php";

$id = $_GET['id'];

$conn->query("DELETE FROM pending_donations WHERE id=$id");

header("Location: index.php");