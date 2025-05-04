<?php
session_start(); // Start session for user tracking

// Variable to hold success or error messages
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Set up database connection parameters
    // Set up database connection parameters
$servername = "localhost"; // MySQL server
$username = "root"; // MySQL username (changed to root)
$password = "Sloppiercoder1!"; // MySQL password (typically empty for default XAMPP)
$dbname = "oobed_videogames"; // Database name

// Create connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = trim($_POST['email']);
    $rawPassword = trim($_POST['password']);
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);

    // Validate that the email is unique
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Email already exists. Please use a different one.";
    } else {
        // Hash the password using a secure algorithm
        $hashedPassword = password_hash($rawPassword, PASSWORD_BCRYPT);

        // Insert the new user into the database
        $sql = "INSERT INTO users (email, first_name, last_name, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $email, $firstName, $lastName, $hashedPassword);

        if ($stmt->execute()) {
            $message = "Registration successful!";
        } else {
            $message = "Error: Registration failed. Please try again.";
        }
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign Up</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: skyblue;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
        }

        h2 {
            font-family: "Garamond", serif;
            color: blueviolet;
            text-align: center;
        }

        .error-message {
            color: red;
        }

        .success-message {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mt-5 mb-4">Sign Up</h2>
        <!-- Show the success or error message, if any -->
        <?php if ($message !== ""): ?>
            <div class="alert <?= strpos($message, "successful") !== false ? 'alert-success' : 'alert-danger' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        <!-- Form for user registration -->
        <form id="registration-form" action="" method="post">
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" name="firstName" id="firstName" class="form-control" required>
                <small id="firstName-error" class="form-text text-danger"></small>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" name="lastName" id="lastName" class="form-control" required>
                <small id="lastName-error" class="form-text text-danger"></small>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required>
                <small id="email-error" class="form-text text-danger"></small>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
                <small id="password-error" class="form-text text-danger"></small>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Sign Up</button>
            </div>
        </form>
        <div id="submission-info"></div>
    </div>
    <script>
    const NAME_REGEX = /^[A-Z][a-z]+$/;
    const EMAIL_REGEX = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    const PASSWORD_REGEX = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/;

    function isValidName(name) {
        return NAME_REGEX.test(name);
    }

    function isValidEmail(email) {
        return EMAIL_REGEX.test(email);
    }

    function isValidPassword(password) {
        return PASSWORD_REGEX.test(password);
    }

    document.getElementById("registration-form").onsubmit = function(event) {
        let valid_form = true;

        // Validate First Name
        let firstName = document.getElementById("firstName").value.trim();
        if(firstName.length === 0) {
            valid_form = false;
            document.getElementById("firstName-error").innerHTML = "First Name cannot be empty.";
        } else if(!isValidName(firstName)) {
            valid_form = false;
            document.getElementById("firstName-error").innerHTML = "First letter should be capital, followed by lowercase letters.";
        } else {
            document.getElementById("firstName-error").innerHTML = "";
        }

        // Validate Last Name
        let lastName = document.getElementById("lastName").value.trim();
        if(lastName.length === 0) {
            valid_form = false;
            document.getElementById("lastName-error").innerHTML = "Last Name cannot be empty.";
        } else if(!isValidName(lastName)) {
            valid_form = false;
            document.getElementById("lastName-error").innerHTML = "First letter should be capital, followed by lowercase letters.";
        } else {
            document.getElementById("lastName-error").innerHTML = "";
        }

        // Validate Email
        let email = document.getElementById("email").value.trim();
        if(email.length === 0) {
            valid_form = false;
            document.getElementById("email-error").innerHTML = "Email cannot be empty.";
        } else if(!isValidEmail(email)) {
            valid_form = false;
            document.getElementById("email-error").innerHTML = "Invalid email format.";
        } else {
            document.getElementById("email-error").innerHTML = "";
        }

        // Validate Password
        let password = document.getElementById("password").value.trim();
        if(password.length === 0) {
            valid_form = false;
            document.getElementById("password-error").innerHTML = "Password cannot be empty.";
        } else if(!isValidPassword(password)) {
            valid_form = false;
            document.getElementById("password-error").innerHTML = "Password must contain at least 8 characters, including uppercase, lowercase, numbers and special characters.";
        } else {
            document.getElementById("password-error").innerHTML = "";
        }

        // If validation fails, prevent form submission
        if (!valid_form) {
            event.preventDefault();
        }
    };
    </script>
</body>
</html>