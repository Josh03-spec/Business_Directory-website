<?php
include 'db_connection.php';

$search_term = isset($_GET['query']) ? trim($_GET['query']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$results_per_page = 10;
$offset = ($page - 1) * $results_per_page;

if (empty($search_term)) {
    die("Please enter a search term.");
}

// Prepare the search term for use in SQL
$search_term = '%' . $search_term . '%';

// Build the SQL query
$query = "
    SELECT b.business_id, b.name, b.description, b.contact_phone, b.address, b.website
    FROM businesses b 
    WHERE (b.name LIKE '$search_term' OR 
            b.description LIKE '$search_term')
    AND b.is_approved = TRUE /* Ensure only approved businesses are shown */
    ORDER BY b.name ASC 
    LIMIT $offset, $results_per_page
";

// Execute the query
$paginated_results = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Get total number of results
$count_query = "
    SELECT COUNT(*) 
    FROM businesses b 
    WHERE (b.name LIKE '$search_term' OR 
            b.description LIKE '$search_term')
    AND b.is_approved = TRUE /* Ensure only approved businesses are counted */
";

// Execute the count query
$total_results = $conn->query($count_query)->fetchColumn();
$total_pages = ceil($total_results / $results_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Nkozi Online</title>
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
            gap: 1rem;
        }
        header nav a {
            color: white;
            text-decoration: none;
            margin: 0 1rem;
            font-size: 1.1rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        header nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-0.125rem);
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
        main a {
            color: #007BFF;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        main a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        .results-list {
            list-style: none;
            padding: 0;
            margin-top: 2rem;
        }
        .results-list li {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border-radius: 0.5rem;
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .results-list li:hover {
            transform: translateY(-0.25rem);
            box-shadow: 0 6px 8px -1px rgba(0, 0, 0, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.08);
        }
        .results-list h3 {
            font-size: 1.5rem;
            color: #2d3748;
            margin-bottom: 1rem;
        }
        .results-list p {
            margin-bottom: 0.75rem;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }
        .pagination a {
            color: #007BFF;
            padding: 0.75rem 1.5rem;
            margin: 0 0.5rem;
            border-radius: 0.375rem;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            transition: background-color 0.3s ease, color 0.3s ease;
            font-size: 1rem;
        }
        .pagination a:hover {
            background-color: #007BFF;
            color: white;
            border-color: #007BFF;
            transform: translateY(-0.125rem);
        }
        .pagination a.active {
            background-color: #007BFF;
            color: white;
            border-color: #007BFF;
        }
        .no-results {
            text-align: center;
            padding: 2rem;
            font-size: 1.25rem;
            color: #6b7280;
        }
        footer {
            margin-top: 2rem;
            text-align: center; /* Center the text */
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
            .search-container form {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }
            .search-container button {
                width: 100%;
                max-width: 300px;
                margin: 0 auto;
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
        <h2>Search Results for "<?php echo htmlspecialchars($_GET['query']); ?>"</h2>
        <?php if (count($paginated_results) > 0): ?>
            <ul class="results-list">
                <?php foreach ($paginated_results as $result): ?>
                    <li>
                        <h3><a href="business_detail.php?business_id=<?php echo htmlspecialchars($result['business_id']); ?>"><?php echo htmlspecialchars($result['name']); ?></a></h3>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($result['description']); ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($result['contact_phone']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($result['address']); ?></p>
                        <p><strong>Website:</strong> <?php echo htmlspecialchars($result['website'] ?? ''); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?query=<?php echo urlencode($_GET['query']); ?>&page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        <?php else: ?>
            <p class="no-results">No results found for "<?php echo htmlspecialchars($_GET['query']); ?>".</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online</p>
    </footer>
</body>
</html>
