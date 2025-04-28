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

// Fetch all businesses that are not approved
$query = "
    SELECT b.business_id, b.name, b.description, b.contact_phone, b.address, b.website, c.category_name, u.username
    FROM businesses b
    LEFT JOIN categories c ON b.category_id = c.category_id
    LEFT JOIN users u ON b.user_id = u.user_id
    WHERE b.is_approved = 0
    ORDER BY b.created_at DESC
    LIMIT :offset, :results_per_page
";
$stmt = $conn->prepare($query);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':results_per_page', $results_per_page, PDO::PARAM_INT);
$stmt->execute();
$submitted_businesses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of results
$count_query = "SELECT COUNT(*) FROM businesses WHERE is_approved = 0";
$total_results = $conn->query($count_query)->fetchColumn();
$total_pages = ceil($total_results / $results_per_page);

// Approve a business
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve'])) {
    $business_id = $_POST['business_id'];
    $approve_query = "UPDATE businesses SET is_approved = 1 WHERE business_id = :business_id";
    $stmt = $conn->prepare($approve_query);
    $stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        header("Location: approve_businesses.php?page=$page");
        exit();
    } else {
        echo "Error approving business.";
    }
}

// Delete a business
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $business_id = $_POST['business_id'];
    $delete_query = "DELETE FROM businesses WHERE business_id = :business_id";
    $stmt = $conn->prepare($delete_query);
    $stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        header("Location: approve_businesses.php?page=$page");
        exit();
    } else {
        echo "Error deleting business.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Businesses - Nkozi Online</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        table th, table td {
            padding: 1.25rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            max-width: 200px;
        }
        table th {
            background-color: #f7fafc;
            color: #2d3748;
            font-weight: 600;
        }
        table td {
            color: #4a5568;
        }
        table tbody tr:hover {
            background-color: #edf2f7;
        }
        .approve-button, .delete-button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
        }
        .approve-button {
            background-color: #007BFF;
            color: white;
        }
        .approve-button:hover {
            background-color: #0056b3;
            transform: translateY(-0.125rem);
        }
        .delete-button {
            background-color: #DC143C;
            color: white;
        }
        .delete-button:hover {
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
        <h2>Submitted Businesses for Approval</h2>
        <?php if (count($submitted_businesses) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Website</th>
                        <th>Submitted By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submitted_businesses as $business): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($business['name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($business['category_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($business['description'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($business['contact_phone'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($business['address'] ?? ''); ?></td>
                            <td><a href="<?php echo htmlspecialchars($business['website'] ?? ''); ?>" target="_blank"><?php echo htmlspecialchars($business['website'] ?? ''); ?></a></td>
                            <td><?php echo htmlspecialchars($business['username'] ?? ''); ?></td>
                            <td>
                                <form method="get" action="confirm_delete_approval.php" style="display:inline;">
                                    <input type="hidden" name="business_id" value="<?php echo $business['business_id']; ?>">
                                    <input type="hidden" name="page" value="<?php echo $page; ?>">
                                    <button type="submit" class="delete-button">Delete</button>
                                </form>
                                <form method="post" action="approve_businesses.php?page=<?php echo $page; ?>" style="display:inline;">
                                    <input type="hidden" name="business_id" value="<?php echo $business['business_id']; ?>">
                                    <button type="submit" name="approve" class="approve-button">Approve</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        <?php else: ?>
            <p>No businesses submitted for approval.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online</p>
    </footer>
</body>
</html>
