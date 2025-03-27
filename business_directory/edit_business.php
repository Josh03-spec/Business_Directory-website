<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$business_id = isset($_GET['business_id']) ? intval($_GET['business_id']) : 0;
if ($business_id == 0) {
    header("Location: manage_businesses.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM businesses WHERE business_id = :business_id");
$stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
$stmt->execute();
$business = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$business) {
    header("Location: manage_businesses.php");
    exit;
}

// Fetch the category name
$category_stmt = $conn->prepare("SELECT category_name FROM categories WHERE category_id = :category_id");
$category_stmt->bindParam(':category_id', $business['category_id'], PDO::PARAM_INT);
$category_stmt->execute();
$category = $category_stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $contact_phone = $_POST['contact_phone'];
    $address = $_POST['address'];
    $website = $_POST['website'];

    // Delete the old business entry
    $delete_stmt = $conn->prepare("DELETE FROM businesses WHERE business_id = :business_id");
    $delete_stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
    $delete_stmt->execute();

    // Insert the new business entry for approval
    $insert_stmt = $conn->prepare("INSERT INTO businesses (category_id, user_id, name, description, contact_phone, address, website, is_approved) VALUES (:category_id, :user_id, :name, :description, :contact_phone, :address, :website, FALSE)");
    $insert_stmt->bindParam(':category_id', $category_id);
    $insert_stmt->bindParam(':user_id', $business['user_id']);
    $insert_stmt->bindParam(':name', $name);
    $insert_stmt->bindParam(':description', $description);
    $insert_stmt->bindParam(':contact_phone', $contact_phone);
    $insert_stmt->bindParam(':address', $address);
    $insert_stmt->bindParam(':website', $website);
    $insert_stmt->execute();

    $message = "Business details updated and submitted for approval.";
    header("Location: manage_businesses.php?message=" . urlencode($message));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Business - Uganda Connect</title>
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
            justify-content: space-between;
            gap: 2rem; /* Adds spacing between the two sections */
        }
        .current-details,
        .edit-form {
            width: 48%; /* Each section takes up 48% of the container width */
            background-color: #f7fafc;
            padding: 1.5rem;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
        }
        .current-details h3,
        .edit-form h3 {
            text-align: center;
            font-size: 1.5rem;
            color: #2d3748;
            margin-bottom: 1rem;
        }
        .current-details p {
            margin-bottom: 0.75rem;
            line-height: 1.7;
            color: #4a5568;
            font-size: 1rem;
        }
        .current-details p strong {
            color: #2d3748;
        }
        .current-details a {
            color: #007BFF;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .current-details a:hover {
            color: #0056b3;
        }
        .edit-form {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
            margin-bottom: 0; /* Remove bottom margin */
        }
        .edit-form table {
            width: 100%;
        }
        .edit-form table th,
        .edit-form table td {
            padding: 0.75rem;
            text-align: left;
        }
        .edit-form table th {
            width: 35%;
            color: #2d3748;
            font-weight: 600;
        }
        .edit-form table td {
            width: 65%;
        }
        .edit-form table input,
        .edit-form table select,
        .edit-form table textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            font-size: 1rem;
            transition: border-color 0.2s ease;
        }
        .edit-form table input:focus,
        .edit-form table select:focus,
        .edit-form table textarea:focus {
            outline: none;
            border-color: #007BFF;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        @media (max-width: 768px) {
            .form-container {
                flex-direction: column;
                align-items: stretch;
            }
            .form-container .current-details,
            .form-container .edit-form {
                width: 100%;
                max-width: none;
                margin-bottom: 2rem;
            }
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
        /* Style for the Submit Button */
        .edit-form table button[type="submit"] {
            background-color:#007BFF; 
            color: white;
            font-size: 1rem;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .edit-form table button[type="submit"]:hover {
            background-color:#0056b3; 
            transform: translateY(-0.125rem);
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
        <h2>Edit Business</h2>
        <div class="form-container">
            <div class="current-details">
                <h3>Current Details</h3>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($category['category_name']); ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($business['name']); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($business['description']); ?></p>
                <p><strong>Contact Phone:</strong> <?php echo htmlspecialchars($business['contact_phone']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($business['address']); ?></p>
                <p><strong>Website:</strong> 
                    <?php if (!empty($business['website'])): ?>
                        <a href="<?php echo htmlspecialchars($business['website']); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo htmlspecialchars($business['website']); ?>
                        </a>
                    <?php else: ?>
                        No website available
                    <?php endif; ?>
                </p>
            </div>
            <div class="edit-form">
                <h3>Edit Details</h3>
                <form action="edit_business.php?business_id=<?php echo $business_id; ?>" method="POST">
                    <table class="form-table">
                        <tr>
                            <th><label for="category_id">Category:</label></th>
                            <td>
                                <select name="category_id" id="category_id" required>
                                    <?php
                                    $stmt = $conn->query("SELECT * FROM categories");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = ($row['category_id'] == $business['category_id']) ? 'selected' : '';
                                        echo "<option value='" . htmlspecialchars($row['category_id']) . "' $selected>" . htmlspecialchars($row['category_name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="name">Business Name:</label></th>
                            <td><input type="text" name="name" id="name" value="<?php echo htmlspecialchars($business['name']); ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="description">Description:</label></th>
                            <td><textarea name="description" id="description" required><?php echo htmlspecialchars($business['description']); ?></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="contact_phone">Contact Phone:</label></th>
                            <td><input type="text" name="contact_phone" id="contact_phone" value="<?php echo htmlspecialchars($business['contact_phone']); ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="address">Address:</label></th>
                            <td><input type="text" name="address" id="address" value="<?php echo htmlspecialchars($business['address']); ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="website">Website:</label></th>
                            <td><input type="url" name="website" id="website" value="<?php echo htmlspecialchars($business['website']); ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <button type="submit">Submit</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </main>
    <footer>
        <p style="text-align: center;">&copy; 2025 Uganda Connect</p>
    </footer>
</body>
</html>
