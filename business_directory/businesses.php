<?php include 'db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nkozi Online - Businesses</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        /* Custom CSS for the business cards - Refined */
        .business-card {
            background-color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            border: 1px solid #e2e8f0;
        }

        .business-card:hover {
            transform: translateY(-0.25rem);
            box-shadow: 0 6px 8px -1px rgba(0, 0, 0, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.08);
        }

        .business-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: #1e293b;
        }

        .business-card p {
            font-size: 1rem;
            line-height: 1.625rem;
            color: #4b5563;
        }

        .view-details-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            margin-top: 1rem;
            background-color: #4CAF50;
            color: white;
            font-size: 1rem;
            font-weight: 500;
            border-radius: 0.375rem;
            transition: background-color 0.2s ease-in-out, transform 0.1s ease;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        .view-details-button:hover {
            background-color: #388E3C;
            transform: translateY(-0.125rem);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .pagination a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            background-color: #f3f4f6;
            color: #4b5563;
            font-size: 1rem;
            border-radius: 0.375rem;
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
            text-decoration: none;
            border: 1px solid #e5e7eb;
        }

        .pagination a:hover {
            background-color: #d1d5db;
            color: #1f2937;
        }

        .pagination a.active {
            background-color: #007BFF;
            color: white;
            border-color: #007BFF;
        }

        .business-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 0 1rem;
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
        <h2 class="text-3xl font-semibold text-gray-800 mb-6 text-center" style="text-align: center; margin-bottom: 2rem;">Businesses</h2>
        <?php
        $results_per_page = 10; // Define the number of results per page at the top of the script

        if (isset($_GET['category_id'])) {
            $category_id = $_GET['category_id'];
            $stmt = $conn->prepare("
                SELECT b.business_id, b.name, b.description, c.category_name 
                FROM businesses b
                LEFT JOIN categories c ON b.category_id = c.category_id
                WHERE b.category_id = :category_id AND b.is_approved = TRUE
                LIMIT :offset, :results_per_page
            ");
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);

            // Pagination logic
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $offset = ($page - 1) * $results_per_page;

            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindParam(':results_per_page', $results_per_page, PDO::PARAM_INT);
            $stmt->execute();

            // Get the total number of results for the selected category
            $total_results_query = $conn->prepare("
                SELECT COUNT(*) 
                FROM businesses b
                LEFT JOIN categories c ON b.category_id = c.category_id
                WHERE b.category_id = :category_id AND b.is_approved = TRUE
            ");
            $total_results_query->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $total_results_query->execute();
            $total_results = $total_results_query->fetchColumn();

            // Fetch the category name for display
            $category_name_query = $conn->prepare("SELECT category_name FROM categories WHERE category_id = :category_id");
            $category_name_query->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $category_name_query->execute();
            $category_name = $category_name_query->fetchColumn();

            echo "<h3 class='text-2xl font-semibold text-gray-700 mb-4 text-center'>Category: " . htmlspecialchars($category_name) . "</h3>";
        } else {
            // Default logic for all businesses
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $offset = ($page - 1) * $results_per_page;

            $query = "
                SELECT b.business_id, b.name, b.description, c.category_name 
                FROM businesses b
                LEFT JOIN categories c ON b.category_id = c.category_id
                WHERE b.is_approved = TRUE
                LIMIT :offset, :results_per_page
            ";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindParam(':results_per_page', $results_per_page, PDO::PARAM_INT);
            $stmt->execute();

            // Get the total number of results for all businesses
            $total_results = $conn->query("
                SELECT COUNT(*) 
                FROM businesses b
                LEFT JOIN categories c ON b.category_id = c.category_id
                WHERE b.is_approved = TRUE
            ")->fetchColumn();
        }

        // Calculate the total number of pages
        $total_pages = ceil($total_results / $results_per_page);
        ?>
        <div class="business-list">
            <?php
            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<div class='business-card'>";
                    echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                    echo "<p>" . htmlspecialchars(substr($row['description'], 0, 100)) . "...</p>";
                    echo "<a href='business_detail.php?business_id=" . htmlspecialchars($row['business_id']) . "' class='view-details-button'>View Details</a>";
                    echo "</div>";
                }
            } else {
                echo "<p class='text-gray-600 text-center'>No businesses found.</p>";
            }
            ?>
        </div>
        <?php
        // Display pagination links
        echo "<div class='pagination'>";
        for ($i = 1; $i <= $total_pages; $i++) {
            $activeClass = ($i == $page) ? 'active' : '';
            echo "<a href='?page=$i' class='$activeClass'>" . $i . "</a> ";
        }
        echo "</div>";
        ?>
    </main>
    <footer>