<?php
include "../includes/auth.php";
include "../includes/config.php";

$id = intval($_GET['id']);

mysqli_query($conn,"
INSERT INTO attendance (user_id,type)
VALUES ($id,'IN')
");

header("Location: index.php");
exit;