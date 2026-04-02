<?php
include "../includes/auth.php";
include "../includes/config.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$q = mysqli_query($conn,"SELECT * FROM archive_users ORDER BY archived_at DESC");
?>

<div class="main">

<div class="page-header">

<div class="page-title">
<h2>Archived Users</h2>
</div>

<div class="page-action">
<a href="index.php" class="btn-add">Back</a>
</div>

</div>

<div class="card">

<table>

<thead>
<tr>
<th>ID</th>
<th>Fullname</th>
<th>Username</th>
<th>Role</th>
<th>Archived Date</th>
</tr>
</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($q)){ ?>

<tr>

<td><?= $row['id'] ?></td>
<td><?= $row['fullname'] ?></td>
<td><?= $row['username'] ?></td>
<td><?= $row['role'] ?></td>
<td><?= $row['archived_at'] ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

<?php include "../includes/footer.php"; ?>