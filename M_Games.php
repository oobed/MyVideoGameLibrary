<?php
session_start(); // Start the session to access user information

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

// Database connection details
$servername = "localhost"; // MySQL server
$username = "root"; // Changed from "oobed" to "root"
$password = ""; // Changed from "dbforassignmentm3" to empty (default XAMPP)
$dbname = "oobed_videogames"; // Database name

// Create the database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Fetch games with a rating of "M" for the specified user
$sql = "SELECT title, genre, release_date, platform, completed, image_link FROM owned_games WHERE user_id = ? AND rating = 'M'";
$stmt = $conn->prepare($sql); // Fixed: changed dot to arrow, added $ to sql
$stmt->bind_param("i", $user_id); // Fixed: changed dot to arrow, added $ to user_id
$stmt->execute(); // Fixed: added $ to stmt, changed dot to arrow
$result = $stmt->get_result(); // Fixed: changed dot to arrow

// Collect the games into an array for display
$games = [];
while ($row = $stmt->fetch_assoc()) { // Fixed: changed dot to arrow, added $ to stmt
    $games[] = $row; // Fixed: added $ to row
}

// Close the database connection
$conn->close(); // Fixed: added $ to conn, changed dot to arrow
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User's M-Rated Games</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: lightgray;
            padding: 20px;
        }

        h2 {
            font-family: "Garamond", serif;
            color: blueviolet;
            text-align: center;
            margin-bottom: 20px;
        }

        .game-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            margin: 10px;
            padding: 15px;
            text-align: left;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
    </style>
</head>
<body>
    <h2>Your M-Rated Games</h2>

    <div class="card-container">
        <?php if (empty($games)): ?>
            <p>No M-rated games found in your library.</p>
        <?php else: ?>
            <?php foreach ($games as $game): ?>
                <div class="game-card">
                    <h3><?= htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p>Genre: <?= htmlspecialchars($game['genre'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p>Platform: <?= htmlspecialchars($game['platform'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p>Release Date: <?= htmlspecialchars($game['release_date'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p>Completed: <?= $game['completed'] ? "Yes" : "No" ?></p>
                    <?php if (!empty($game['image_link'])): ?>
                        <img src="<?= htmlspecialchars($game['image_link'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>