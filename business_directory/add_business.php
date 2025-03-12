<!-- filepath: c:\WAMP_SERVER\www\business_directory\add_business.php -->
<?php include 'db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uganda Connect - Add Business</title>
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
        <h2>Add Business</h2>
        <form action="submit_business.php" method="POST">
            <label for="category_id">Category:</label>
            <select name="category_id" id="category_id" required>
                <?php
                $stmt = $conn->query("SELECT * FROM categories");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row['category_id'] . "'>" . $row['category_name'] . "</option>";
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
    </main>
    <footer>
        <p>&copy; 2025 Uganda Connect</p>
    </footer>
</body>
</html>