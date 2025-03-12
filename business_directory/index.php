<!-- filepath: c:\WAMP_SERVER\www\business_directory\index.php -->
<?php include 'db_connection.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nkozi Online - Home</title>
    <link rel="stylesheet" href="home_styles.css"> <!-- Link to the new CSS file -->
</head>
<body>
    <header>
        <h1>Welcome to Nkozi Online</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="categories.php">Categories</a>
            <a href="businesses.php">Businesses</a>
            <a href="add_business.php">Add Business</a>
        </nav>
    </header>
    <main>
        <form action="search.php" method="GET">
            <input type="text" name="query" placeholder="Search for businesses...">
            <button type="submit">Search</button>
        </form>
        <h2>Filter by Category</h2>
        <form action="search.php" method="GET">
            <select name="category_id">
                <option value="">All Categories</option>
                <?php
                $stmt = $conn->query("SELECT * FROM categories");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row['category_id'] . "'>" . $row['category_name'] . "</option>";
                }
                ?>
            </select>
            <button type="submit">Filter</button>
        </form>
        <h2>Categories</h2>
        <ul>
            <?php
            $stmt = $conn->query("SELECT * FROM categories");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<li><a href='businesses.php?category_id=" . $row['category_id'] . "'>" . $row['category_name'] . "</a></li>";
            }
            ?>
        </ul>
        <h2><a href="./login.php">Admin/Staff Login</a></h2>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online</p>
    </footer>
</body>
</html>