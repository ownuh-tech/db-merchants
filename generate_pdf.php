<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "myshop";

// Create a connection
$connection = new mysqli($servername, $username, $password, $database);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Fetch all client records
$sql = "SELECT * FROM clients WHERE balance <> 0 ORDER BY address,collection ASC";

$result = $connection->query($sql);

if (!$result) {
    die("Invalid query: " . $connection->error);
}

// Close the database connection
$connection->close();

// Include the TCPDF library
require_once 'tcpdf/tcpdf.php';

// Create a new PDF document with landscape orientation
$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set the document properties
$pdf->SetCreator('My Shop');
$pdf->SetTitle('Client Data');

// Add a new page to the PDF
$pdf->AddPage();

// Set font settings for the heading
$pdf->SetFont('helvetica', 'B', 16);

// Add the heading to the PDF
$pdf->Cell(0, 15, 'DB MERCHANTS CLIENT DATA REPORT', 0, 1, 'C');

// Set font settings for the date and time
$pdf->SetFont('helvetica', '', 12);

// Get the current date and time
$dateTime = date('Y-m-d H:i:s');

// Add the date and time to the PDF
$pdf->Cell(0, 10, 'Date and Time: ' . $dateTime, 0, 1, 'R');



// Set font settings
$pdf->SetFont('helvetica', '', 11);

// Define table column headings
$columnHeaders = array('Name', 'Phone', 'Address', 'Collection', 'Balance','Paid today');

// Set the cell width for each column
$columnWidth = 50;

// Set the cell height
$rowHeight = 10;

// Set the starting point for the table
$y = 40;

// Set the left margin for the table
$leftMargin = 0.5;
$pdf->SetLeftMargin($leftMargin);

// Add the table headers
foreach ($columnHeaders as $header) {
    $pdf->SetXY($leftMargin, $y);
    $pdf->Cell($columnWidth, $rowHeight, $header, 1, 0, 'C');
    $leftMargin += $columnWidth;
}

// Reset the left margin for data rows
$leftMargin = 0.5;

// Add the table rows
while ($row = $result->fetch_assoc()) {
    $y += $rowHeight;
    $pdf->SetXY($leftMargin, $y);
    $pdf->Cell($columnWidth, $rowHeight, $row['name'], 1, 0, 'C');
    $pdf->Cell($columnWidth, $rowHeight, $row['phone'], 1, 0, 'C');
    $pdf->Cell($columnWidth, $rowHeight, $row['address'], 1, 0, 'C');
    $pdf->Cell($columnWidth, $rowHeight, $row['collection'], 1, 0, 'C');
    $pdf->Cell($columnWidth, $rowHeight, $row['balance'], 1, 0, 'C');
    $pdf->Cell($columnWidth, $rowHeight, '', 1, 0, 'C');
    
}

// Output the PDF to the browser
$pdf->Output('Client_Data.pdf', 'D');
