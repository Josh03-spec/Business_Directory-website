<!-- filepath: c:\WAMP_SERVER\www\Business_Directory-website\business_directory\confirm_delete_business.php -->
<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$business_id = isset($_GET['business_id']) ? intval($_GET['business_id']) : 0;
if ($business_id == 0) {
    header("Location: manage_businesses.php");
    exit;
}

$stmt = $conn->prepare("SELECT name FROM businesses WHERE business_id = :business_id");
$stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
$stmt->execute();
$business = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$business) {
    header("Location: manage_businesses.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
    $stmt = $conn->prepare("DELETE FROM businesses WHERE business_id = :business_id");
    $stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
    $stmt->execute();
    $message = "Business successfully deleted.";
    header("Location: manage_businesses.php?message=" . urlencode($message));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Delete Business - Uganda Connect</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Uganda Connect</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="categories.php">Categories</a>
            <a href="businesses.php">Businesses</a>
            <a href="add_business.php">Add Business</a>
            <a href="admin_dashboard.php">Admin Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <h2>Confirm Delete Business</h2>
        <p>Are you sure you want to delete the business "<?php echo htmlspecialchars($business['name']); ?>"?</p>
        <form action="confirm_delete_business.php?business_id=<?php echo $business_id; ?>" method="POST">
            <button type="submit" name="confirm_delete">Yes, Delete</button>
            <a href="manage_businesses.php">Cancel</a>
        </form>
    </main>
    <footer>
        <p>&copy; 2025 Uganda Connect</p>
    </footer>
</body>
</html>