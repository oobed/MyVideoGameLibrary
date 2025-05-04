<?php
session_start(); // Start session for tracking user login

// Set up database connection parameters
$servername = "localhost"; // MySQL server
$username = "root"; // MySQL username (changed to root)
$password = ""; // MySQL password (typically empty for default XAMPP)
$dbname = "oobed_videogames"; // Database name

// Create connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);
// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for error and success messages
$error_message = "";
$success_message = "";

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $user_password = trim($_POST["password"]);

    // Fetch user by email
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If the user exists, get their information
        $user = $result->fetch_assoc();

        // Check if the password matches
        if (password_verify($user_password, $user["password"])) {
            // Successful login
            $_SESSION["user_id"] = $user["user_id"]; // Store the user's ID in the session
            $_SESSION["first_name"] = $user["first_name"]; // Optionally, store the user's name

            // Redirect to the personal library page
            header("Location: personal_library.php");
            exit; // Ensure the script stops executing after redirection
        } else {
            // Incorrect password
            $error_message = "Invalid email or password. Please try again.";
        }
    } else {
        // No user found with the given email
        $error_message = "Invalid email or password. Please try again.";
    }
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: lightgray;
            font-family: Arial, sans-serif;
        }

        .container {
            margin-top: 100px;
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
        <div class="row justify-content-center">
            <div class="col-6">
                <h2>Login</h2>

                <!-- Display error or success messages -->
                <?php if ($error_message): ?>
                    <div class="alert alert-danger">
                        <?= $error_message ?>
                    </div>
                <?php elseif ($success_message): ?>
                    <div class="alert alert-success">
                        <?= $success_message ?>
                    </div>
                <?php endif; ?>

                <form action="" method="post"> <!-- Form submits to the same page -->
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="text" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="remember-me">
                        <label class="form-check-label" for="remember-me">Remember Me</label>
                    </div>
                    <div class="form-group row">
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                        <div class="col-6 text-right">
                            <span class="forgot-password">Forgot Password?</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        // JavaScript for form validation and login handling
        const EMAIL_REGEX = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        document.querySelector("#login-form").onsubmit = function (event) {
            event.preventDefault(); // Prevent form from submitting

            let valid_form = true; // Default to true unless an error occurs
            const email = document.querySelector("#email").value.trim();
            const password = document.querySelector("#password").value.trim();

            // Validate email
            if (email.length === 0) {
                document.querySelector("#email-error").innerHTML = "Email cannot be empty.";
                valid_form = false;
            } else if (!EMAIL_REGEX.test(email)) {
                document.querySelector("#email-error").innerHTML = "Invalid email address.";
                valid_form = false;
            } else {
                document.querySelector("#email-error").innerHTML = ""; // Clear error message if valid
            }

            // Validate password
            if (password.length === 0) {
                document.querySelector("#password-error").innerHTML = "Password cannot be empty.";
                valid_form = false;
            } else {
                document.querySelector("#password-error").innerHTML = ""; // Clear error message if valid
            }

            if (valid_form) {
                console.log("Login successful"); // Placeholder for actual login logic
                // Process the login (e.g., send a request to a backend server)
            }
        };

        document.querySelector(".forgot-password").onclick = function () {
            alert("Redirect to password recovery page"); // Placeholder for actual redirection logic
        };
    </script>
</body>
</html>
