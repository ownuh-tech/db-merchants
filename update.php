<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "myshop";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['phone']) && isset($_POST['address']) && isset($_POST['collection']) && isset($_POST['balance'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $collection = $_POST['collection'];
        $balance = floatval($_POST['balance']);

        // Convert collection date to the correct format for database insertion
        $collectionDate = date("Y-m-d", strtotime($collection));

        // Create a connection
        $connection = new mysqli($servername, $username, $password, $database);

        // Check connection
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        // Update client record using SQL query
        $sql = "UPDATE clients SET name = '$name', email = '$email', phone = '$phone', address = '$address', collection = '$collectionDate', balance = $balance WHERE id = $id";

        if ($connection->query($sql) === true) {
            // Update successful
            echo "Client record updated successfully! Redirecting back to the home page...";
            header("refresh:3;url=/myshop/index.php");
            exit();
        } else {
            // Update failed
            echo "Error updating client record: " . $connection->error;
        }

        $connection->close();
    } else {
        echo "Please provide all required fields.";
    }
} else {
    // Redirect back to the index.php page if the request method is not POST
    header("Location: /myshop/index.php");
    exit();
}
?>
