<?php
include "../includes/auth.php";
include "../includes/config.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$id = intval($_GET['id']);

$data = mysqli_query($conn,"SELECT * FROM users WHERE id=$id");
$row = mysqli_fetch_assoc($data);

if(isset($_POST['update'])){

$fullname = $_POST['fullname'];
$username = $_POST['username'];
$password = $_POST['password'];
$role = $_POST['role'];

mysqli_query($conn,"UPDATE users SET
fullname='$fullname',
username='$username',
password='$password',
role='$role'
WHERE id=$id");

header("Location:index.php");
exit;
}
?>

<div class="main">

<div class="page-header">

<div class="page-title">
<h2>Edit User

<div class="page-action">
<a href="index.php" class="btn-decline">Back</a>
</div>
</h2>



</div>

</div>

<div class="card">

<form method="POST">

<div class="form-wrapper">

<div class="form-row">
<label>Full Name</label>
<input type="text" name="fullname" value="<?= $row['fullname'] ?>">
</div>

<div class="form-row">
<label>Username</label>
<input type="text" name="username" value="<?= $row['username'] ?>">
</div>

<div class="form-row">
<label>Password</label>
<input type="text" name="password" value="<?= $row['password'] ?>">
</div>

<div class="form-row">
<label>Role</label>

<select name="role">

<option value="admin" <?= $row['role']=="admin"?'selected':'' ?>>Admin</option>

<option value="staff" <?= $row['role']=="staff"?'selected':'' ?>>Staff</option>

</select>

</div>

<button class="btn-save" name="update">Update User</button>

</div>

</form>

</div>

</div>

<?php include "../includes/footer.php"; ?>