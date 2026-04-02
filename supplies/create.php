<?php
// 1. Initial setup and authentication
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Add Supply";
$active = "supplies";

// 2. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = $_POST['item_name'];
    $category  = $_POST['category'];
    $quantity  = $_POST['quantity'];
    $unit      = $_POST['unit'];
    $status    = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO supplies (item_name, category, quantity, unit, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $item_name, $category, $quantity, $unit, $status);

    if ($stmt->execute()) {
        header("Location: index.php?success=1");
        exit();
    }
}

// 3. Start capturing content for the master layout
ob_start();
?>

<h2>Add New Supply</h2>
<p style="color:#666; margin-bottom:20px;">
    Register a new item in the inventory
</p>

<div class="card" style="max-width: 600px;">
    <form method="POST" action="">
        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px;">Item Name</label>
            <input type="text" name="item_name" placeholder="e.g. Dog Food (Kibble)" required style="width:100%;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display:block; margin-bottom:5px;">Category</label>
            <select name="category" required style="width:100%;">
                <option value="">-- Select Category --</option>
                <option value="Food">Food</option>
                <option value="Medicine">Medicine</option>
                <option value="Cleaning">Cleaning</option>
                <option value="Equipment">Equipment</option>
                <option value="Others">Others</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:5px;">Quantity</label>
                <input type="number" name="quantity" required style="width:100%;">
            </div>
            <div style="flex: 1;">
                <label style="display:block; margin-bottom:5px;">Unit</label>
                <input type="text" name="unit" placeholder="e.g. Bags, Bottles" required style="width:100%;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display:block; margin-bottom:5px;">Status</label>
            <select name="status" style="width:100%;">
                <option value="In Stock">In Stock</option>
                <option value="Low Stock">Low Stock</option>
                <option value="Out of Stock">Out of Stock</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn-add" style="border:none; cursor:pointer;">Save Item</button>
            <a href="index.php" class="btn-add" style="background:#666; text-decoration:none; text-align:center;">Cancel</a>
        </div>
    </form>
</div>

<?php
// 4. Inject the content into the master layout
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";
?>