<?php
session_start();
include "includes/config.php";

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $username = $_POST["username"];
    $password = $_POST["password"];

    $q = mysqli_query($conn,"SELECT * FROM users WHERE username='$username' AND password='$password'");

    if(mysqli_num_rows($q) > 0){

        $user = mysqli_fetch_assoc($q);

$_SESSION["user_id"] = $user["id"];
$_SESSION["role"] = $user["role"];
$_SESSION["fullname"] = $user["fullname"];

        header("Location: dashboard.php");
        exit;

    }else{
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>C'Flame Fire Protection Product Trading Login</title>

<link rel="stylesheet" href="/animalrescue_db/animalrescue/assets/css/main.css">
</head>

<body>

<!-- TOP BAR -->
<div class="topbar" style="text-align:center;">
CFLAME Fire Protection Equipment Management System
</div>


<!-- CENTER LOGIN -->
<div class="center-wrapper">

<div class="card" style="width:420px;">

<h2>System Login</h2>
<p class="sub">Please login to continue.</p>

<?php if($error): ?>
<div class="error-box">
<b><?= htmlspecialchars($error) ?></b>
</div>
<?php endif; ?>

<form method="POST">

<div class="field">
<label>Username</label>
<input type="text" name="username" required>
</div>

<div class="field">
<label>Password</label>
<input type="password" name="password" required>
</div>

<div class="actions">

<button class="btn-save" type="submit">
Login
</button>

</div>

</form>

</div>

</div>

</body>
</html>