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

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $con = mysqli_connect("localhost", "Markward", "Jacob!2", "petservices_db");

            if (!$con) {
                $error = "Connection failed";
            } else {
                $Eid      = $_POST['Eid'];
                $Password = $_POST['Password'];

                $stmt = mysqli_prepare($con, "SELECT Eid, Password, IsAdmin FROM employee WHERE Eid = ? LIMIT 1");
                mysqli_stmt_bind_param($stmt, "s", $Eid);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);

                    if ($Password === $row['Password']) {
                        $_SESSION['Eid']     = $row['Eid'];
                        $_SESSION['IsAdmin'] = $row['IsAdmin'];

                        if ($row['IsAdmin'] == 1) {
                            header("Location: ../employeeAccount/adminHome.php");
                        } else {
                            header("Location: ../employeeAccount/empHome.php");
                        }
                        exit;
                    } else {
                        $error = "Invalid credentials";
                    }
                } else {
                    $error = "Invalid credentials";
                }
            }
        }
    ?>


    <div class="container">
        <div class="title">Login</div>
        <div class="content">
            <form action="" method="POST">
                <div class="user-details">
                    <div class="input-box">
                        <span class="details"><strong>Eid</strong></span>
                        <input type="integer" placeholder="Enter your employee Id" id="Eid" name="Eid" required>
                    </div>
                    <div class="input-box">
                        <span class="details"><strong>Password</strong></span>
                        <input type="password" placeholder="Enter your password" id="Password" name="Password" required>
                    </div>
                </div>
                <div class="button">
                    <input type="submit" value="Login">
                </div>
            </form>
        </div>
    </div>


</body>

</html>

