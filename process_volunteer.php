<?php
require_once "includes/config.php";

if (isset($_POST['submit_volunteer'])) {
    // 1. Kunin ang input mula sa form
    // Siguraduhin na ang 'name' attribute sa HTML form ay match dito
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $role    = mysqli_real_escape_string($conn, $_POST['role']);

    // 2. I-save sa 'volunteers' table
    // Ginaya ko ang structure base sa nakita ko sa 'Edit Volunteer' screen mo
    $sql = "INSERT INTO volunteers (name, contact, role) 
            VALUES ('$name', '$contact', '$role')";

    if (mysqli_query($conn, $sql)) {
        // 3. Popup alert at babalik sa homepage
        echo "<script>
                alert('Thank you for volunteering, $name! Your record has been saved.');
                window.location.href = 'index.php';
            </script>";
    } else {
        echo "<script>
                alert('Error: " . mysqli_error($conn) . "');
                window.location.href = 'index.php';
            </script>";
    }
} else {
    header("Location: index.php");
}
exit();
?>