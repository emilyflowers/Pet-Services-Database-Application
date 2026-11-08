<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $con = mysqli_connect("localhost", "root", "", "petservices_db");

    $stmt = mysqli_prepare($con, "SELECT EId, Password, IsAdmin FROM employee WHERE EId = ?");
    mysqli_stmt_bind_param($stmt, "i", $_POST['Eid']);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($row && $_POST['Password'] === $row['Password']) {
        $_SESSION['Eid'] = $row['EId'];
        $_SESSION['IsAdmin'] = $row['IsAdmin'];
        header("Location: empHome.php");
        exit;
    }
    $error = "Invalid credentials";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Employee</title>
    <link rel="stylesheet" href="../pages/style.css"> 
    <h1 class="header">Pet Services</h1>  
</head>
<body>
    <div class="main-content">
    <h2>Employee Login</h2>
    <p style="color:red"><?php echo $error; ?></p>
    <form method="POST">
        <div class="user-details">
            <div class="input-box">
                <span class="details"><strong>Employee ID:</strong></span>
                <input type="number" name="Eid" placeholder="Employee Id" required>
            </div>
            <div class="input-box">
                <span class="details"><strong>Password:</strong></span>
                <input type="password" name="Password" placeholder="Password" required>
            </div>
        </div>
              
        <div class="button-container">
            <button type="submit" name="action" value="Login" class="login-menu-button">Login</button>   
        </div>

    </form>
    </div>
</body>
</html>