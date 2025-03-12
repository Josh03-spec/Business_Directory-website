<?php
require_once 'db_connection.php';

// Get business ID from the query string
$businessId = isset($_GET['id']) ? $_GET['id'] : null;

if ($businessId === null || !is_numeric($businessId)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid business ID.']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM businesses WHERE business_id = :id");
    $stmt->execute([':id' => $businessId]);
    $business = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($business) {
        header('Content-Type: application/json');
        echo json_encode($business);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Business not found.']);
    }
} catch (PDOException $e) {
    header('Content-Type: application/json');
    error_log("Get business error: " . $e->getMessage());
    echo json_encode(['error' => 'An error occurred while retrieving the business.']);
}

$conn = null;
?>