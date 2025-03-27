<?php include 'db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nkozi Online - Categories</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        /* Global Styles */
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
            flex: 1;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        main h2 {
            font-size: 2.25rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 2rem;
            text-align: center;
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

        /* Table Styles */
        table {
            width: 100%;
            max-width: 800px;
            margin-top: 2rem;
            border-collapse: collapse;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        th, td {
            padding: 1.25rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            font-weight: 600;
            color: #1e293b;
        }
        td {
            color: #4b5563;
        }
        th:first-child,
        td:first-child {
            padding-left: 1.5rem;
        }
        th:last-child,
        td:last-child {
            padding-right: 1.5rem;
        }
        tr:last-child td {
            border-bottom: none;
        }
        thead {
            background-color: #f7fafc;
        }
        tbody tr:hover {
            background-color: #edf2f7;
            transition: background-color 0.2s ease;
        }
        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <header>
        <h1>Nkozi Online</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="categories.php">Categories</a>
            <a href="businesses.php">Businesses</a>
            <a href="add_business.php">Add Business</a>
        </nav>
    </header>
    <main class="container mx-auto py-8">
        <h2>Categories</h2>
        <table>
            <thead>
                <tr>
                    <th>Category ID</th>
                    <th>Category Name</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->query("SELECT * FROM categories");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr><td>" . htmlspecialchars($row['category_id']) . "</td><td>" . htmlspecialchars($row['category_name']) . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online</p>
    </footer>
</body>
</html>
