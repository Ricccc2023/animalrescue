<?php
require_once __DIR__ . "/../includes/config.php";

$id = (int) $_GET['id'];

$conn->query("DELETE FROM adoptions WHERE id=$id");

header("Location: index.php");