<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee - Home</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <h1 class="header">Employee - Home</h1>  

        <div class="emp-menu">
            <button class="emp-menu-button">View Appointments</button>
            
            <!-- Form to trigger PHP logic  for View Reviews-->
            <form method="GET" action="employeemain.php" style="display:inline;">
                <button type="submit" name="action" value="reviews" class="emp-menu-button">View Reviews</button>
            </form>
            
            <!-- Button for Employees to add a new pet parent -->
            <form method="GET" action="adduser.html" style="display:inline;">
                <button type="submit" name="action" value="adduser" class="emp-menu-button">Add New Pet Parent</button>
            </form>

            <!-- Button for Employees to add a new pet  -->
            <form method="GET" action="addpet.html" style="display:inline;">
                <button type="submit" name="action" value="addpet" class="emp-menu-button">Add New Pet</button>
            </form>
          
            <!-- Button for Employees to add a new appointment, navigates to newappointment.php  -->
            <form method="GET" action="newappointment.php" style="display:inline;">
                <button type="submit" name="action" value="addappointment" class="emp-menu-button">Schedule New Appointment</button>
            </form>           
        </div>

        <!-- PHP Container for Reviews - viewing the recent reviews -->
        <div id="reviews-container">
            <?php
            if (isset($_GET['action']) && $_GET['action'] === 'reviews') {
                // Database credentials
                $host = 'localhost';
                $dbname = 'petservices_db';
                $user = 'root';
                $pass = '';

                try {
                    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Query 1: Average Rating from the last 3 months
                    $avgSql = "SELECT AVG(Rating) as AvgRating FROM review WHERE Date >= DATE_SUB(CURRENT_DATE(), INTERVAL 3 MONTH)";
                    $avgStmt = $pdo->query($avgSql);
                    $avgResult = $avgStmt->fetch(PDO::FETCH_ASSOC);
                    $averageRating = $avgResult['AvgRating'] ? number_format((float)$avgResult['AvgRating'], 1, '.', '') : 'N/A';

                    echo "<h2>Review Dashboard</h2>";
                    echo "<h3>Average Rating Over Past 3 Months: <strong>{$averageRating} / 5</strong></h3>";

                    // Query 2: Display reviews from the past 3 months
                    $sql = "SELECT Rating, Date, Body FROM review WHERE Date >= DATE_SUB(CURRENT_DATE(), INTERVAL 3 MONTH) ORDER BY Date DESC";
                    $stmt = $pdo->query($sql);
                    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($reviews) {
                        echo "<h3>Reviews From Past 3 Months:</h3><ul>";
                        foreach ($reviews as $row) {
                            $formattedDate = date("F j, Y", strtotime($row['Date']));
                            echo "<li><strong>Rating: {$row['Rating']}/5</strong> - {$formattedDate}<br>{$row['Body']}</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>No reviews found from the past month.</p>";
                    }
                } catch (PDOException $e) {
                    echo "<p>Error loading reviews: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
            }
            ?>
        </div>
</body>
<footer>
</footer>
</html>