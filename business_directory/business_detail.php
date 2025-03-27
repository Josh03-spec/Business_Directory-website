<?php
include 'db_connection.php';

if (!isset($_GET['business_id'])) {
    die("Business ID not provided.");
}

$business_id = $_GET['business_id'];
$stmt = $conn->prepare("SELECT b.name, b.description, b.contact_phone, b.address, b.website, c.category_name, u.username FROM businesses b LEFT JOIN categories c ON b.category_id = c.category_id LEFT JOIN users u ON b.user_id = u.user_id WHERE b.business_id = :business_id AND b.is_approved = TRUE");
$stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    die("Business not found or not approved.");
}

$business = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($business['name'] ?? ''); ?> - Nkozi Online</title>
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
        main p {
            margin-bottom: 1rem;
            font-size: 1.1rem;
            color: #4a5568;
        }
        main strong {
            color: #1a202c;
        }
        main a {
            color: #007BFF;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        main a:hover {
            color: #0056b3;
            text-decoration: underline;
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
            header nav {
                flex-direction: column;
            }
            header nav a {
                margin: 0.5rem 0;
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
        </nav>
    </header>
    <main>
        <h2><?php echo htmlspecialchars($business['name'] ?? ''); ?></h2>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($business['category_name'] ?? ''); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($business['description'] ?? ''); ?></p>
        <p><strong>Contact:</strong> <?php echo htmlspecialchars($business['contact_phone'] ?? 'Not available'); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($business['address'] ?? 'Not available'); ?></p>
        <p><strong>Website:</strong> 
            <?php 
            if (!empty($business['website'])) {
                echo '<a href="' . htmlspecialchars($business['website']) . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($business['website']) . '</a>';
            } else {
                echo 'No website available';
            }
            ?>
        </p>
        <p><strong>Submitted By:</strong> <?php echo htmlspecialchars($business['username'] ?? 'Unknown'); ?></p>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online</p>
    </footer>
</body>
</html>
