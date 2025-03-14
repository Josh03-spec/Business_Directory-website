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
    SELECT b.business_id, b.name, b.description, b.contact_phone, c.category_name 
    FROM businesses b 
    LEFT JOIN categories c ON b.category_id = c.category_id 
    WHERE (b.name LIKE '$search_term' OR 
           b.description LIKE '$search_term' OR 
           c.category_name LIKE '$search_term')
    ORDER BY b.name ASC 
    LIMIT $offset, $results_per_page
";

// Execute the query
$paginated_results = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Get total number of results
$count_query = "
    SELECT COUNT(*) 
    FROM businesses b 
    LEFT JOIN categories c ON b.category_id = c.category_id 
    WHERE (b.name LIKE '$search_term' OR 
           b.description LIKE '$search_term' OR 
           c.category_name LIKE '$search_term')
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
    <title>Search Results - Uganda Connect</title>
    <link rel="stylesheet" href="styles.css">
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
        <h2>Search Results for "<?php echo htmlspecialchars($_GET['query']); ?>"</h2>
        <?php if (count($paginated_results) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Contact</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paginated_results as $result): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($result['name']); ?></td>
                            <td><?php echo htmlspecialchars($result['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($result['description']); ?></td>
                            <td><?php echo htmlspecialchars($result['contact_phone']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?query=<?php echo urlencode($_GET['query']); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        <?php else: ?>
            <p>No results found for "<?php echo htmlspecialchars($_GET['query']); ?>".</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2025 Uganda Connect</p>
    </footer>
</body>
</html>