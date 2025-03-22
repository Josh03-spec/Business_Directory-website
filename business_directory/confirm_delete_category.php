<!-- filepath: c:\WAMP_SERVER\www\Business_Directory-website\business_directory\confirm_delete_category.php -->
<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
if ($category_id == 0) {
    header("Location: manage_categories.php");
    exit;
}

$stmt = $conn->prepare("SELECT category_name FROM categories WHERE category_id = :category_id");
$stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header("Location: manage_categories.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
    $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = :category_id");
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: manage_categories.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Delete Category - Uganda Connect</title>
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
        <h2>Confirm Delete Category</h2>
        <p>Are you sure you want to delete the category "<?php echo htmlspecialchars($category['category_name']); ?>"?</p>
        <form action="confirm_delete_category.php?category_id=<?php echo $category_id; ?>" method="POST">
            <button type="submit" name="confirm_delete">Yes, Delete</button>
            <a href="manage_categories.php">Cancel</a>
        </form>
    </main>
    <footer>
        <p>&copy; 2025 Uganda Connect</p>
    </footer>
</body>
</html>