
<?php include 'db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nkozi Online - Home</title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative; /* Added for absolute positioning of login button */
        }
        header h1 {
            margin: 0 0 1rem 0;
            font-size: 2rem;
            width: 100%;
        }
        header nav {
            margin-bottom: 1rem;
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
        .login-button {
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 1.1rem;
            background-color: #4CAF50;
            white-space: nowrap;
            margin-left: auto; /* Push to the right */
        }
        .login-button:hover {
            background-color: #388E3C;
            transform: translateY(-0.125rem);
        }
        main {
            flex: 1;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .search-container {
            display: flex;
            justify-content: center; /* Center the form horizontally */
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .search-container form {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            max-width: 75%; /* Increased width to 75% */
        }
        .search-container input[type="text"] {
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            width: 100%;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.2s ease;
        }
        .search-container input[type="text"]:focus {
            outline: none;
            border-color: #007BFF;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        .search-container button {
            padding: 0.75rem 1.5rem;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            font-size: 1.1rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            white-space: nowrap;
            flex: 0 0 auto; /* Prevent button from growing */
        }
        .search-container button:hover {
            background-color: #0056b3;
            transform: translateY(-0.125rem);
        }
        h2 {
            color: #007BFF;
            margin-bottom: 1.5rem;
            font-size: 1.875rem;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 0.75rem;
            text-align: center;
        }
        ul {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        ul li {
            background-color: #fff;
            padding: 1.25rem;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        ul li:hover {
            background-color: #f7fafc;
            transform: translateY(-0.25rem);
            box-shadow: 0 6px 8px -1px rgba(0, 0, 0, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.08);
        }
        ul li a {
            text-decoration: none;
            color: #333;
            display: block;
            font-size: 1.1rem;
            word-wrap: break-word;
            transition: color 0.2s ease;
        }
        ul li a:hover {
            color: #007BFF;
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

        @media (max-width: 1200px) {
            ul {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 992px) {
            ul {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
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
            .login-button {
                margin-left: 0;
            }
            .search-container {
                flex-direction: column;
                align-items: stretch;
            }
            .search-container form {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }
            .search-container input[type="text"] {
                margin-bottom: 0;
            }
            .search-container button {
                width: 100%;
                max-width: 300px;
                margin: 0 auto;
            }
            ul {
                grid-template-columns: 1fr;
            }
        }

        /* Custom Media Queries for responsive grid */
        @media (min-width: 1200px) {
            ul {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        @media (max-width: 1199px) and (min-width: 992px) {
            ul {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 991px) and (min-width: 768px) {
            ul {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 767px) {
            ul {
                grid-template-columns: 1fr;
            }
        }

        .search-container form {
            max-width: 100%; /* Ensure form doesn't exceed container width */
            flex-wrap: nowrap; /* Prevent wrapping, keep button beside input */
        }
        .search-container input[type="text"] {
            flex: 1 1 auto; /* Allow input to grow and shrink */
            min-width: 200px; /* Add a minimum width for the input */
            margin-bottom: 0; /* Remove bottom margin */
        }
        .search-container button {
            flex: 0 0 auto; /* Prevent button from growing excessively */
        }

        @media (max-width: 768px) {
            .search-container form {
                flex-direction: column;
                align-items: stretch;
                flex-wrap: wrap; /* Allow wrapping on small screens */
            }
            .search-container button {
                width: 100%;
                max-width: 300px;
                margin: 1rem auto 0 auto; /* Add margin-top to button */
            }
            .search-container input[type="text"]{
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <header>
        <h1>Welcome to Nkozi Online</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="categories.php">Categories</a>
            <a href="businesses.php">Businesses</a>
            <a href="add_business.php">Add Business</a>
            <a href="login.php" class="login-button">Admin/Staff Login</a>
        </nav>
    </header>
    <main class="container mx-auto py-8">
        <div class="search-container">
            <form action="search.php" method="GET">
                <input type="text" name="query" placeholder="Search for businesses..." required>
                <button type="submit">Search</button>
            </form>
        </div>
        <h2>Categories</h2>
        <ul>
            <?php
            $stmt = $conn->query("SELECT * FROM categories");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<li><a href='businesses.php?category_id=" . htmlspecialchars($row['category_id']) . "'>" . htmlspecialchars($row['category_name']) . "</a></li>";
            }
            ?>
        </ul>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online.</p>
    </footer>
</body>
</html>
