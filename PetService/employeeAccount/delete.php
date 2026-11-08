<?php
session_start();
if (($_SESSION['IsAdmin'] ?? 0) != 1) { header("Location: login.php"); exit; }

$con = mysqli_connect("localhost", "root", "", "petservices_db");
$type = $_GET['type'] ?? '';
$id = (int)($_GET['id'] ?? 0);
$back = "empHome.php";

try {
    if ($type === 'appointment') {
        mysqli_query($con, "DELETE FROM review WHERE AId = $id");
        mysqli_query($con, "DELETE FROM appointment WHERE AId = $id");
        $back .= "?action=appointments";

    } elseif ($type === 'review') {
        mysqli_query($con, "DELETE FROM review WHERE AId = $id");
        $back .= "?action=reviews";

    } elseif ($type === 'pet') {
        mysqli_query($con, "DELETE r FROM review r JOIN appointment a ON r.AId = a.AId WHERE a.PId = $id");
        mysqli_query($con, "DELETE FROM appointment WHERE PId = $id");
        mysqli_query($con, "DELETE FROM pet WHERE PId = $id");
        $back .= "?action=pets";

    } elseif ($type === 'owner') {
        mysqli_query($con, "DELETE r FROM review r JOIN appointment a ON r.AId = a.AId JOIN pet p ON a.PId = p.PId WHERE p.OId = $id");
        mysqli_query($con, "DELETE a FROM appointment a JOIN pet p ON a.PId = p.PId WHERE p.OId = $id");
        mysqli_query($con, "DELETE FROM owner WHERE OId = $id");   // pets are removed automatically (ON DELETE CASCADE)
        $back .= "?action=owners";

    } elseif ($type === 'employee') {
        if ($id == $_SESSION['Eid']) { die("You can't delete your own account."); }
        mysqli_query($con, "DELETE FROM employee WHERE EId = $id");
        $back .= "?action=employees";
    }
} catch (mysqli_sql_exception $e) {
    if ($type === 'employee') {
        die("This employee still has appointments assigned. Reassign them first (edit each appointment), then delete. <a href='empHome.php?action=employees'>Back</a>");
    }
    die("Could not delete. <a href='empHome.php'>Back</a>");
}

header("Location: $back");
exit;