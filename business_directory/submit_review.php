<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Initialize variables
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $business_id = filter_input(INPUT_POST, 'business_id', FILTER_VALIDATE_INT);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 5]
    ]);
    $review_text = trim($_POST['review_text'] ?? '');

    // Validate inputs
    if (!$business_id || !$rating) {
        $error = "Invalid input data. Please try again.";
    } elseif (empty($review_text)) {
        $error = "Review text cannot be empty.";
    } else {
        try {
            // Insert review into database
            $stmt = $conn->prepare("
                INSERT INTO reviews 
                (business_id, user_id, rating, review_text)
                VALUES (:business_id, :user_id, :rating, :review_text)
            ");
            
            $stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
            $stmt->bindParam(':review_text', $review_text);
            
            if ($stmt->execute()) {
                // Update business average rating
                update_business_rating($conn, $business_id);
                
                $success = "Review submitted successfully!";
            } else {
                $error = "Error submitting review. Please try again.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Redirect back to business page with status message
$redirect_url = "business_detail.php?business_id=" . urlencode($business_id ?? '');
if (!empty($error)) {
    $redirect_url .= "&error=" . urlencode($error);
} elseif (!empty($success)) {
    $redirect_url .= "&success=" . urlencode($success);
}
header("Location: $redirect_url");
exit;

function update_business_rating($conn, $business_id) {
    // Calculate new average rating
    $stmt = $conn->prepare("
        UPDATE businesses b
        SET avg_rating = (
            SELECT ROUND(AVG(rating), 2)
            FROM reviews 
            WHERE business_id = :business_id
        )
        WHERE business_id = :business_id
    ");
    $stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
    $stmt->execute();
}
?>