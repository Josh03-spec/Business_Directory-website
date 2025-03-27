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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        /* Global Styles */
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
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        header h1 {
            margin: 0 0 1rem 0;
            font-size: 2rem;
            width: 100%;
        }
        header nav {
            display: flex;
            justify-content: center;
            gap: 1rem;
            width: 100%;
            flex-wrap: wrap;
        }
        header nav a {
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 1.1rem;
        }
        header nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-0.125rem);
        }
        header nav a[href="admin_dashboard.php"]:hover {
            background-color: #28a745; /* Green on hover */
        }
        header nav a[href="logout.php"]:hover {
            background-color: #dc3545; /* Red on hover */
        }
        main {
            padding: 2rem;
            max-width: 800px;
            margin: 2rem auto;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            text-align: center;
        }
        main h2 {
            font-size: 2.25rem;
            color: #2d3748;
            margin-bottom: 2rem;
        }
        main p {
            font-size: 1.2rem;
            color: #4a5568;
            margin-bottom: 2rem;
        }
        form {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        button[type="submit"],
        a {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            font-size: 1.1rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            white-space: nowrap;
            text-decoration: none;
        }
        button[type="submit"] {
            background-color: #dc3545;
            color: white;
        }
        button[type="submit"]:hover {
            background-color: #c82333;
            transform: translateY(-0.125rem);
        }
        a {
            background-color: #007BFF;
            color: #333;
        }
        a:hover {
            background-color: #0056b3;
            transform: translateY(-0.125rem);
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
