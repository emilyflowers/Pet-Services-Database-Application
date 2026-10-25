<?php
$host = "localhost";
$dbname = "petservices_db";
$user = "root";
$password = "";

try {
    // Create a PDO database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    
    // Set PDO error mode to exception for easier debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the form was submitted using POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Retrieve and sanitize input data
        $petName    = trim($_POST['pet-name'] ?? '');
        $clientName = trim($_POST['client-name'] ?? '');
        $species    = trim($_POST['species'] ?? '');
        $dob        = trim($_POST['dob'] ?? '');
        
        // Checkboxes only send data if checked. If checked, set to 1, otherwise 0.
        $isFixed    = isset($_POST['is-fixed']) ? 1 : 0;
        $isVaccinated = isset($_POST['is-vaccinated']) ? 1 : 0;

        // Simple validation to ensure required fields aren't empty
        if (!empty($petName) && !empty($clientName) && !empty($species) && !empty($dob)) {
            
            // Prepare the SQL INSERT statement including the new column
            $sql = "INSERT INTO pet (name, oid, species, birthdate, isfixed, isVaccinated) 
                    VALUES (:pet_name, :client_name, :species, :dob, :is_fixed, :is_vaccinated)";
            
            $stmt = $pdo->prepare($sql);

            // Execute the query with the form values
            $stmt->execute([
                ':pet_name'    => $petName,
                ':client_name' => $clientName,
                ':species'     => $species,
                ':dob'         => $dob,
                ':is_fixed'    => $isFixed, 
                ':is_vaccinated' => $isVaccinated
            ]);

            echo "<h2>Success!</h2>";
            echo "<p>New pet <strong>" . htmlspecialchars($petName) . "</strong> has been added successfully.</p>";
            echo '<p><a href="index.html">Add another pet</a></p>';

        } else {
            echo "<p>Error: All required fields must be filled out. Please go back and try again.</p>";
        }
    }
} catch (PDOException $e) {
    // Handle database connection or query errors
    echo "Database error: " . htmlspecialchars($e->getMessage());
}
?>
