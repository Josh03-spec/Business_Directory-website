<!-- filepath: c:\WAMP_SERVER\www\Business_Directory-website\business_directory\edit_business.php -->
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
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-table {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            border-collapse: collapse;
        }
        .form-table th, .form-table td {
            padding: 10px;
            text-align: left;
        }
        .form-table th {
            width: 30%;
        }
        .form-table td {
            width: 70%;
        }
        .form-table input, .form-table select, .form-table textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .form-table button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-table button:hover {
            background-color: #45a049;
        }
        .current-details, .edit-form {
            width: 45%;
            display: inline-block;
            vertical-align: top;
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
        <div class="current-details">
            <h3>Current Details</h3>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($category['category_name']); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($business['name']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($business['description']); ?></p>
            <p><strong>Contact Phone:</strong> <?php echo htmlspecialchars($business['contact_phone']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($business['address']); ?></p>
            <p><strong>Website:</strong> <a href="<?php echo htmlspecialchars($business['website']); ?>" target="_blank"><?php echo htmlspecialchars($business['website']); ?></a></p>
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
                                    echo "<option value='" . htmlspecialchars($row['category_id']) . "'>" . htmlspecialchars($row['category_name']) . "</option>";
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
    </main>
    <footer>
        <p>&copy; 2025 Uganda Connect</p>
    </footer>
</body>
</html>