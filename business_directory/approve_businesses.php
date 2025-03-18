<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch all businesses that are not approved
$query = "
    SELECT b.business_id, b.name, b.description, b.contact_phone, b.address, b.website, c.category_name, u.username
    FROM businesses b
    LEFT JOIN categories c ON b.category_id = c.category_id
    LEFT JOIN users u ON b.user_id = u.user_id
    WHERE b.is_approved = 0
    ORDER BY b.created_at DESC
";

$submitted_businesses = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Approve a business
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve'])) {
    $business_id = $_POST['business_id'];
    $approve_query = "UPDATE businesses SET is_approved = 1 WHERE business_id = :business_id";
    $stmt = $conn->prepare($approve_query);
    $stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo "Business approved successfully.";
        // Refresh the page to show updated list
        header("Location: approve_businesses.php");
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
        echo "Business deleted successfully.";
        // Refresh the page to show updated list
        header("Location: approve_businesses.php");
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
    <title>Approve Businesses - Uganda Connect</title>
    <link rel="stylesheet" href="styles.css">
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
                                <form method="post" action="approve_businesses.php" style="display:inline;">
                                    <input type="hidden" name="business_id" value="<?php echo $business['business_id']; ?>">
                                    <button type="submit" name="approve">Approve</button>
                                </form>
                                <form method="post" action="approve_businesses.php" style="display:inline;">
                                    <input type="hidden" name="business_id" value="<?php echo $business['business_id']; ?>">
                                    <button type="submit" name="delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No businesses submitted for approval.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2025 Uganda Connect</p>
    </footer>
</body>
</html>