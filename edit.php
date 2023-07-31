<?php
// Function to get the client details for the given ID
function getClientDetails($servername, $username, $password, $database, $id) {
    $connection = new mysqli($servername, $username, $password, $database);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $sql_fetch = "SELECT * FROM clients WHERE id = ?";
    $stmt_fetch = $connection->prepare($sql_fetch);
    $stmt_fetch->bind_param("i", $id);
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result();
    $client = $result_fetch->fetch_assoc();
    $stmt_fetch->close();

    $connection->close();

    return $client;
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "myshop";

// Check if the request method is GET and ID is provided
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];
    $client = getClientDetails($servername, $username, $password, $database, $id);
} else {
    // Redirect back to the index.php page if ID is not provided or if the request method is not GET
    header("Location: /myshop/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Client</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container my-5">
        <button type="button" class="btn btn-secondary" onclick="window.location.href='/myshop/index.php'">Back</button>
        <br><br>
        <h2>Edit Client</h2>
        <form method="POST" action="/myshop/update.php">
            <div class="row">
                <div class="col-md-6">
                    <input type="hidden" name="id" value="<?php echo $client['id']; ?>">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" name="name" value="<?php echo $client['name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" name="email" value="<?php echo $client['email']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="tel" class="form-control" name="phone" value="<?php echo $client['phone']; ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input type="text" class="form-control" name="address" value="<?php echo $client['address']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="collection">Collection Date:</label>
                        <?php
                        // Convert collection date to display in the input field
                        $collectionDate = date("Y-m-d", strtotime($client['collection']));
                        ?>
                        <input type="date" class="form-control" name="collection" value="<?php echo $collectionDate; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="balance">Balance:</label>
                        <input type="number" step="50" class="form-control" name="balance" value="<?php echo $client['balance']; ?>" required>
                    </div>
                </div>
            </div>
            <div class="form-group mt-3 d-flex justify-content-end">
    <button type="submit" class="btn btn-primary mr-2">Update</button>
        <button type="button" class="btn btn-secondary mx-2" onclick="window.location.href='/myshop/index.php'">Cancel</button>
</div>

            </div>
        </form>
    </div>
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; <?php echo date("Y"); ?> Dube Merchants. All rights reserved.</p>
    </footer>
</body>
</html>
