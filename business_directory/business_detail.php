<!-- filepath: c:\WAMP_SERVER\www\business_directory\business_detail.php -->
<?php
include 'db_connection.php';

if (!isset($_GET['business_id'])) {
    die("Business ID not provided.");
}

$business_id = $_GET['business_id'];
$stmt = $conn->prepare("SELECT b.name, b.description, b.contact_phone, b.address, b.website, c.category_name, u.username FROM businesses b LEFT JOIN categories c ON b.category_id = c.category_id LEFT JOIN users u ON b.user_id = u.user_id WHERE b.business_id = :business_id AND b.is_approved = TRUE");
$stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    die("Business not found or not approved.");
}

$business = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($business['name'] ?? ''); ?> - Uganda Connect</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file here -->
</head>
<body>
    <header>
        <h1>Uganda Connect</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="categories.php">Categories</a>
            <a href="businesses.php">Businesses</a>
            <a href="add_business.php">Add Business</a>
        </nav>
    </header>
    <main>
        <h2><?php echo htmlspecialchars($business['name'] ?? ''); ?></h2>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($business['category_name'] ?? ''); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($business['description'] ?? ''); ?></p>
        <p><strong>Contact:</strong> <?php echo htmlspecialchars($business['contact_phone'] ?? ''); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($business['address'] ?? ''); ?></p>
        <p><strong>Website:</strong> <a href="<?php echo htmlspecialchars($business['website'] ?? ''); ?>" target="_blank"><?php echo htmlspecialchars($business['website'] ?? ''); ?></a></p>
        <p><strong>Submitted By:</strong> <?php echo htmlspecialchars($business['username'] ?? ''); ?></p>
    </main>
    <footer>
        <p>&copy; 2025 Uganda Connect</p>
    </footer>
</body>
</html>