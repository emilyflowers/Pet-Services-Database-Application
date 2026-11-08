<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $con = mysqli_connect("localhost", "root", "", "petservices_db");
    $stmt = mysqli_prepare($con, "SELECT OId, Password FROM owner WHERE Email = ?");
    mysqli_stmt_bind_param($stmt, "s", $_POST['Email']);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($row && $_POST['Password'] === $row['Password']) {
        $_SESSION['OId'] = $row['OId'];
        header("Location: ownerHome.php");
        exit;
    }
    $error = "Invalid credentials";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pet Parent</title>
    <link rel="stylesheet" href="../pages/style.css"> 
    <h1 class="header">Pet Services</h1>  
</head>
<body>
    <div class="main-content">
        <h1>Login</h1>
        <div class="content">
            <p style="color:red"><?php echo $error; ?></p>
            <form action="" method="POST">
                <div class="user-details">
                    <div class="input-box">
                        <span class="details"><strong>Email</strong></span>
                        <input type="email" placeholder="Enter your email" id="Email" name="Email" required>
                    </div>
                    <div class="input-box">
                        <span class="details"><strong>Password</strong></span>
                        <input type="password" placeholder="Enter your password" id="Password" name="Password" required>
                    </div>
                </div>
              
                <div class="button-container">
                    <button type="submit" name="action" value="login" class="login-menu-button">Login</button>   
                </div>

                <p class="x"><strong>Did not register?</strong> <a href="register.php">Register</a></p>

            </form>
        </div>
    </div>
</body>
</html>