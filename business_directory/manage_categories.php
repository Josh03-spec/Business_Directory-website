<?php
include 'db_connection.php';
session_start();

// Session inactivity timeout
$inactive = 1800; // 30 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive)) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}
$_SESSION['last_activity'] = time();

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
    <title>Manage Categories - Nkozi Online</title>
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
        /* Specific styling for Admin Dashboard and Logout */
        header nav a[href="admin_dashboard.php"]:hover {
            background-color: #28a745; /* Green on hover */
        }
        header nav a[href="logout.php"]:hover {
            background-color: #dc3545; /* Red on hover */
        }
        main {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        main h2 {
            font-size: 2.25rem;
            color: #2d3748;
            margin-bottom: 2rem;
            text-align: center;
        }
        .form-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            gap: 1.5rem;
        }
        .form-container form {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            width: 100%;
            max-width: 400px;
        }
        .form-container input[type="text"] {
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            width: 100%;
            font-size: 1rem;
            transition: border-color 0.2s ease;
        }
        .form-container input[type="text"]:focus {
            outline: none;
            border-color: #007BFF;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        .form-container button {
            padding: 0.75rem 1.5rem;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            font-size: 1.1rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            white-space: nowrap;
        }
        .form-container button:hover {
            background-color: #0056b3;
            transform: translateY(-0.125rem);
        }
        .category-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .category-table th, .category-table td {
            padding: 1.25rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        .category-table th {
            background-color: #f7fafc;
            color: #2d3748;
            font-weight: 600;
        }
        .category-table td {
            color: #4a5568;
        }
        .category-table tbody tr:hover {
            background-color: #edf2f7;
        }
        .category-table button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            white-space: nowrap;
        }
        .category-table button:hover {
            transform: translateY(-0.125rem);
        }
        .category-table button.edit {
            background-color: #007BFF;
            color: white;
        }
        .category-table button.edit:hover {
            background-color: #0056b3;
        }
        .category-table button.delete {
            background-color: #DC143C;
            color: white;
        }
        .category-table button.delete:hover {
            background-color: #B22222;
        }
        .category-table button.status {
            background-color: #28a745;
            color: white;
        }
        .category-table button.status:hover {
            background-color: #218838;
        }
        .category-table button.status.disabled {
            background-color: #6c757d;
            color: white;
        }
        .category-table button.status.disabled:hover {
            background-color: #5a6268;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }
        .pagination a {
            color: #007BFF;
            padding: 0.75rem 1.5rem;
            margin: 0 0.5rem;
            border-radius: 0.375rem;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            transition: background-color 0.3s ease, color 0.3s ease;
            font-size: 1rem;
        }
        .pagination a:hover {
            background-color: #007BFF;
            color: white;
            border-color: #007BFF;
            transform: translateY(-0.125rem);
        }
        .pagination a.active {
            background-color: #007BFF;
            color: white;
            border-color: #007BFF;
        }
        .no-results {
            text-align: center;
            padding: 2rem;
            font-size: 1.25rem;
            color: #6b7280;
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

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                align-items: center;
            }
            header h1 {
                margin-bottom: 1rem;
            }
            header nav {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }
            .form-container {
                flex-direction: column;
                align-items: stretch;
            }
            .form-container form {
                width: 100%;
                max-width: none;
            }
            .form-container input[type="text"] {
                margin-bottom: 0;
            }
            .form-container button {
                width: 100%;
            }
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
        <?php if (count($categories) > 0): ?>
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
                                <form action="rename_category.php" method="GET" style="display:inline-block;">
                                    <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                                    <button type="submit" class="edit">Rename</button>
                                </form>
                                <form action="manage_categories.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                                    <button type="submit" name="delete" class="delete">Delete</button>
                                </form>
                                 <form action="manage_categories.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                                    <input type="hidden" name="status" value="<?php echo $category['status']; ?>">
                                    <button type="submit" name="toggle_status" class="<?php echo $category['status'] ? 'status' : 'status disabled'; ?>"><?php echo $category['status'] ? 'Disable' : 'Enable'; ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_term); ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        <?php else: ?>
            <p class="no-results">No categories found.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online</p>
    </footer>
</body>
</html>
