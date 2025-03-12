<?php
require_once 'db_connection.php'; // Include the database connection

try {
    $stmt = $conn->query("SELECT * FROM businesses");
    $businesses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($businesses);
} catch (PDOException $e) {
    // Log the error (optional)
    error_log("Database query error: " . $e->getMessage());

    // Send a generic error message to the client
    header('Content-Type: application/json');
    echo json_encode(['error' => 'An error occurred while retrieving businesses. Please try again later.']);
}

$conn = null; // Close the database connection
?>