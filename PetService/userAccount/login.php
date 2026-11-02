<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
   <?php
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $con = mysqli_connect("localhost", "Markward", "Jacob!2", "petservices_db");
        $stmt = mysqli_prepare($con, "SELECT OId, Password FROM owner WHERE Email = ?");
        mysqli_stmt_bind_param($stmt, "s", $_POST['Email']);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($row && $_POST['Password'] === $row['Password']) {
            $_SESSION['OId'] = $row['OId'];
            header("Location: ../userAccount/selectPet.php");
            exit;
        }
        echo "Invalid credentials";
    }
    ?>
    <div class="container">
        <div class="title">Login</div>
        <div class="content">
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
                <div class="button">
                    <input type="submit" value="Login">
                </div>
                <p class="x"><strong>Did not register?<li><a href="../userAccount/register.php">Register</strong></a></li>
            </form>
        </div>
    </div>


</body>

</html>
