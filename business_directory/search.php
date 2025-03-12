<?php
include 'db_connection.php';

$search_term = isset($_GET['query']) ? trim($_GET['query']) : '';
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$results_per_page = 10;
$offset = ($page - 1) * $results_per_page;

if (empty($search_term) && empty($category_id)) {
    die("Please enter a search term or select a category.");
}

// Prepare the search term for use in SQL
$search_term = '%' . $search_term . '%';

// Build the SQL query
$query = "
    SELECT b.business_id, b.name, b.description, b.contact_phone, c.category_name 
    FROM businesses b 
    LEFT JOIN categories c ON b.category_id = c.category_id 
    WHERE (b.name LIKE :search_term OR 
           b.description LIKE :search_term OR 
           c.category_name LIKE :search_term)
";

// Add category filter conditionally
if (!empty($category_id)) {
    $query .= " AND b.category_id = :category_id";
}

// Add the LIMIT clause
$query .= " ORDER BY b.name ASC LIMIT :offset, :results_per_page";

// Prepare and execute the statement
$stmt = $conn->prepare($query);

// Bind parameters properly
$stmt->bindValue(':search_term', $search_term, PDO::PARAM_STR);

if (!empty($category_id)) {
    $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
}

// Bind the offset and results_per_page as named parameters
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);

$stmt->execute();
$paginated_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of results
$count_query = "
    SELECT COUNT(*) 
    FROM businesses b 
    LEFT JOIN categories c ON b.category_id = c.category_id 
    WHERE (b.name LIKE :search_term OR 
           b.description LIKE :search_term OR 
           c.category_name LIKE :search_term)";
        
if (!empty($category_id)) {
    $count_query .= " AND b.category_id = :category_id";
}

$count_stmt = $conn->prepare($count_query);
$count_stmt->bindValue(':search_term', $search_term, PDO::PARAM_STR);
if (!empty($category_id)) {
    $count_stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
}
$count_stmt->execute();
$total_results = $count_stmt->fetchColumn();
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
        <h2>Search Results for "<?php echo htmlspecialchars($search_term); ?>"</h2>
        <?php if (count($paginated_results) > 0): ?>
            <ul>
                <?php foreach ($paginated_results as $result): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($result['name']); ?></strong><br>
                        Category: <?php echo htmlspecialchars($result['category_name']); ?><br>
                        Description: <?php echo htmlspecialchars($result['description']); ?><br>
                        Contact: <?php echo htmlspecialchars($result['contact_phone']); ?><br>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?query=<?php echo urlencode($search_term); ?>&category_id=<?php echo urlencode($category_id); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        <?php else: ?>
            <p>No results found for "<?php echo htmlspecialchars($search_term); ?>".</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2025 Uganda Connect</p>
    </footer>
</body>
</html>