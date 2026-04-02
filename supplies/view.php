<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "View Supply";
$active = "supplies";

$id = $_GET['id'] ?? 0;

// Fetch supply details based on ID
$stmt = $conn->prepare("SELECT * FROM supplies WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$supply = $stmt->get_result()->fetch_assoc();

if (!$supply) {
    die("Supply item not found.");
}

ob_start();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Supply Details</h2>
    <a href="index.php" class="btn-save" style="background:#666; text-decoration:none;">Back to List</a>
</div>

<div class="card" style="max-width: 600px;">
    <div style="margin-bottom: 15px;">
        <label style="font-weight:bold; color:#555;">Item Name:</label>
        <p style="font-size: 1.2em; margin-top: 5px;"><?= htmlspecialchars($supply['item_name']) ?></p>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="font-weight:bold; color:#555;">Category:</label>
        <p><?= htmlspecialchars($supply['category']) ?></p>
    </div>

    <div style="display: flex; gap: 20px; margin-bottom: 15px;">
        <div style="flex: 1;">
            <label style="font-weight:bold; color:#555;">Quantity:</label>
            <p><?= htmlspecialchars($supply['quantity'] . ' ' . $supply['unit']) ?></p>
        </div>
        <div style="flex: 1;">
            <label style="font-weight:bold; color:#555;">Status:</label>
            <p>
                <span style="font-weight:bold; color: <?= ($supply['status'] == 'Out of Stock') ? 'red' : 'green' ?>;">
                    <?= htmlspecialchars($supply['status']) ?>
                </span>
            </p>
        </div>
    </div>

    <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
        <a href="edit.php?id=<?= $supply['id'] ?>" class="btn-add" style="text-decoration:none;">Edit Item</a>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";
?>