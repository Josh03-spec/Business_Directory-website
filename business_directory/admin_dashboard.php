<!-- filepath: c:\WAMP_SERVER\www\business_directory\admin_dashboard.php -->
<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uganda Connect - Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to the common CSS file -->
</head>
<body>
    <header>
        <h1>Uganda Connect</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="categories.php">Categories</a>
            <a href="businesses.php">Businesses</a>
            <a href="add_business.php">Add Business</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <h2>Admin Dashboard</h2>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <ul>
            <li><a href="manage_categories.php">Manage Categories</a></li>
            <li><a href="manage_businesses.php">Manage Businesses</a></li>
            <li><a href="manage_reviews.php">Manage Reviews</a></li>
        </ul>
    </main>
    <footer>
        <p>&copy; 2025 Uganda Connect</p>
    </footer>
</body>
</html>