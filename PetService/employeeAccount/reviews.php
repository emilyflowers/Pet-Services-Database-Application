<?php
// Redirect if someone navigates directly to reviews.php instead of through empHome.php
if (!isset($con)) {
    header("Location: empHome.php?action=reviews");
    exit;
}

$filter = $_GET['filter'] ?? 'all';
$timeframe = $_GET['timeframe'] ?? '3_months';
$loggedInEId = intval($_SESSION['Eid']);

// Map the dropdown value to a valid SQL interval to prevent SQL injection
$allowedIntervals = [
    '12_months' => '12 MONTH',
    '6_months'  => '6 MONTH',
    '3_months'  => '3 MONTH',
    '1_month'   => '1 MONTH',
    '1_week'    => '1 WEEK'
];
$sqlInterval = $allowedIntervals[$timeframe] ?? '3 MONTH';

// Labels for the UI
$displayLabels = [
    '12_months' => 'Past 12 Months',
    '6_months'  => 'Past 6 Months',
    '3_months'  => 'Past 3 Months',
    '1_month'   => 'Past 1 Month',
    '1_week'    => 'Past 1 Week'
];
$displayLabel = $displayLabels[$timeframe] ?? 'Past 3 Months';

// Adjust SQL queries based on the active filter
if ($filter === 'mine') {
    $avgQuery = "SELECT AVG(r.Rating) AS AvgRating FROM review r JOIN appointment a ON r.AId = a.AId WHERE r.Date >= DATE_SUB(CURRENT_DATE(), INTERVAL $sqlInterval) AND a.EId = $loggedInEId";
    $revQuery = "SELECT r.AId, r.Rating, r.Date, r.Body, p.Name AS PetName, o.Name AS OwnerName, emp.Name AS EmployeeName 
                 FROM review r 
                 JOIN appointment a ON r.AId = a.AId 
                 JOIN pet p ON a.PId = p.PId
                 JOIN owner o ON p.OId = o.OId
                 JOIN employee emp ON a.EId = emp.EId
                 WHERE r.Date >= DATE_SUB(CURRENT_DATE(), INTERVAL $sqlInterval) AND a.EId = $loggedInEId 
                 ORDER BY r.Date DESC";
} else {
    $avgQuery = "SELECT AVG(Rating) AS AvgRating FROM review WHERE Date >= DATE_SUB(CURRENT_DATE(), INTERVAL $sqlInterval)";
    $revQuery = "SELECT r.AId, r.Rating, r.Date, r.Body, p.Name AS PetName, o.Name AS OwnerName, emp.Name AS EmployeeName 
                 FROM review r 
                 JOIN appointment a ON r.AId = a.AId 
                 JOIN pet p ON a.PId = p.PId
                 JOIN owner o ON p.OId = o.OId
                 JOIN employee emp ON a.EId = emp.EId
                 WHERE r.Date >= DATE_SUB(CURRENT_DATE(), INTERVAL $sqlInterval) 
                 ORDER BY r.Date DESC";
}

// 1. Fetch Average Rating
$avg = mysqli_fetch_assoc(mysqli_query($con, $avgQuery));
$averageRating = $avg['AvgRating'] ? number_format((float)$avg['AvgRating'], 1) : 'N/A';

// 2. Fetch Reviews
$reviewsQuery = mysqli_query($con, $revQuery);

$reviewsData = [];
$reviewsTextForAI = "";

// 3. Store reviews in an array and build the text block for the AI prompt
while ($r = mysqli_fetch_assoc($reviewsQuery)) {
    $reviewsData[] = $r;
    if (!empty($r['Body'])) {
        $reviewsTextForAI .= "- Rating: " . $r['Rating'] . "/5, Feedback: " . $r['Body'] . "\n";
    }
}

