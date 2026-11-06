<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $con = mysqli_connect("localhost", "Markward", "Jacob!2", "petservices_db");

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
    <title>Login</title>
</head>
<body>
    <h2>Employee Login</h2>
    <p style="color:red"><?php echo $error; ?></p>
    <form method="POST">
        <input type="number" name="Eid" placeholder="Employee Id" required>
        <input type="password" name="Password" placeholder="Password" required>
        <input type="submit" value="Login">
    </form>
</body>
</html>
