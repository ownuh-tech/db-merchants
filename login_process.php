<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "myshop";

// Create a connection
$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Process the user login form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // User found, verify password
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["password"])) {
            // Password is correct, create session
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"]; // Add role field to the users table

            // Redirect to dashboard or any protected page
            header("Location:/myshop/index.php");
            exit();
        } else {
            // Incorrect password
            echo "Invalid credentials";
        }
    } else {
        // User not found
        echo "Invalid credentials";
    }

    $stmt->close();
}

$connection->close();
?>
