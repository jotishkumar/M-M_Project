<?php
session_start(); // Start a session to store user data (e.g., login status)

// Check if the user is already logged in, redirect to a dashboard if necessary
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}

// Check if the login form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Replace these values with your actual database credentials
    $db_server = "localhost";
    $db_username = "admin"; // Your MySQL username
    $db_password = "admin";     // Your MySQL password (if any)
    $db_name = "Web App";  // Your database name

    // Create a database connection
    $conn = new mysqli($db_server, $db_username, $db_password, $db_name);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get input from the form
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Query to check if the provided username and password match a record in the database
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Login successful
        $_SESSION['username'] = $username; // Store the username in the session
        header("Location: dashboard.php"); // Redirect to the dashboard page
        exit;
    } else {
        // Login failed
        $error_message = "Invalid username or password. Please try again.";
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
</head>
<body>
    <h2>Login</h2>
    <?php
    if (isset($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" value="Login">
    </form>
</body>
</html>
