<?php include 'db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nkozi Online - Home</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }
        header {
            background-color: #007BFF;
            color: white;
            padding: 15px 20px;
            text-align: center;
            border-radius: 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        header h1 {
            margin: 0 10px 0 0;
            font-size: 28px;
        }
        header nav {
            display: flex;
            gap: 15px;
            margin: 10px 0;
        }
        header nav a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            background-color: #4CAF50;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 16px;
            display: block;
        }
        header nav a:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }
        .login-button {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            background-color: #DC3545;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 16px;
            margin-left: 10px;
            display: block;
            white-space: nowrap;
        }
        .login-button:hover {
            background-color: #C82333;
            transform: translateY(-2px);
        }
        main {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .search-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .search-container form {
            display: flex;
            align-items: center;
            flex: 1 1 300px;
            gap: 10px;
        }
        .search-container input[type="text"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
            font-size: 14px;
            transition: border-color 0.2s ease;
        }
        .search-container input[type="text"]:focus {
            outline: none;
            border-color: #007BFF;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }
        .search-container button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            white-space: nowrap;
        }
        .search-container button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        h2 {
            color: #007BFF;
            margin-bottom: 20px;
            font-size: 24px;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 10px;
        }
        ul {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            max-width: 100%;
            gap: 15px;
        }
        ul li {
            background-color: #fff;
            margin: 5px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        ul li:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        ul li a {
            text-decoration: none;
            color: #333;
            display: block;
            font-size: 16px;
            word-wrap: break-word;
        }
        ul li a:hover {
            color: #007BFF;
        }
        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 20px;
            border-top: 1px solid #ddd;
        }
        @media (max-width: 1200px) {
            ul {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
                text-align: center;
            }
            header nav {
                flex-direction: column;
                align-items: center;
            }
            .search-container {
                flex-direction: column;
                align-items: stretch;
            }
            .search-container form {
                flex-direction: column;
                align-items: stretch;
            }
            .search-container input[type="text"] {
                margin-bottom: 10px;
            }
            .login-button {
                margin-left: 0;
            }
            ul {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to Nkozi Online</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="categories.php">Categories</a>
            <a href="businesses.php">Businesses</a>
            <a href="add_business.php">Add Business</a>
        </nav>
        <a href="login.php" class="login-button">Admin/Staff Login</a>
    </header>
    <main>
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
        <p>&copy; 2025 Nkozi Online</p>
    </footer>
</body>
</html>
