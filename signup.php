

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
            <div class="alert <?= strpos($message, "successful") !== false ? 'success-message' : 'error-message' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        <!-- Form for user registration -->
        <form id="registration-form" action="" method="post">
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" name="firstName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" name="lastName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Sign Up</button>
            </div>
        </form>
    </div>
    <script>
	const NAME_REGEX = /^[A-Z][a-z]+$/;
	const EMAIL_REGEX = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	const PASSWORD_REGEX = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/;
	const noPassword_REGEX = /^$/;

	function isValidName(name){
		return NAME_REGEX.test(name);
	}

	function isValidEmail(email){
		return EMAIL_REGEX.test(email);

	}
	function isValidPassword(password){
		return PASSWORD_REGEX.test(password);

	}
	function noPassword(password){
		return noPassword_REGEX.test(password);
	}


	document.querySelector("form").onsubmit = function(){
		console.log("Form has been submitted");

		let valid_form = true;

		let firstName = document.querySelector("#firstName").value.trim();
		console.log(firstName);

		if(firstName.length === 0){
			valid_form = false;
			document.querySelector("#firstName-error").innerHTML = "First Name cannot be empty.";
		}
		else if(!isValidName(firstName)){
			valid_form = false;
			document.querySelector("#firstName-error").innerHTML = "Invalid First Name.";
		}
		else{
			document.querySelector("#firstName-error").innerHTML = "";
		}


		let lastName = document.querySelector("#lastName").value.trim();
		console.log(lastName);

		if(lastName.length === 0){
			valid_form = false;
			document.querySelector("#lastName-error").innerHTML = "Last Name cannot be empty."
		}
		else if(!isValidName(lastName)){
			valid_form = false;
			document.querySelector("#lastName-error").innerHTML = "Invalid Last Name.";
		}
		else{
			document.querySelector("#lastName-error").innerHTML = "";
		}


		let email = document.querySelector("#email").value.trim();
		console.log(email);
		if(email.length === 0){
			valid_form = false;
			document.querySelector("#email-error").innerHTML = "Email cannot be empty.";
		}
		else if(!isValidEmail(email)){
			valid_form = false;
			document.querySelector("#email-error").innerHTML = "Invalid email.";
		}
		else if(!email.endsWith("usc.edu")){
			valid_form = false;
			document.querySelector("#email-error").innerHTML = "Must be usc.edu email.";
		}
		else{
			document.querySelector("#email-error").innerHTML = "";

		}

		let password = document.querySelector("#password").value.trim();
		console.log(password);
		if(password.length === 0){
			valid_form = false;
			document.querySelector("#password-error").innerHTML = "Password cannot be empty.";
		}
		else if(!isValidPassword(password)){
			valid_form = false;
			document.querySelector("#password-error").innerHTML = "Insecure password.";
		}
		else{
			document.querySelector("#password-error").innerHTML = "";
		}
		if (valid_form) {
            const fullName = document.querySelector("#firstName").value.trim() + " " + document.querySelector("#lastName").value.trim();
            const emailPrefix = document.querySelector("#email").value.trim().split("@")[0];
            const successMessage = document.querySelector("#submission-info");
            successMessage.innerHTML = `<p>Full Name: ${fullName}</p><p>Username (Email Prefix): ${emailPrefix}</p>`;
        }

		valid_form = false;
		return valid_form;


}

</script>
</body>
</html>


<?php
session_start(); // Start session for user tracking

// Set up database connection parameters
$servername = "localhost"; // MySQL server
$username = "oobed"; // MySQL username
$password = "dbforassignmentm3"; // MySQL password
$dbname = "oobed_videogames"; // Database name

// Create the database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Variable to hold success or error messages
$message = "";

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
        $stmt->bind_param("ssss", $email, firstName, lastName, hashedPassword);

        if ($stmt->execute()) {
            $message = "Registration successful!";
        } else {
            $message = "Error: Registration failed. Please try again.";
        }
    }
}

// Close the database connection
$conn->close();
?>
