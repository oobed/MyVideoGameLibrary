<?php
session_start(); // Start the session to access user information

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

// Database connection details
$servername = "localhost"; // MySQL server
$username = "oobed"; // Your MySQL username
$password = "dbforassignmentm3"; // MySQL password
$dbname = "oobed_videogames"; // Database name

// Create the database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Variables for feedback messages
$delete_message = "";
$insert_message = "";

// Handle the delete operation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete']) && isset($_POST['game_id'])) {
    $game_id = intval($_POST['game_id']); // Ensure the ID is an integer

    // Delete the game with the specified ID for the current user
    $sql = "DELETE FROM owned_games WHERE game_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $game_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $delete_message = "Game deleted successfully.";
    } else {
        $delete_message = "Error: Could not delete the game.";
    }
}

// Handle the insert operation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add'])) {
    // Extract values from the form
    $title = trim($_POST['title']);
    $genre = trim($_POST['genre']);
    $release_date = trim($_POST['release_date']);
    $platform = trim($_POST['platform']);
    $completed = isset($_POST['completed']) ? 1 : 0; // Checkbox for completion status
    $rating = trim($_POST['rating']);
    $image_link = trim($_POST['image_link']);

    // Insert a new game into the database for the logged-in user
    if (empty($title) || empty($genre) || empty($release_date) || empty($platform) || empty($rating)) {
        $insert_message = "Error: Please fill in all required fields.";
    } else {
        $sql = "INSERT INTO owned_games (user_id, title, genre, release_date, platform, completed, rating, image_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssiss", $_SESSION['user_id'], $title, genre, release_date, platform, completed, rating, image_link);

        if ($stmt->execute()) {
            $insert_message = "Game added successfully.";
        } else {
            $insert_message = "Error: Could not add the game.";
        }
    }
}

// Fetch the games for the specific user
$sql = "SELECT * FROM owned_games WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']); // Bind the user ID
$stmt->execute();
$result = $stmt->get_result();

// Collect the games into an array for display
$games = [];
while ($row = $result->fetch_assoc()) {
    $games[] = $row;
}

// Close the database connection
$conn.close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Personal Library</title>
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

        .delete-button {
            color: white;
            background-color: red;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h2>Personal Library</h2>

    <!-- Feedback messages for insert and delete operations -->
    <?php if ($delete_message || $insert_message): ?>
        <div class="<?= strpos($delete_message, 'successful') !== false ? 'alert alert-success' : 'alert alert-danger' ?>">
            <?= htmlspecialchars($delete_message ?: $insert_message, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <!-- Display the game cards -->
    <div class="card-container">
        <?php if (empty($games)): ?>
            <p>No games found in your library.</p>
        <?php else: ?>
            <?php foreach ($games as $game): ?>
                <div class="game-card">
                    <h3><?= htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p>Genre: <?= htmlspecialchars($game['genre'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p>Platform: <?= htmlspecialchars($game['platform'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p>Release Date: <?= htmlspecialchars($game['release_date'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p>Completed: <?= $game['completed'] ? "Yes" : "No" ?></p>
                    <p>Rating: <?= htmlspecialchars($game['rating'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php if (!empty($game['image_link'])): ?>
                        <img src="<?= htmlspecialchars($game['image_link'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>

                    <!-- Delete form -->
                    <form action="" method="post" onsubmit="return confirm('Are you sure you want to delete this game?');">
                        <input type="hidden" name="game_id" value="<?= $game['game_id'] ?>">
                        <button type="submit" name="delete" class="delete-button">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Form to add new games to the library -->
    <h3>Add a New Game to Your Library</h3>
    <form action="" method="post">
        <div class="form-group">
            <label for="title">Game Title:</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="genre">Genre:</label>
            <select name="genre" required>
                <option value="">Select a genre</option>
                <option value="Action">Action</option>
                <option value="Adventure">Adventure</option>
                option value="RPG">RPG</option>
                option value="Sports">Sports</option>
                option value="Platformer">Platformer</option>
                option value="Strategy">Strategy</option>
            </select>
        </div>
        <div class="form-group">
            <label for="release_date">Release Date:</label>
            <input type="date" name="release_date" required>
        </div>
        <div class="form-group">
            <label for="platform">Platform:</label>
            <input type="text" name="platform" required>
        </div>
        <div class="form-group form-check">
            <input type="checkbox" name="completed">
            <label for="completed">Completed</label>
        </div>
        <div class="form-group">
            <label for="rating">Game Rating:</label>
            <select name="rating" required>
                <option value="E">E</option>
                option value="E10+">E10+</option>
                option value="T">T</option>
                option value="M">M</option>
            </select>
        </div>
        <div class="form-group">
            <label for="image_link">Image Link:</label>
            <input type="url" name="image_link" required>
        </div>
        <div class="form-group">
            <button type="submit" name="add" class="btn btn-primary">Add Game</button>
        </div>
    </form>
</body>
</html>
