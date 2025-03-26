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
    <title>Nkozi Online - Admin Dashboard</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
            margin-top: 1rem;
            display: flex;
            justify-content: center;
        }
        header nav a {
            color: white;
            text-decoration: none;
            margin: 0 1rem;
            font-size: 1.1rem;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        header nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        main {
            padding: 2rem;
            flex: 1;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        main h2 {
            color: #007BFF;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            order: 1;
            align-self: flex-start;
            text-align: left;
        }
        main p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            order: 2;
            align-self: flex-start;
            text-align: left;
        }
        main ul {
            list-style: none;
            padding: 0;
            margin-bottom: 2rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
            order: 3;
        }
        main ul li a {
            background-color: #e9ecef;
            color: #333;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1.1rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            width: 200px;
            text-align: center;
            display: block;
        }
        main ul li a:hover {
            background-color: #ced4da;
            transform: translateY(-2px);
        }
        main .admin-links {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            margin-top: 2rem;
            width: 100%;
            max-width: 400px;
            order: 4;
        }
        main .admin-links a {
            background-color: #28a745;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1.1rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            width: 100%;
            text-align: center;
            display: block;
        }
        main .admin-links a:hover {
            background-color: #218838;
            transform: translateY(-2px);
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
            <li><a href="manage_users.php">Manage Users</a></li>
        </ul>
        <div class="admin-links">
            <a href="add_business.php">Add Business</a>
            <a href="approve_businesses.php">Approve Businesses</a>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online</p>
    </footer>
</body>
</html>
