<?php

session_start();

$host = "localhost";
$dbname = "petservices_db";
$user = "root";
$password = "";

if (empty($_SESSION['OId'])) {
    exit("Please log in before submitting a review.");
}

try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $ownerId = (int) $_SESSION['OId'];
        $appointmentId = filter_input(INPUT_POST, 'AId', FILTER_VALIDATE_INT);
        $rating = (int) ($_POST['rating'] ?? 0);
        $reviewBody = trim($_POST['review-body'] ?? '');
        
        if ($appointmentId && $rating >= 1 && $rating <= 5 && !empty($reviewBody)){
            $check = $pdo->prepare(
                "SELECT a.AId
                 FROM appointment a
                 JOIN pet p ON a.PId = p.PId
                 LEFT JOIN review r ON a.AId = r.AId
                 WHERE a.AId = :aid
                 AND p.OId = :oid
                 AND a.EndDate <= NOW()
                 AND r.AId IS NULL"
            );
            
            $check->execute([
                ':aid' => $appointmentId,
                ':oid' => $ownerId
            ]);


            if (!$check->fetch()) {
                exit("This appointment is not eligible for a review.");
            }

            $sql = "INSERT INTO review (AId, Body, Rating) VALUES (:aid, :body, :rating)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':aid' => $appointmentId,
                ':body' => $reviewBody,
                ':rating' => $rating
            ]);

            echo "<h2>Thank you!</h2>";
            echo "<p>Your review has been recorded.</p>";
            echo '<p><a href="index.html">Back to home</a></p>';

        } else {
            echo "<p>Error: Please select an appointment, choose a rating, and write a review.</p>";
        }
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo "<p>Sorry, your review could not be saved.</p>";
}
?>