<?php
session_start();
require_once "includes/config.php"; 
// Note: Hindi na natin kailangan mag-fetch ng pets dito dahil sa pets.php na ang gallery natin.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strays Worth Saving - Portal</title>
    <style>
        /* Modern Minimalist & Boxy Style */
        body { 
            margin: 0; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f8f9fa; 
            overflow-x: hidden;
        }

        /* Watermark Background */
        body::before { 
            content: ""; 
            position: fixed; 
            inset: 0; 
            background-image: url('logo.png'); 
            background-repeat: repeat; 
            background-size: 200px; 
            opacity: 0.04; 
            z-index: -1; 
            pointer-events: none; 
        }
        .topbar { 
            background: #409fcf; 
            color: white; 
            padding: 6px 20px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            font-size: 24px;
            font-weight: 600;
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

        .center-wrapper { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 60vh; 
            margin-top: 20px;
        }

        .booking-card { 
            background: white; 
            width: 510px;
            height: 425px; 
            padding: 50px 40px; 
            border-radius: 0px; /* Modern boxy corners */
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            text-align: center; 
            border: 1px solid #eee;
            margin-top: 20px;
            margin-bottom: 0px;
        }

        .section-box { 
            display: inline-block; 
            background: #f1f5f9; 
            padding: 8px 16px; 
            border-radius: 4px; 
            margin: 10px 0; 
            font-size: 13px; 
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .action-btn { 
            display: inline-block; 
            background: #409fcf; 
            color: white; 
            padding: 14px 28px; 
            border-radius: 4px; 
            text-decoration: none; 
            font-weight: bold; 
            border: none; 
            cursor: pointer; 
            font-size: 15px; 
            margin: 8px;
            transition: 0.3s;
        }
        .action-btn:hover { background: #358bb8; transform: translateY(-3px); box-shadow: 0 4px 12px rgba(64, 159, 207, 0.3); }
        .btn-dark { background: #1f4e46; }
        .btn-dark:hover { background: #163a34; }

        /* Modal Styles */
        .modal { 
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(64, 159, 207, 0); /* Themed backdrop */
            backdrop-filter: blur(3px);
        }

        .modal-content { 
            background: #fff; 
            margin: 8% auto; 
            padding: 35px; 
            border-radius: 8px; 
            width: 450px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.2); 
            position: relative; 
        }

        .form-group { margin-bottom: 18px; text-align: left; }
        .form-group label { display: block; margin-bottom: 7px; font-weight: bold; color: #1f4e46; font-size: 14px; }
        .form-group input, .form-group textarea { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #cbd5e1; 
            border-radius: 4px; 
            box-sizing: border-box; 
            font-family: inherit;
        }
        
        .modal-header { margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; }
        .modal-header h3 { margin: 0; color: #1f4e46; }

        .close-btn { 
            background: #f1f5f9; 
            border: none; 
            padding: 10px 20px; 
            border-radius: 4px; 
            cursor: pointer; 
            color: #64748b; 
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="topbar">
    <div class="topbar-info">
        <b>Strays Worth Saving Management System</b>
        <small>Welcome, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Guest') ?></small>
    </div>
    <a href="login.php" class="admin-btn">Admin Login</a>
</div>

<div class="center-wrapper">
    <div class="booking-card">
        <div style="margin-bottom:25px;">
            <img src="logo.png" alt="Logo" style="width:100px; border-radius:12px;">
        </div>
        
        <h1 style="font-size:36px; margin:0; color:#1f4e46; letter-spacing:-1px;">SWS Adoption Portal</h1>
        <div class="section-box">Tanauan, Batangas, Philippines</div>

        <div style="text-align:left; margin: 0px 0; background:#f8fafc; padding:25px; border-left:4px solid #409fcf; border-radius:4px;">
            <b style="color:#1f4e46; font-size:16px;">General Guidelines:</b>
            <ol style="margin: 12px 0 0 0; padding-left:20px; font-size:14px; color:#475569; line-height:1.7;">
                <li>Click <b>Adopt Now</b> to view our available furry friends.</li>
                <li>Submit a request for adoption, donation, or volunteering.</li>
                <li>Wait for our team to contact you for the next steps.</li>
            </ol>
        </div>

        <div style="margin-top:20px;">
            <a href="requestsadoptions/pets.php" class="action-btn">Adopt Now!</a>
            
            <a href="public_donate.php" class="action-btn">Donate</a>

            <button type="button" onclick="openModal('volunteerModal')" class="action-btn">Be a Volunteer</button>
        </div>
    </div>
</div>

<div id="volunteerModal" class="modal">
    <div class="modal-content">
        <div class="modal-header"><h3>Volunteer Application</h3></div>
            <form action="/animalrescue_db/volunteers/process_volunteer.php" method="POST">
                <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact" placeholder="09123456789" required>
            </div>
            <div class="form-group">
                <label>Preferred Role</label>
                <input type="text" name="role" placeholder="Rescuer, Shelter Cleaner" required>
            </div>
            <div style="display:flex; gap:10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal('volunteerModal')" class="close-btn">Cancel</button>
                <button type="submit" name="submit_volunteer" class="action-btn" style="margin:0;">Join Team</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).style.display = 'block';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = "none";
    }
}
</script>

</body>
</html>