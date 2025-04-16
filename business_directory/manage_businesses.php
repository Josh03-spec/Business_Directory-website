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
        .add-button {
            padding: 0.75rem 1.5rem;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-size: 1.1rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            white-space: nowrap;
        }
        .add-button:hover {
            background-color: #218838;
            transform: translateY(-0.125rem);
        }
        .business-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .business-table th, .business-table td {
            padding: 1.25rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
             text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            max-width: 200px;
        }
        .business-table th {
            background-color: #f7fafc;
            color: #2d3748;
            font-weight: 600;
        }
        .business-table td {
            color: #4a5568;
        }
        .business-table tbody tr:hover {
            background-color: #edf2f7;
        }
        .business-table .edit-button, .business-table .delete-button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
        }
        .business-table .edit-button {
            background-color: #007BFF;
            color: white;
        }
        .business-table .edit-button:hover {
            background-color: #0056b3;
            transform: translateY(-0.125rem);
        }
        .business-table .delete-button {
            background-color: #DC143C;
            color: white;
        }
        .business-table .delete-button:hover {
            background-color: #B22222;
            transform: translateY(-0.125rem);
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
            .form-container button, .form-container a.add-button{
                width: 100%;
            }
            .business-table {
                /* Force table to scroll on small screens */
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            .business-table th, .business-table td{
                max-width: none; /* prevent wrapping */
                white-space: nowrap;
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
        <h2>Manage Businesses</h2>
        <div class="form-container">
            <form action="manage_businesses.php" method="GET">
                <input type="text" name="search" placeholder="Search businesses by name..." value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
                <button type="submit">Search</button>
            </form>
            <a href="add_business.php" class="add-button">Add New Business</a>
        </div>
        <?php if (count($businesses) > 0): ?>
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
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_term); ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        <?php else: ?>
            <p class="no-results">No businesses found.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online</p>
    </footer>
</body>
</html>
