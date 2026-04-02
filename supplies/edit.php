<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Edit Supply";
$active = "supplies";

$id = $_GET['id'] ?? 0;

// Handle Update Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = $_POST['item_name'];
    $category  = $_POST['category'];
    $quantity  = $_POST['quantity'];
    $unit      = $_POST['unit'];
    $status    = $_POST['status'];

    $stmt = $conn->prepare("UPDATE supplies SET item_name=?, category=?, quantity=?, unit=?, status=? WHERE id=?");
    $stmt->bind_param("ssissi", $item_name, $category, $quantity, $unit, $status, $id);

    if ($stmt->execute()) {
        header("Location: index.php?updated=1");
        exit();
    }
}

// Fetch current data
$stmt = $conn->prepare("SELECT * FROM supplies WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$supply = $stmt->get_result()->fetch_assoc();

if (!$supply) {
    die("Supply item not found.");
}

ob_start();
?>

<h2>Edit Supply Item</h2>
<p style="color:#666; margin-bottom:20px;">Update the information for this supply record.</p>

<div class="card" style="max-width: 600px;">
    <form method="POST">
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold;">Item Name</label>
            <input type="text" name="item_name" value="<?= htmlspecialchars($supply['item_name']) ?>" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold;">Category</label>
            <select name="category" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
                <option value="Food" <?= $supply['category'] == 'Food' ? 'selected' : '' ?>>Food</option>
                <option value="Medicine" <?= $supply['category'] == 'Medicine' ? 'selected' : '' ?>>Medicine</option>
                <option value="Cleaning" <?= $supply['category'] == 'Cleaning' ? 'selected' : '' ?>>Cleaning</option>
                <option value="Equipment" <?= $supply['category'] == 'Equipment' ? 'selected' : '' ?>>Equipment</option>
                <option value="Others" <?= $supply['category'] == 'Others' ? 'selected' : '' ?>>Others</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Quantity</label>
                <input type="number" name="quantity" value="<?= htmlspecialchars($supply['quantity']) ?>" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
            </div>
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Unit</label>
                <input type="text" name="unit" value="<?= htmlspecialchars($supply['unit']) ?>" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold;">Status</label>
            <select name="status" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
                <option value="In Stock" <?= $supply['status'] == 'In Stock' ? 'selected' : '' ?>>In Stock</option>
                <option value="Low Stock" <?= $supply['status'] == 'Low Stock' ? 'selected' : '' ?>>Low Stock</option>
                <option value="Out of Stock" <?= $supply['status'] == 'Out of Stock' ? 'selected' : '' ?>>Out of Stock</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn-add" style="border:none; cursor:pointer;">Update Item</button>
            <a href="index.php" class="btn-save" style="background:#666; text-decoration:none; text-align:center; padding: 10px 20px;">Cancel</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";
?>