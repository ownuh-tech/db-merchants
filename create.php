<?php
// Initialize variables with empty values
$name = $email = $phone = $address = $collection = $balance = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form field values using POST method
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $collection = trim($_POST["collection"]);
    $balance = floatval($_POST["balance"]);

    // Check if all required fields are filled
    if (empty($name) || empty($phone) || empty($address) || empty($collection) || empty($balance)) {
        echo "Please fill all required fields.<br>";
    } else {
        // Database connection
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "myshop";

        $connection = new mysqli($servername, $username, $password, $database);

        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        // Prepare your SQL INSERT statement (Use prepared statements to prevent SQL injection)
        $sql = "INSERT INTO clients (name, email, phone, address, collection, balance) 
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sssssd", $name, $email, $phone, $address, $collection, $balance);

        if ($stmt->execute()) {
            // Insertion successful
            echo "New client added successfully!";
            
            // Clear the form fields after successful addition
            $name = $email = $phone = $address = $collection = $balance = '';
        } else {
            // Insertion failed
            echo "Error: " . $sql . "<br>" . $connection->error;
        }

        // No need to close the $stmt object here

        $connection->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Shop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container my-5">
<button type="button" class="btn btn-secondary" onclick="window.location.href='/myshop/index.php'">Back</button>
<br><br>
    <h2>New Clients</h2>
    <form method="POST">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
            </div>
            <br>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
            </div>
            <br>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" class="form-control" name="phone" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" class="form-control" name="address" value="<?php echo isset($address) ? htmlspecialchars($address) : ''; ?>">
            </div>
            <br>
            <div class="form-group">
                <label for="collection">Collection Date:</label>
                <input type="date" class="form-control" name="collection" value="<?php echo isset($collection) ? htmlspecialchars($collection) : ''; ?>">
            </div>
            <br>
            <div class="form-group">
                <label for="balance">Balance:</label>
                <input type="number" step="0.01" class="form-control" name="balance" value="<?php echo isset($balance) ? htmlspecialchars($balance) : ''; ?>">
            </div>
        </div>
        
    </div>
    
</form>
<br>
<div class="form-group mt-3 d-flex justify-content-end">
        <button type="submit" class="btn btn-primary">Submit</button>
        <button type="button" class="btn btn-secondary mx-2" onclick="window.location.href='/myshop/index.php'">Cancel</button>
    </div>
</div>
</body>
<br><br>
<footer class="bg-dark text-white text-center py-3">
    <p>&copy; <?php echo date("Y"); ?> Dube Merchants. All rights reserved.</p>
</footer>

</html>

