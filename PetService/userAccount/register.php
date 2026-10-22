<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
</head>

<body>
    <section>
        <h3>DB Connection</h3>
        <?php
           $con = mysqli_connect("localhost", "Markward", "Jacob!2", "PetService_db");

            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }
        ?>
    </section>

    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $Username = $_POST['Username'];
        $Email = $_POST['Email'];
        $Password = $_POST['Password'];
        $PhoneNumber = $_POST['PhoneNumber'];
        $Address = $_POST['Address'];

        

        $query = "INSERT INTO `owner` (Name, Email, Password, PhoneNumber, Address) VALUES ('$Username', '$Email', '$Password', '$PhoneNumber', '$Address')";
        $result = mysqli_query($con, $query);
        if ($result) {
            echo ("Registration Succesful");
            header("Location: ../userAccount/login.php");
        } else {
            echo ("Fail");
        }
    }
    ?>


    <b>Ideally, after registration, it should lead to add pet page</b>

    <div class="container">
        <div class="title">Registration</div>
        <div class="content">
            <form action="" method="POST">
                <div class="user-details">
                    <div class="input-box">
                        <span class="details">Username</span>
                        <input type="text" placeholder="Enter your username" id="Username" name="Username" required>
                    </div>
                    <div class="input-box">
                        <span class="details">Email</span>
                        <input type="email" placeholder="Enter your email" id="Email" name="Email" required>
                    </div>
                    <div class="input-box">
                        <span class="details">Password</span>
                        <input type="password" placeholder="Enter your password" id="Password" name="Password" required>
                    </div>
                    <div class="input-box">
                        <span class="details">Phone Number</span>
                        <input type="tel" placeholder="Enter phone number" id="Phone" name="Phone" required>
                    </div>
                    <div class="input-box">
                        <span class="details">Address</span>
                        <input type="text" placeholder="Enter Home Address" id="Address" name="Address" required>
                    </div>
                </div>
                <div class="button">
                    <input type="submit" value="Register">
                    <p class="login"><strong>Already have an account ?</strong><a href="../userAccount/login.php">Login.</a></p>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
