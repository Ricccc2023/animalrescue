<?php
require_once __DIR__ . "/../includes/config.php";

$id = (int) $_GET['id'];

$conn->query("DELETE FROM volunteers WHERE id=$id");

header("Location: index.php");