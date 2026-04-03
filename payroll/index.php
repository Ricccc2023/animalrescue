<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

/* =============================
   ADMIN SECURITY
============================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>
        alert('Access Denied. Admin only.');
        window.location='../dashboard.php';
    </script>";
    exit;
}

/* =============================
   FILTER (FIXED FORMAT)
============================= */
$month = str_pad((int)($_GET['month'] ?? date("m")), 2, '0', STR_PAD_LEFT);
$year  = (int)($_GET['year'] ?? date("Y"));

/* =============================
   UPDATE PER DAY SALARY
============================= */
if (isset($_POST['update_salary'])) {
    $id = (int)$_POST['user_id'];
    $salary = (float)$_POST['per_day'];

    mysqli_query($conn, "
        UPDATE users
        SET per_day = '$salary'
        WHERE id = $id
    ");
}

/* =============================
   FETCH STAFF
============================= */
$q = mysqli_query($conn, "
    SELECT *
    FROM users
    WHERE role='staff'
    ORDER BY fullname ASC
");

include __DIR__ . "/../includes/header.php";
include __DIR__ . "/../includes/sidebar.php";
?>

<div class="main">

    <div class="page-header">
        <div class="page-title">
            <h2>Payroll Management</h2>
        </div>
    </div>

    
    <div class="card">
        <form method="GET" class="filter-bar">

            <label>Month:</label>
            <select name="month">
                <?php for ($m = 1; $m <= 12; $m++): 
                    $val = str_pad($m, 2, '0', STR_PAD_LEFT);
                ?>
                    <option value="<?= $val ?>" <?= $month == $val ? 'selected' : '' ?>>
                        <?= date("F", mktime(0, 0, 0, $m, 1)) ?>
                    </option>
                <?php endfor; ?>
            </select>

            <label>Year:</label>
            <select name="year">
                <?php for ($y = date("Y"); $y >= 2024; $y--): ?>
                    <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>>
                        <?= $y ?>
                    </option>
                <?php endfor; ?>
            </select>

            <button type="submit" class="btn-search">Filter</button>

        </form>
    </div>

    
    <div class="card">
        <div class="table-wrap">
            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Per Day</th>
                        <th>Days Worked</th>
                        <th>Total Salary</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (mysqli_num_rows($q) == 0): ?>
                    <tr>
                        <td colspan="6">No staff found.</td>
                    </tr>
                <?php else: ?>

                    <?php while ($row = mysqli_fetch_assoc($q)): ?>

                        <?php
                        
                        $userId = (int)$row['id'];
                        $attendance = [];

                        $res = mysqli_query($conn, "
                            SELECT DATE(time) as day
                            FROM attendance
                            WHERE user_id = $userId
                            AND type = 'IN'
                            AND MONTH(time) = '$month'
                            AND YEAR(time) = '$year'
                        ");

                        while ($r = mysqli_fetch_assoc($res)) {
                            $attendance[$r['day']] = true;
                        }

                        $daysWorked = count($attendance);

                        
                        $perDay = (float)$row['per_day'];
                        $totalSalary = $daysWorked * $perDay;
                        ?>

                        <tr>

                            <td><?= $row['id'] ?></td>

                            <td>
                                <strong><?= htmlspecialchars($row['fullname']) ?></strong>
                            </td>

                            <td>
                                <form method="POST" style="display:flex; gap:5px;">
                                    <input type="hidden" name="user_id" value="<?= $row['id'] ?>">

                                    <input type="number"
                                        name="per_day"
                                        value="<?= $row['per_day'] ?>"
                                        step="0.01"
                                        style="width:100px;">

                                    <button name="update_salary" class="action-btn action-success">
                                        Save
                                    </button>
                                </form>
                            </td>

                            <td><?= $daysWorked ?></td>

                            <td>
                                <strong style="color:#198754;">
                                    ₱<?= number_format($totalSalary, 2) ?>
                                </strong>
                            </td>

                            <td>
                                <div class="actions">

                                    <a href="view.php?id=<?= $row['id'] ?>&month=<?= $month ?>&year=<?= $year ?>"
                                    class="action-btn action-success">
                                        View
                                    </a>

                                    <a href="print.php?id=<?= $row['id'] ?>&month=<?= $month ?>&year=<?= $year ?>"
                                    class="action-btn action-secondary"
                                    target="_blank">
                                        Print
                                    </a>

                                </div>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php endif; ?>

                </tbody>

            </table>
        </div>
    </div>

</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>