// 4. Generate AI Summary if reviews exist
$aiSummary = "No reviews with text available to summarize.";
if (!empty($reviewsTextForAI)) {
    // In a production environment, store this in an environment variable or secure config file.
    $apiKey = 'AQ.Ab8RN6LIcq8kR_7noT7Ir1fSEZFFkAhZbQOAV7VAEg27enjP2Q'; 
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-lite:generateContent?key=' . $apiKey;

    $prompt = "You are an assistant for a pet services business. Summarize the following recent customer reviews into 2-3 concise sentences. Highlight the overall sentiment, key strengths, and any common areas for improvement mentioned by the customers:\n\n" . $reviewsTextForAI;

    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ],
        "generationConfig" => [
            "temperature" => 0.2
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        $aiSummary = htmlspecialchars($result['candidates'][0]['content']['parts'][0]['text']);
    } else {
        $aiSummary = "AI Summary temporarily unavailable. Please try again later.";
    }
}
?>

<h2>Review Dashboard</h2>

<div class="filters" style="display: flex; gap: 20px; align-items: center; margin-bottom: 20px;">
    <div class="timeframe-container">
        <label for="timeframe">Timeframe: </label>
        <select id="timeframe" onchange="window.location.href='empHome.php?action=reviews&filter=<?php echo $filter; ?>&timeframe=' + this.value">
            <option value="12_months" <?php echo $timeframe === '12_months' ? 'selected' : ''; ?>>12 Months</option>
            <option value="6_months" <?php echo $timeframe === '6_months' ? 'selected' : ''; ?>>6 Months</option>
            <option value="3_months" <?php echo $timeframe === '3_months' ? 'selected' : ''; ?>>3 Months</option>
            <option value="1_month" <?php echo $timeframe === '1_month' ? 'selected' : ''; ?>>1 Month</option>
            <option value="1_week" <?php echo $timeframe === '1_week' ? 'selected' : ''; ?>>1 Week</option>
        </select>
    </div>
    <!-- Sliding Toggle Switch -->
    <div class="toggle-container">
        <span>Show Only My Reviews? </span>
        <label class="switch">
            <input type="checkbox" <?php echo $filter === 'mine' ? 'checked' : ''; ?> 
                   onchange="window.location.href='empHome.php?action=reviews&filter=' + (this.checked ? 'mine' : 'all')">
            <span class="slider"></span>
        </label>
    </div>
</div>

<div class="review-top-content">
    <h3>Average Rating (<?php echo $displayLabel; ?>): <strong><?php echo $averageRating; ?> / 5</strong></h3>
    
    <?php if (count($reviewsData) > 0) { ?>
        <!-- AI Summary Section -->
        <div class="ai-summary">
            <h3>Real-Time AI Summary:</h3>
            <p>
                <?php echo nl2br($aiSummary); ?>
            </p>
        </div>
</div>

        <h3>Reviews (<?php echo $displayLabel; ?>):</h3>
        <ul>
        <?php foreach ($reviewsData as $r) { ?>
            <li style="margin-bottom: 15px;">
                <strong>Rating: <?php echo $r['Rating']; ?>/5</strong> - <?php echo date("F j, Y", strtotime($r['Date'])); ?>
                (appointment #<?php echo $r['AId']; ?>)
                <?php if ($isAdmin) { ?> 
                    <a href="delete.php?type=review&id=<?php echo $r['AId']; ?>" onclick="return confirm('Delete this review?')">Delete</a>
                <?php } ?>
                <br>
                <strong>Pet:</strong> <?php echo e($r['PetName']); ?> (<?php echo e($r['OwnerName']); ?>)<br>
                <strong>Employee:</strong> <?php echo e($r['EmployeeName']); ?>
                <?php if (trim((string)$r['Body']) !== '') { ?>
                    <p>"<?php echo e($r['Body']); ?>"</p>
                <?php } ?>
            </li>
        <?php } ?>
        </ul>
    <?php } else { ?>
        <p>No reviews found for the <?php echo strtolower($displayLabel); ?>.</p>
    <?php } ?>