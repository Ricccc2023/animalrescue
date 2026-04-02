<?php
include "../includes/auth.php";
include "../includes/config.php";
include "../includes/header.php";
include "../includes/sidebar.php";

if(isset($_POST['save'])){

$fullname = mysqli_real_escape_string($conn,$_POST['fullname']);
$username = mysqli_real_escape_string($conn,$_POST['username']);
$password = mysqli_real_escape_string($conn,$_POST['password']);
$role = mysqli_real_escape_string($conn,$_POST['role']);

$query = "INSERT INTO users (fullname,username,password,role,availability)
VALUES ('$fullname','$username','$password','$role',0)";

$result = mysqli_query($conn,$query);

if($result){
    echo "<script>
    window.location='index.php';
    </script>";
}else{
    echo "Error: " . mysqli_error($conn);
}

}
?>

<div class="main">

<div class="page-header">

<div class="page-title">
<h2>Add User</h2>
</div>

</div>

<div class="card">

<form method="POST">

<div class="form-wrapper">

<div class="form-row">
<label>Full Name</label>
<input type="text" name="fullname" required>
</div>

<div class="form-row">
<label>Username</label>
<input type="text" name="username" required>
</div>

<div class="form-row">
<label>Password</label>
<input type="password" name="password" required>
</div>

<div class="form-row">
<label>Role</label>
<select name="role">
<option value="admin">Admin</option>
<option value="staff">Staff</option>
</select>
</div>

<button class="btn-save" name="save">Save User</button>

</div>

</form>

</div>

</div>

<?php include "../includes/footer.php"; ?>