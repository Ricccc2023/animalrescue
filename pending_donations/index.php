<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Pending Donations";
$active = "pending_donations";

/* FETCH DATA */
$result = $conn->query("SELECT * FROM pending_donations ORDER BY id DESC");
$rows = $result->fetch_all(MYSQLI_ASSOC);

/* TOTAL */
$total = $conn->query("SELECT SUM(amount) as total FROM pending_donations")
              ->fetch_assoc()['total'] ?? 0;

ob_start();
?>

<div class="page-header">
    <div class="page-title">
        <h2>Pending Donations</h2>
    </div>
    <div class="page-action" style="margin-left:90px;">
        
        <a href="create.php" class="btn-add">+ Add Donation</a>
        <a href="../donations/index.php" class="btn-add">Back</a>
    </div>
</div>

<div class="card" style="margin-bottom:15px;">
    <b>Total Pending:</b> ₱ <?= number_format($total,2) ?>
</div>

<div class="card">
<div class="table-wrap">
<table>

<tr>
    <th>Donor</th>
    <th>Amount</th>
    <th>Receipt</th>
    <th>Date</th>
    <th>Actions</th>
</tr>

<?php foreach($rows as $r): ?>
<tr>
    <td><?= htmlspecialchars($r['donor_name']) ?></td>
    <td>₱ <?= number_format($r['amount'],2) ?></td>
    
    <td>
        <?php if($r['gcash_receipt']): ?>
            <img src="/animalrescue_db/uploads/gcash_receipts/<?= $r['gcash_receipt'] ?>" width="40">
        <?php else: ?>
            —
        <?php endif; ?>
    </td>

    <td><?= $r['donation_date'] ?></td>

    <td>
        <div class="actions">

            <?php if($r['gcash_receipt']): ?>
                <button class="action-btn action-success"
                    onclick="openModal('/animalrescue_db/uploads/gcash_receipts/<?= $r['gcash_receipt'] ?>')">
                    View
                </button>
            <?php endif; ?>

            <a href="confirm.php?id=<?= $r['id'] ?>" 
               class="action-btn action-success"
               onclick="return confirm('Confirm this donation?')">
               Confirm
            </a>

            <a href="edit.php?id=<?= $r['id'] ?>" 
               class="action-btn action-secondary">
               Edit
            </a>

            <a href="archive.php?id=<?= $r['id'] ?>" 
               class="action-btn action-danger"
               onclick="return confirm('Delete this record?')">
               Delete
            </a>

        </div>
    </td>
</tr>
<?php endforeach; ?>

<?php if(count($rows) == 0): ?>
<tr>
    <td colspan="5">No pending donations found.</td>
</tr>
<?php endif; ?>

</table>
</div>
</div>

<!-- MODAL -->
<div id="imgModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <img id="modalImg" src="">
        <br>
        <button class="btn-save" onclick="closeModal()">Back</button>
    </div>
</div>

<style>
.modal {
    display: none; /* ✅ FIXED */
    position: fixed;
    z-index: 999;
    inset: 0;
    background: rgba(0,0,0,0.7);
    align-items: center;
    justify-content: center;
}

.modal.show {
    display: flex; /* lalabas lang pag may class na show */
}

.modal-content {
    background: #fff;
    padding: 20px;
    max-width: 500px;
    width: 90%;
    text-align: center;
    position: relative;
    border-radius: 10px;
}

.modal-content img {
    max-width: 100%;
    height: auto;
    margin-bottom: 15px;
}

.close-btn {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 22px;
    cursor: pointer;
}
</style>

<script>
function openModal(src) {
    document.getElementById("modalImg").src = src;
    document.getElementById("imgModal").classList.add("show");
}

function closeModal() {
    document.getElementById("imgModal").classList.remove("show");
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";
?>