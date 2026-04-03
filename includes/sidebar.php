<?php
define('BASE_URL', '/animalrescue_db');

$role = $_SESSION['role'] ?? '';
?>

<div class="sidebar">

<div class="nav">

<a href="<?= BASE_URL ?>/dashboard.php">
Dashboard
</a>

<a href="<?= BASE_URL ?>/inventory/index.php">
Animals
</a>

<a href="<?= BASE_URL ?>/rescues/index.php">
Rescues
</a>

<a href="<?= BASE_URL ?>/volunteers/index.php">
Volunteers
</a>


<a href="<?= BASE_URL ?>/adoptions/index.php">
Adoptions
</a>

<?php if($role === 'admin'): ?>
<a href="<?= BASE_URL ?>/donations/index.php">
Donations
</a>
<?php endif; ?>

<a href="<?= BASE_URL ?>/supplies/index.php">
Supplies
</a>

<a href="<?= BASE_URL ?>/records/index.php">
Records
</a>

</div>

<div class="power">

<a href="<?= BASE_URL ?>/staffs/index.php">
Users
</a>

<?php if($role === 'admin'): ?>
<a href="<?= BASE_URL ?>/payroll/index.php">
Payroll
</a>
<?php endif; ?>

<a class="logout-btn" href="<?= BASE_URL ?>/logout.php">
Logout
</a>

</div>

</div>