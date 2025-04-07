<?php
session_start();
include 'db_connection.php'; // Make sure this path is correct

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
            $_SESSION['admin_logged_in'] = ($user['role'] === 'admin');  // Set admin session

            // Store the last activity time
            $_SESSION['last_activity'] = time();

            // Regenerate session ID for security
            session_regenerate_id(true);

            // Normalize role value to lowercase
            $role = strtolower($user['role']);

            // Debugging: Check the role value
            error_log("User role: " . $role);

            // Redirect based on user role
            switch ($role) {
                case 'admin':
                    error_log("Redirecting to admin_dashboard.php");
                    header("Location: admin_dashboard.php");
                    break;
                case 'editor':
                    error_log("Redirecting to manage_categories.php");
                    header("Location: manage_categories.php");
                    break;
                case 'business':
                    error_log("Redirecting to manage_businesses.php");
                    header("Location: manage_businesses.php");
                    break;
                default:
                    error_log("Redirecting to index.php");
                    header("Location: index.php");
                    break;
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
    }
} else {
    // If the user tries to access this page directly (not via POST)
    error_log("Direct access to process_login.php without POST request");
    header("Location: login.php");
    exit;
}
?>
