<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Get the category ID from the request
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
if ($category_id == 0) {
    header("Location: manage_categories.php");
    exit;
}

// Fetch the category details
$stmt = $conn->prepare("SELECT category_name FROM categories WHERE category_id = :category_id");
$stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header("Location: manage_categories.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_category_name = trim($_POST['category_name']);
    if (!empty($new_category_name)) {
        $update_stmt = $conn->prepare("UPDATE categories SET category_name = :category_name WHERE category_id = :category_id");
        $update_stmt->bindParam(':category_name', $new_category_name);
        $update_stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $update_stmt->execute();
        header("Location: manage_categories.php?message=Category renamed successfully");
        exit;
    } else {
        $error_message = "Category name cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Rename Category - Nkozi Online</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        /* Inherit styles from manage_categories.php */
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
            color: #333;
            line-height: 1.6;
        }
        header {
            background-color: #007BFF;
            color: white;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 0;
        }
        header h1 {
            margin: 0;
            font-size: 2rem;
        }
        header nav {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }
        header nav a {
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        header nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-0.125rem);
        }
        main {
            padding: 2rem;
            max-width: 600px;
            margin: 2rem auto;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        main h2 {
            font-size: 2rem;
            color: #2d3748;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        form input[type="text"] {
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            font-size: 1rem;
            width: 100%;
        }
        form input[type="text"]:focus {
            outline: none;
            border-color: #007BFF;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        form button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.375rem;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        form button[type="submit"] {
            background-color: #007BFF;
            color: white;
        }
        form button[type="submit"]:hover {
            background-color: #0056b3;
            transform: translateY(-0.125rem);
        }
        form button[type="button"] {
            background-color: #6c757d;
            color: white;
        }
        form button[type="button"]:hover {
            background-color: #5a6268;
            transform: translateY(-0.125rem);
        }
        .error-message {
            color: #dc3545;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        footer {
            margin-top: 2rem;
            text-align: center;
            color: #888;
            font-size: 0.9rem;
            padding: 1rem;
            background-color: #f0f4f8;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <header>
        <h1>Nkozi Online</h1>
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
        <h2>Rename Category</h2>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form action="rename_category.php?category_id=<?php echo $category_id; ?>" method="POST">
            <label for="category_name">New Category Name:</label>
            <input type="text" name="category_name" id="category_name" value="<?php echo htmlspecialchars($category['category_name']); ?>" required>
            <div style="display: flex; justify-content: space-between;">
                <button type="submit">Rename</button>
                <button type="button" onclick="window.location.href='manage_categories.php'">Cancel</button>
            </div>
        </form>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online</p>
    </footer>
</body>
</html>