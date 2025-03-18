<!-- filepath: c:\WAMP_SERVER\www\business_directory\add_business.php -->
<?php include 'db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uganda Connect - Add Business</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file here -->
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
        </nav>
    </header>
    <main>
        <h2>Add Business</h2>
        <form action="submit_business.php" method="POST">
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
        <p>&copy; 2025 Uganda Connect</p>
    </footer>
</body>
</html>