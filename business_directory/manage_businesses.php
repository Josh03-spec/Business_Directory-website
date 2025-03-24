<!-- filepath: c:\WAMP_SERVER\www\Business_Directory-website\business_directory\manage_businesses.php -->
<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Pagination setup
$results_per_page = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $results_per_page;

// Search businesses
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_query = $search_term ? "WHERE name LIKE :search_term" : '';
$search_param = $search_term ? '%' . $search_term . '%' : '';

// Fetch businesses
$query = "SELECT b.*, c.category_name, u.username FROM businesses b 
          LEFT JOIN categories c ON b.category_id = c.category_id 
          LEFT JOIN users u ON b.user_id = u.user_id 
          $search_query 
          LIMIT :offset, :results_per_page";
$stmt = $conn->prepare($query);
if ($search_term) {
    $stmt->bindParam(':search_term', $search_param);
}
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':results_per_page', $results_per_page, PDO::PARAM_INT);
$stmt->execute();
$businesses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of results
$count_query = "SELECT COUNT(*) FROM businesses $search_query";
$count_stmt = $conn->prepare($count_query);
if ($search_term) {
    $count_stmt->bindParam(':search_term', $search_param);
}
$count_stmt->execute();
$total_results = $count_stmt->fetchColumn();
$total_pages = ceil($total_results / $results_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Businesses - Uganda Connect</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .form-container form {
            display: flex;
            align-items: center;
        }
        .form-container input[type="text"] {
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 300px;
        }
        .form-container button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #45a049;
        }
        .add-button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }
        .add-button:hover {
            background-color: #45a049;
        }
        .business-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .business-table th, .business-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            max-width: 200px;
        }
        .business-table th {
            background-color: #f2f2f2;
        }
        .business-table .edit-button, .business-table .delete-button {
            padding: 5px 10px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .business-table .edit-button {
            background-color: #2196F3;
        }
        .business-table .edit-button:hover {
            background-color: #1976D2;
        }
        .business-table .delete-button {
            background-color: #f44336;
        }
        .business-table .delete-button:hover {
            background-color: #e53935;
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
        <h2>Manage Businesses</h2>
        <div class="form-container">
            <form action="manage_businesses.php" method="GET">
                <input type="text" name="search" placeholder="Search businesses by name..." value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
                <button type="submit">Search</button>
            </form>
            <a href="add_business.php" class="add-button">Add New Business</a>
        </div>
        <table class="business-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>Website</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($businesses as $business): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($business['name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($business['category_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($business['description'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($business['contact_phone'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($business['address'] ?? ''); ?></td>
                        <td><a href="<?php echo htmlspecialchars($business['website'] ?? ''); ?>" target="_blank"><?php echo htmlspecialchars($business['website'] ?? ''); ?></a></td>
                        <td>
                            <a href="edit_business.php?business_id=<?php echo $business['business_id']; ?>" class="edit-button">Edit</a>
                            <a href="confirm_delete_business.php?business_id=<?php echo $business['business_id']; ?>" class="delete-button">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_term); ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 Uganda Connect</p>
    </footer>
</body>
</html>