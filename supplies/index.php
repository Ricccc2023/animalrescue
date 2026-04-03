<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Supplies";
$active = "supplies";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_supply'])) {
        $stmt = $conn->prepare("INSERT INTO supplies (item_name, category, quantity, unit, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $_POST['item_name'], $_POST['category'], $_POST['quantity'], $_POST['unit'], $_POST['status']);
        $stmt->execute();
    } elseif (isset($_POST['update_supply'])) {
        $stmt = $conn->prepare("UPDATE supplies SET item_name=?, category=?, quantity=?, unit=?, status=? WHERE id=?");
        $stmt->bind_param("ssissi", $_POST['item_name'], $_POST['category'], $_POST['quantity'], $_POST['unit'], $_POST['status'], $_POST['id']);
        $stmt->execute();
    }
    header("Location: index.php");
    exit();
}

/* =========================
FILTERS (UPGRADED)
========================= */

$search   = trim($_GET['search'] ?? '');
$category = $_GET['category'] ?? '';
$status   = $_GET['status'] ?? '';
$order    = ($_GET['sort'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

$params = [];
$types  = "";

/* =========================
BASE QUERY
========================= */

$sql = "SELECT * FROM supplies WHERE 1=1";

/* =========================
SEARCH
========================= */

if ($search !== '') {
    $sql .= " AND (
        item_name LIKE ?
        OR category LIKE ?
    )";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}

/* =========================
CATEGORY FILTER
========================= */

if ($category !== '') {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

/* =========================
STATUS FILTER
========================= */

if ($status !== '') {
    $sql .= " AND status = ?";
    $params[] = $status;
    $types .= "s";
}

$sql .= " ORDER BY id $order";

/* =========================
EXECUTE
========================= */

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$supplies = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

ob_start();
?>

<style>
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
    .modal-content { background: #fff; margin: 8% auto; padding: 25px; border-radius: 8px; width: 450px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
    .modal-footer { margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end; }
</style>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2>Supplies Inventory</h2>
    </div>
    <button onclick="openModal('addModal')" class="btn-add" style="border:none; cursor:pointer;">+ Add New Supply</button>
</div>

<div class="card" style="margin-bottom:20px;">
<form method="GET" class="filter-bar" style="display:flex; gap:10px; flex-wrap:wrap;">

<input type="text" 
       name="search"
       placeholder="Search item or category..."
       value="<?= htmlspecialchars($search) ?>"
       style="flex:1; min-width:200px;">

<select name="category">
    <option value="">All Category</option>
    <option value="Food" <?= $category=='Food'?'selected':'' ?>>Food</option>
    <option value="Medicine" <?= $category=='Medicine'?'selected':'' ?>>Medicine</option>
    <option value="Cleaning" <?= $category=='Cleaning'?'selected':'' ?>>Cleaning</option>
    <option value="Equipment" <?= $category=='Equipment'?'selected':'' ?>>Equipment</option>
</select>

<select name="status">
    <option value="">All Status</option>
    <option value="In Stock" <?= $status=='In Stock'?'selected':'' ?>>In Stock</option>
    <option value="Low Stock" <?= $status=='Low Stock'?'selected':'' ?>>Low Stock</option>
    <option value="Out of Stock" <?= $status=='Out of Stock'?'selected':'' ?>>Out of Stock</option>
</select>

<select name="sort">
    <option value="DESC" <?= $order==='DESC'?'selected':'' ?>>Newest</option>
    <option value="ASC" <?= $order==='ASC'?'selected':'' ?>>Oldest</option>
</select>

<button type="submit" class="btn-add" style="border:none; cursor:pointer;">Filter</button>

<?php if($search || $category || $status): ?>
<a href="index.php" class="btn-save" style="background:#666; text-decoration:none;">Clear</a>
<?php endif; ?>

</form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($supplies)): ?>
                    <tr><td colspan="5" style="text-align:center; padding:20px;">No supply records found.</td></tr>
                <?php else: ?>

                <?php foreach ($supplies as $s): ?>
                <tr>
                    <td><b><?= htmlspecialchars($s['item_name']) ?></b></td>
                    <td><?= htmlspecialchars($s['category']) ?></td>
                    <td><?= htmlspecialchars($s['quantity']) ?> <?= htmlspecialchars($s['unit']) ?></td>
                    <td>
                        <span style="color: <?= ($s['status'] == 'Out of Stock') ? 'red' : 'green' ?>; font-weight:bold;">
                            <?= htmlspecialchars($s['status']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="actions">
                            <button class="btn-save btn-sm" onclick='viewItem(<?= json_encode($s) ?>)'>View</button>
                            <button class="btn-save btn-sm" onclick='editItem(<?= json_encode($s) ?>)'>Edit</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODALS (UNCHANGED) -->

<div id="addModal" class="modal">
    <div class="modal-content">
        <h3>Add New Supply</h3>
        <form method="POST">
            <div class="form-group"><label>Item Name</label><input type="text" name="item_name" required></div>
            <div class="form-group">
                <label>Category</label>
                <select name="category">
                    <option value="Food">Food</option>
                    <option value="Medicine">Medicine</option>
                    <option value="Cleaning">Cleaning</option>
                    <option value="Equipment">Equipment</option>
                </select>
            </div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;"><label>Qty</label><input type="number" name="quantity" required></div>
                <div class="form-group" style="flex:1;"><label>Unit</label><input type="text" name="unit" placeholder="pcs, bags" required></div>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="In Stock">In Stock</option>
                    <option value="Low Stock">Low Stock</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('addModal')" class="btn-save" style="background:#666;">Cancel</button>
                <button type="submit" name="add_supply" class="btn-add">Save Item</button>
            </div>
        </form>
    </div>
</div>

<div id="viewModal" class="modal">
    <div class="modal-content">
        <h3>Supply Details</h3>
        <hr>
        <div id="viewContent" style="line-height: 2.5; margin-top: 15px;"></div>
        <div class="modal-footer">
            <button type="button" onclick="closeModal('viewModal')" class="btn-add">Close</button>
        </div>
    </div>
</div>

<div id="editModal" class="modal">
    <div class="modal-content">
        <h3>Edit Supply Item</h3>
        <form method="POST">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-group"><label>Item Name</label><input type="text" name="item_name" id="edit_name" required></div>
            <div class="form-group">
                <label>Category</label>
                <select name="category" id="edit_category">
                    <option value="Food">Food</option>
                    <option value="Medicine">Medicine</option>
                    <option value="Cleaning">Cleaning</option>
                    <option value="Equipment">Equipment</option>
                </select>
            </div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;"><label>Qty</label><input type="number" name="quantity" id="edit_qty" required></div>
                <div class="form-group" style="flex:1;"><label>Unit</label><input type="text" name="unit" id="edit_unit" required></div>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="edit_status">
                    <option value="In Stock">In Stock</option>
                    <option value="Low Stock">Low Stock</option>
                    <option value="Out of Stock">Out of Stock</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('editModal')" class="btn-save" style="background:#666;">Cancel</button>
                <button type="submit" name="update_supply" class="btn-add">Update Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).style.display = 'block'; }
function closeModal(id) { document.getElementById(id).style.display = 'none'; }

function viewItem(data) {
    document.getElementById('viewContent').innerHTML = `
        <strong>Item Name:</strong> ${data.item_name}<br>
        <strong>Category:</strong> ${data.category}<br>
        <strong>Inventory:</strong> ${data.quantity} ${data.unit}<br>
        <strong>Current Status:</strong> <span style="color:${data.status === 'Out of Stock' ? 'red' : 'green'}">${data.status}</span>
    `;
    openModal('viewModal');
}

function editItem(data) {
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_name').value = data.item_name;
    document.getElementById('edit_category').value = data.category;
    document.getElementById('edit_qty').value = data.quantity;
    document.getElementById('edit_unit').value = data.unit;
    document.getElementById('edit_status').value = data.status;
    openModal('editModal');
}

window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";
?>