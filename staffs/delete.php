<?php
include "../includes/config.php";

$id = $_GET['id'];

/* GET USER DATA */
$get = mysqli_query($conn,"SELECT * FROM users WHERE id='$id'");
$user = mysqli_fetch_assoc($get);

/* INSERT INTO ARCHIVE */
mysqli_query($conn,"INSERT INTO archive_users
(id,fullname,username,password,role,availability)
VALUES
('{$user['id']}','{$user['fullname']}','{$user['username']}','{$user['password']}','{$user['role']}','{$user['availability']}')");

/* DELETE FROM USERS */
mysqli_query($conn,"DELETE FROM users WHERE id='$id'");

header("Location: index.php");
exit;
?>