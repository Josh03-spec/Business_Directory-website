<!-- filepath: c:\WAMP_SERVER\www\business_directory\businesses.php -->
<?php include 'db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uganda Connect - Businesses</title>
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
        <h2>Businesses</h2>
        <?php
        if (isset($_GET['category_id'])) {
            $category_id = $_GET['category_id'];
            $stmt = $conn->prepare("SELECT business_id, name, description FROM businesses WHERE category_id = :category_id AND is_approved = TRUE");
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->execute();
            echo "<h3>Category: " . htmlspecialchars($category_id) . "</h3>";
        } else {
            $stmt = $conn->query("SELECT business_id, name, description FROM businesses WHERE is_approved = TRUE");
        }

        if ($stmt->rowCount() > 0) {
            echo "<ul>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<li><a href='business_detail.php?business_id=" . htmlspecialchars($row['business_id']) . "'><strong>" . htmlspecialchars($row['name']) . "</strong></a><br>" . htmlspecialchars(substr($row['description'], 0, 100)) . "...</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No businesses found.</p>";
        }
        ?>
    </main>
    <footer>
        <p>&copy; 2025 Uganda Connect</p>
    </footer>
</body>
</html>