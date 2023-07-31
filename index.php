<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    // Redirect unauthorized users to the login page
    header("Location:/myshop/login.php");
    exit();
}


$servername = "localhost";
$username = "root";
$password = "";
$database = "myshop";
$success_message = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if the delete button is clicked and the ID is provided
    if (isset($_POST['delete']) && isset($_POST['id'])) {
        $id = $_POST['id'];

        // Create a connection
        $connection = new mysqli($servername, $username, $password, $database);

        // Check connection
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        // Fetch client details to include in the confirmation message
        $sql_fetch = "SELECT * FROM clients WHERE id = ?";
        $stmt_fetch = $connection->prepare($sql_fetch);
        $stmt_fetch->bind_param("i", $id);
        $stmt_fetch->execute();
        $result_fetch = $stmt_fetch->get_result();
        $client = $result_fetch->fetch_assoc();
        $stmt_fetch->close();

        // Prepare your SQL DELETE statement
        $sql_delete = "DELETE FROM clients WHERE id = ?";
        $stmt_delete = $connection->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id);

        if ($stmt_delete->execute()) {
            // Deletion successful
            $success_message = "Client record '{$client['name']}' with ID {$client['id']} has been successfully deleted.";
            echo "<script>setTimeout(function(){ alert('{$success_message}'); }, 3000);</script>";
        } else {
            // Deletion failed
            echo "Error deleting client: " . $stmt_delete->error;
        }

        $stmt_delete->close();
        $connection->close();
    }
}

// Create a connection
$connection = new mysqli($servername, $username, $password, $database);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Fetch all client records
$sql = "SELECT * FROM clients";
$result = $connection->query($sql);

if (!$result) {
    die("Invalid query: " . $connection->error);
}

// Calculate the total balance of all clients
$sql_total_balance = "SELECT SUM(balance) AS total_balance FROM clients";
$result_total_balance = $connection->query($sql_total_balance);
$total_balance = $result_total_balance->fetch_assoc()['total_balance'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Shop</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

</head>
<body>
<?php include 'header.php'; ?>
<div class="container my-5">

    <!-- Dashboard content for authorized users only -->
<div class="welcome-message">
    <?php
    // Get the current hour in 24-hour format
    $currentHour = (int) date("H");

    // Determine the appropriate greeting based on the current time
    if ($currentHour >= 5 && $currentHour < 12) {
        $greeting = "Good morning";
    } elseif ($currentHour >= 12 && $currentHour < 18) {
        $greeting = "Good afternoon";
    } else {
        $greeting = "Good evening";
    }

    // Display the greeting along with the username
    echo "<span>{$greeting}, {$_SESSION["username"]}</span>";
    ?>
</div>
<br>
    <a class="btn btn-primary" href="/myshop/create.php" role="button">New Client</a>
    <button class="btn btn-primary" onclick="downloadPDF()">Download PDF</button>

<script>
    function downloadPDF() {
        // Create a new XMLHttpRequest to trigger the PDF generation
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/myshop/generate_pdf.php', true);
        xhr.responseType = 'blob';

        xhr.onload = function () {
            if (xhr.status === 200) {
                // Success: create a temporary anchor element to facilitate the download
                var blob = new Blob([xhr.response], { type: 'application/pdf' });
                var url = URL.createObjectURL(blob);
                var link = document.createElement('a');
                link.href = url;
                link.download = 'Client_Data_Report.pdf';
                link.click();
                URL.revokeObjectURL(url);
            } else {
                // Error: display an alert message
                alert('Error downloading PDF');
            }
        };

        xhr.send();
    }
</script>

    <br>
    <br>
    <h5>List of Clients</h5>
    <table id="clientTable" class="table table-bordered table-striped">
        <thead>
        
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Collection</th>
                <th>Balance</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td data-order="<?php echo strtotime($row['collection']); ?>"><?php echo $row['collection']; ?></td>
                    <td data-order="<?php echo $row['balance']; ?>"><?php echo $row['balance']; ?></td>
                    <td>
                        <a class="btn btn-primary btn-sm" href="/myshop/edit.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <!-- Add a form to handle delete confirmation -->
                        <form method="post" style="display: inline-block;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this client record?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<!-- Include the latest version of jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Use the latest version of DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#clientTable').DataTable({
            "paging": true,
            "ordering": true,
            "info": true,
        });
    });
</script>



<!-- <script>
    function downloadCSV() {
        const table = document.getElementById('clientTable');
        const rows = table.querySelectorAll('tr');

        // Extract the table headers
        const header = Array.from(rows[0].querySelectorAll('th')).map(cell => cell.innerText);

        // Extract the data rows
        const data = Array.from(rows).slice(1).map(row => {
            return Array.from(row.querySelectorAll('td')).map(cell => cell.innerText);
        });

        // Combine header and data to form the TSV content
        const tsvContent = [header.join('\t')];
        data.forEach(row => {
            tsvContent.push(row.join('\t'));
        });

        // Join the rows with newline characters
        const tsvText = tsvContent.join('\n');

        // Create a Blob with the TSV content
        const blob = new Blob([tsvText], { type: 'text/tsv;charset=utf-8;' });

        // Create a temporary anchor element to facilitate the download
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `Client_Data_${new Date().toISOString()}.tsv`;

        // Programmatically click the anchor to trigger the download
        link.click();
    }
</script> -->

</body>
<footer class="bg-dark text-white text-center py-3">
    <p>&copy; <?php echo date("Y"); ?> Dube Merchants. All rights reserved.</p>
</footer>

</html>
