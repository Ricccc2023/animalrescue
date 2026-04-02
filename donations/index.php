<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Donations";
$active = "donations";

/* FETCH DONATIONS */
$result = $conn->query("SELECT * FROM donations ORDER BY id DESC");
$rows = $result->fetch_all(MYSQLI_ASSOC);

/* TOTAL */
$total = $conn->query("SELECT SUM(amount) as total FROM donations")
              ->fetch_assoc()['total'] ?? 0;

/* ✅ CHECK PENDING DONATIONS */
$pendingQuery = $conn->query("SELECT COUNT(*) as total FROM pending_donations");
$pendingData = $pendingQuery->fetch_assoc();
$hasPending = $pendingData['total'] > 0;
$pendingCount = $pendingData['total'];

ob_start();
?>

<h2>Donations</h2>
<p style="color:#666;margin-bottom:15px;">Track all donations</p>

<div class="card" style="margin-bottom:15px;">
    <b>Total Donations:</b> ₱ <?= number_format($total,2) ?>
</div>

<!-- BUTTONS -->
<a href="create.php" class="btn-add" style="margin-bottom:10px;">
    + Add Donation
</a>

<?php if($hasPending): ?>
    <a href="../pending_donations/index.php" class="btn-add" style="margin-bottom:10px;">
        New Donation (<?= $pendingCount ?>)
    </a>
<?php endif; ?>

<div class="card">

<div class="table-wrap">
<table>

<tr>
    <th>Donor</th>
    <th>Amount</th>
    <th>Date</th>
    <th>Actions</th>
</tr>

<?php foreach($rows as $r): ?>
<tr>
    <td><b><?= htmlspecialchars($r['donor_name']) ?></b></td>
    <td>₱ <?= number_format($r['amount'],2) ?></td>
    <td><?= $r['donation_date'] ?></td>

    <td>
        <div class="actions">
            <a href="view.php?id=<?= $r['id'] ?>" class="btn-save btn-sm">View</a>
            <a href="edit.php?id=<?= $r['id'] ?>" class="btn-save btn-sm">Edit</a>
            <a href="archive.php?id=<?= $r['id'] ?>" class="btn-decline btn-sm"
            onclick="return confirm('Delete this record?')">
            Delete
            </a>
        </div>
    </td>
</tr>
<?php endforeach; ?>

<?php if(count($rows) == 0): ?>
<tr>
    <td colspan="4">No donations found.</td>
</tr>
<?php endif; ?>

</table>
</div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";
?>