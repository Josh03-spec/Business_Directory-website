<!-- filepath: c:\WAMP_SERVER\www\business_directory\submit_business.php -->
<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Assuming you have a way to get the current user ID

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $contact_phone = $_POST['contact_phone'];
    $address = $_POST['address'];
    $website = $_POST['website'];

    $stmt = $conn->prepare("INSERT INTO businesses (category_id, user_id, name, description, contact_phone, address, website, is_approved) VALUES (:category_id, :user_id, :name, :description, :contact_phone, :address, :website, FALSE)");
    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':contact_phone', $contact_phone);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':website', $website);

    if ($stmt->execute()) {
        $message = "Business submitted successfully. It will be reviewed for approval.";
    } else {
        $message = "Error submitting business.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Business - Nkozi Online</title>
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
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #007BFF;
            color: white;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            flex: 1;
        }
        main h2 {
            font-size: 2.25rem;
            color: #2d3748;
            margin-bottom: 2rem;
            text-align: center;
        }
        main p {
            margin-bottom: 1rem;
            font-size: 1.1rem;
            color: #4a5568;
            text-align: center;
        }
        main a {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        main a.green-button {
            background-color: #28a745;
            color: white;
        }
        main a.green-button:hover {
            background-color: #218838;
            transform: translateY(-0.125rem);
        }
        main a.blue-button {
            background-color: #007BFF;
            color: white;
        }
        main a.blue-button:hover {
            background-color: #0056b3;
            transform: translateY(-0.125rem);
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        form label {
            font-weight: 600;
            color: #2d3748;
        }
        form input,
        form select,
        form textarea {
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            font-size: 1rem;
            width: 100%;
        }
        form input:focus,
        form select:focus,
        form textarea:focus {
            outline: none;
            border-color: #007BFF;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        form button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.375rem;
            font-size: 1rem;
            cursor: pointer;
            background-color: #007BFF;
            color: white;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        form button:hover {
            background-color: #0056b3;
            transform: translateY(-0.125rem);
        }
        footer {
            margin-top: auto;
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
        </nav>
    </header>
    <main>
        <h2>Submit Business</h2>
        <?php if ($message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
            <a href="index.php" class="green-button">Go to Homepage</a>
            <a href="add_business.php" class="blue-button">Submit Another Business</a>
        <?php else: ?>
            <form action="submit_business.php" method="POST">
                <label for="category_id">Category:</label>
                <select name="category_id" id="category_id" required>
                    <?php
                    $stmt = $conn->query("SELECT * FROM categories");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . htmlspecialchars($row['category_id']) . "'>" . htmlspecialchars($row['category_name']) . "</option>";
                    }
                    ?>
                </select>
                <label for="name">Business Name:</label>
                <input type="text" name="name" id="name" required>
                <label for="description">Description:</label>
                <textarea name="description" id="description" required></textarea>
                <label for="contact_phone">Contact Phone:</label>
                <input type="text" name="contact_phone" id="contact_phone" required>
                <label for="address">Address:</label>
                <input type="text" name="address" id="address" required>
                <label for="website">Website:</label>
                <input type="url" name="website" id="website">
                <button type="submit">Submit</button>
            </form>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online.</p>
    </footer>
</body>
</html>