<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT user_id, username, password_hash, role FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Authentication successful
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Debugging: Check the role value
            error_log("User role: " . $user['role']);

            // Redirect based on user role
            switch ($user['role']) {
                case 'admin':
                    error_log("Redirecting to admin_dashboard.php");
                    header("Location: admin_dashboard.php");
                    break;
                case 'editor':
                    error_log("Redirecting to manage_categories.php");
                    header("Location: manage_categories.php"); // Or some other page
                    break;
                case 'business':
                    error_log("Redirecting to manage_businesses.php");
                    header("Location: manage_businesses.php");
                    break;
                default:
                    error_log("Redirecting to index.php");
                    header("Location: index.php"); // Or a general user page. Consider a 403.
            }
            exit;
        } else {
            // Authentication failed
            error_log("Authentication failed for username: " . $username);
            header("Location: login.php?error=Invalid username or password");
            exit;
        }
    } catch (PDOException $e) {
        // Handle database errors
        error_log("Database error: " . $e->getMessage());
        header("Location: login.php?error=An error occurred. Please try again later.");
        exit;
    }
} else {
    // If the user tries to access this page directly (not via POST)
    error_log("Direct access to process_login.php without POST request");
    header("Location: login.php");
    exit;
}
?>
