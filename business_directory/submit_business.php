<!-- filepath: c:\WAMP_SERVER\www\business_directory\submit_business.php -->
<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Assuming you have a way to get the current user ID

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $contact_phone = $_POST['contact_phone'];
    $address = $_POST['address'];
    $website = $_POST['website'];

    $stmt = $conn->prepare("INSERT INTO businesses (category_id, user_id, name, description, contact_phone, address, website, is_approved) VALUES (:category_id, :user_id, :name, :description, :contact_phone, :address, :website, FALSE)");
    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':contact_phone', $contact_phone);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':website', $website);

    if ($stmt->execute()) {
        $message = "Business submitted successfully. It will be reviewed for approval.";
    } else {
        $message = "Error submitting business.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Business - Uganda Connect</title>
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
        <h2>Submit Business</h2>
        <?php if ($message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
            <a href="index.php">Go to Home Page</a> | <a href="add_business.php">Submit Another Business</a>
        <?php else: ?>
            <form action="submit_business.php" method="POST">
                <label for="category_id">Category:</label>
                <select name="category_id" id="category_id" required>
                    <?php
                    $stmt = $conn->query("SELECT * FROM categories");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . htmlspecialchars($row['category_id']) . "'>" . htmlspecialchars($row['category_name']) . "</option>";
                    }
                    ?>
                </select><br>
                <label for="name">Business Name:</label>
                <input type="text" name="name" id="name" required><br>
                <label for="description">Description:</label>
                <textarea name="description" id="description" required></textarea><br>
                <label for="contact_phone">Contact Phone:</label>
                <input type="text" name="contact_phone" id="contact_phone" required><br>
                <label for="address">Address:</label>
                <input type="text" name="address" id="address" required><br>
                <label for="website">Website:</label>
                <input type="url" name="website" id="website"><br>
                <button type="submit">Submit</button>
            </form>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2025 Uganda Connect</p>
    </footer>
</body>
</html>