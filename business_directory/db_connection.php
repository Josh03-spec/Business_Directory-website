<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "uganda_connect"; // Use your database name

try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Important security setting
} catch (PDOException $e) {
    // Log the error (optional)
    error_log("Database connection error: " . $e->getMessage());

    // Send a generic error message to the client
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed. Please try again later.']);
    exit; // Stop further execution
}
?>