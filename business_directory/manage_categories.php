<!-- filepath: c:\WAMP_SERVER\www\Business_Directory-website\business_directory\manage_categories.php -->
<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Handle category actions (create, rename, delete, enable/disable)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        $category_name = $_POST['category_name'];
        $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (:category_name)");
        $stmt->bindParam(':category_name', $category_name);
        $stmt->execute();
    } elseif (isset($_POST['rename'])) {
        $category_id = $_POST['category_id'];
        $category_name = $_POST['category_name'];
        $stmt = $conn->prepare("UPDATE categories SET category_name = :category_name WHERE category_id = :category_id");
        $stmt->bindParam(':category_name', $category_name);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();
    } elseif (isset($_POST['delete'])) {
        $category_id = $_POST['category_id'];
        header("Location: confirm_delete_category.php?category_id=$category_id");
        exit();
    } elseif (isset($_POST['toggle_status'])) {
        $category_id = $_POST['category_id'];
        $status = $_POST['status'];
        $new_status = $status ? 0 : 1;
        $stmt = $conn->prepare("UPDATE categories SET status = :new_status WHERE category_id = :category_id");
        $stmt->bindParam(':new_status', $new_status);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();
    }
}

// Pagination setup
$results_per_page = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $results_per_page;

// Search categories
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_query = $search_term ? "WHERE category_name LIKE :search_term" : '';
$search_param = $search_term ? '%' . $search_term . '%' : '';

// Fetch categories
$query = "SELECT * FROM categories $search_query LIMIT :offset, :results_per_page";
$stmt = $conn->prepare($query);
if ($search_term) {
    $stmt->bindParam(':search_term', $search_param);
}
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':results_per_page', $results_per_page, PDO::PARAM_INT);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of results
$count_query = "SELECT COUNT(*) FROM categories $search_query";
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
    <title>Manage Categories - Uganda Connect</title>
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
        .category-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .category-table th, .category-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .category-table th {
            background-color: #f2f2f2;
        }
        .category-table button {
            padding: 5px 10px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .category-table button:hover {
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
        <h2>Manage Categories</h2>
        <div class="form-container">
            <form action="manage_categories.php" method="GET">
                <input type="text" name="search" placeholder="Search categories..." value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit">Search</button>
            </form>
            <form action="manage_categories.php" method="POST">
                <input type="text" name="category_name" placeholder="Create a new category" required>
                <button type="submit" name="create">Create Category</button>
            </form>
        </div>
        <table class="category-table">
            <thead>
                <tr>
                    <th>Category ID</th>
                    <th>Category Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category['category_id']); ?></td>
                        <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                        <td><?php echo $category['status'] ? 'Enabled' : 'Disabled'; ?></td>
                        <td>
                            <form action="manage_categories.php" method="POST" style="display:inline;">
                                <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                                <input type="hidden" name="category_name" value="<?php echo $category['category_name']; ?>">
                                <button type="submit" name="rename">Rename</button>
                            </form>
                            <form action="manage_categories.php" method="POST" style="display:inline;">
                                <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                                <button type="submit" name="delete">Delete</button>
                            </form>
                            <form action="manage_categories.php" method="POST" style="display:inline;">
                                <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                                <input type="hidden" name="status" value="<?php echo $category['status']; ?>">
                                <button type="submit" name="toggle_status"><?php echo $category['status'] ? 'Disable' : 'Enable'; ?></button>
                            </form>
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