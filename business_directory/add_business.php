<?php include 'db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nkozi Online - Add Business</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        /* Custom CSS for the business cards - Refined */
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
            padding: 0.75rem;
            box-sizing: border-box;
            border-radius: 0.375rem;
            border: 1px solid #e2e8f0;
            font-size: 1rem;
            transition: border-color 0.2s ease;
        }

        .form-table input:focus, .form-table select:focus, .form-table textarea:focus {
            outline: none;
            border-color: #007BFF;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .form-table textarea {
            min-height: 100px;
            resize: vertical;
        }
        .form-table button {
            padding: 0.75rem 1.5rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 0.375rem;
            font-size: 1rem;
            transition: background-color 0.2s ease-in-out, transform 0.1s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }
        .form-table button:hover {
            background-color: #45a049;
            transform: translateY(-0.125rem);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
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

        footer {
            margin-top: 2rem;
            text-align: center;
            color: #888;
            font-size: 0.9rem;
            padding: 1rem;
            background-color: #f0f4f8;
            border-top: 1px solid #ddd;
        }

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
        main {
            flex: 1;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center; /* Center content horizontally */
        }
        h2 {
            font-size: 2.25rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 2rem;
            text-align: center;
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
        
        <form action="submit_business.php" method="POST" class="w-full">
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
                    <td><input type="text" name="name" id="name" required></td>
                </tr>
                <tr>
                    <th><label for="description">Description:</label></th>
                    <td><textarea name="description" id="description" required></textarea></td>
                </tr>
                <tr>
                    <th><label for="contact_phone">Contact Phone:</label></th>
                    <td><input type="text" name="contact_phone" id="contact_phone" required></td>
                </tr>
                <tr>
                    <th><label for="address">Address:</label></th>
                    <td><input type="text" name="address" id="address" required></td>
                </tr>
                <tr>
                    <th><label for="website">Website:</label></th>
                    <td><input type="url" name="website" id="website"></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <button type="submit">Submit</button>
                    </td>
                </tr>
            </table>
        </form>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online</p>
    </footer>
</body>
</html>
