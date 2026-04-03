<?php
session_start();
require_once "../includes/config.php"; 

// =============================
// ✅ PROCESS ADOPTION (SAFE)
// =============================
if(isset($_POST['submit_adoption'])) {

    $animal_id = intval($_POST['animal_id']);
    $name = mysqli_real_escape_string($conn, $_POST['adopter_name']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $date = date("Y-m-d");

    // IMPORTANT: siguraduhin may status column ka
    $query = "INSERT INTO adoptions 
              (animal_id, adopter_name, contact, address, adoption_date, status)
              VALUES 
              ('$animal_id', '$name', '$contact', '$address', '$date', 'Pending')";

    if(mysqli_query($conn, $query)) {

        // redirect sa tamang path
        header("Location: ../adoptions/index.php?success=1");
        exit;

    } else {
        die("Insert Error: " . mysqli_error($conn));
    }
}

// =============================
// ✅ FIXED QUERY (CASE SENSITIVE)
// =============================
$pets_query = "SELECT * FROM animals WHERE LOWER(status) = 'available' ORDER BY id DESC";
$pets_result = mysqli_query($conn, $pets_query);

if(!$pets_result){
    die("Query Error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Pets - Strays Worth Saving</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', Arial, sans-serif; background-color: #f8f9fa; }


.topbar { 
            background: #409fcf; 
            color: white; 
            padding: 15px 40px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
.topbar-info b { font-size: 18px; display: block; }
.topbar-info small { font-size: 12px; opacity: 0.9; }


        .admin-btn { 
            background: white; 
            color: #1f4e46; 
            padding: 10px 20px; 
            font-size: 14px; 
            font-weight: 600; 
            text-decoration: none; 
            border-radius: 4px; /* Boxy corners */
            transition: 0.3s;
        }
        .admin-btn:hover { background: #eef2f3; transform: translateY(-2px); }

        .back-btn { color: white; text-decoration: none; font-weight: bold; font-size: 14px; }
        
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .page-title { color: #1f4e46; margin-bottom: 30px; text-align: center; }

        /* Gallery Grid */
        .pet-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
        .pet-card { background: white; border-radius: 8px; border: 1px solid #eee; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .pet-img { width: 100%; height: 250px; object-fit: cover; }
        .pet-info { padding: 20px; }
        .pet-name { font-size: 22px; color: #1f4e46; margin: 0 0 10px 0; }
        .pet-tags { color: #409fcf; font-size: 12px; font-weight: bold; margin-bottom: 10px; text-transform: uppercase; }
        
        .action-btn { background: #409fcf; color: white; padding: 12px; border-radius: 4px; border: none; width: 100%; font-weight: bold; cursor: pointer; }

        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); }
        .modal-content { background: #fff; margin: 5% auto; padding: 30px; border-radius: 8px; width: 400px; position: relative; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        
        body::before { content: ""; position: fixed; inset: 0; background-image: url('logo.png'); background-repeat: repeat; background-size: 250px; opacity: 0.03; z-index: -1; pointer-events: none; }
    </style>
</head>
<body>

<div class="topbar">
    <div class="topbar-info">
        <b>Strays Worth Saving Management System</b>
        <small>Welcome, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Guest') ?></small>
    </div>
    <a href="/animalrescue_db/index.php" class="admin-btn">Back to Page</a>
</div>

<div class="container">
    <h1 class="page-title">Find Your New Best Friend</h1>
    
    <div class="pet-grid">
        <?php while($row = mysqli_fetch_assoc($pets_result)): 
            $img = "../uploads/animals/" . $row['image'];
            $display_img = (!empty($row['image']) && file_exists($img)) ? $img : "logo.png";
        ?>
            <div class="pet-card">
                <img src="<?= $display_img ?>" class="pet-img">
                <div class="pet-info">
                    <div class="pet-tags"><?= $row['type'] ?> • <?= $row['breed'] ?></div>
                    <h3 class="pet-name"><?= $row['name'] ?></h3>
                    <p style="font-size:14px; color:#666;"><?= $row['notes'] ?></p>
                    <button onclick="openAdoptModal('<?= $row['id'] ?>', '<?= $row['name'] ?>')" class="action-btn">Adopt Me</button>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<div id="adoptModal" class="modal">
    <div class="modal-content">
        <h3>Adopt <span id="pet_name_display"></span></h3>

        <form method="POST">
            <input type="hidden" name="animal_id" id="animal_id">
            <div class="form-group">
                <label>Your Name</label>
                <input type="text" name="adopter_name" required>
            </div>
            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact" required>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" required></textarea>
            </div>
            <button type="submit" name="submit_adoption" class="action-btn">Submit Request</button>
            <button type="button" onclick="closeModal()" style="background:none; border:none; color:gray; cursor:pointer; width:100%; margin-top:10px;">Close</button>
        </form>
    </div>
</div>

<script>
function openAdoptModal(id, name) {
    document.getElementById('animal_id').value = id;
    document.getElementById('pet_name_display').innerText = name;
    document.getElementById('adoptModal').style.display = 'block';
}
function closeModal() {
    document.getElementById('adoptModal').style.display = 'none';
}
</script>

</body>
</html>