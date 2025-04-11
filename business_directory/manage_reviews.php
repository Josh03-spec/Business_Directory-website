<?php
include 'db_connection.php';
session_start();

// Check admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Handle review deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_review'])) {
    $review_id = filter_input(INPUT_POST, 'review_id', FILTER_VALIDATE_INT);
    if ($review_id) {
        $delete_stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
        $delete_stmt->execute([$review_id]);
        header("Location: manage_reviews.php");
        exit;
    }
}

// Pagination and filtering setup
$results_per_page = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $results_per_page;

// Filter parameters
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$min_rating = isset($_GET['min_rating']) ? intval($_GET['min_rating']) : 0;
$business_filter = isset($_GET['business_id']) ? intval($_GET['business_id']) : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build base query
$query = "SELECT r.*, b.name AS business_name, u.username 
          FROM reviews r
          JOIN businesses b ON r.business_id = b.business_id
          JOIN users u ON r.user_id = u.user_id
          WHERE 1=1";

$params = [];

// Apply filters
if (!empty($search_term)) {
    $query .= " AND r.review_text LIKE ?";
    $params[] = "%$search_term%";
}

if ($min_rating > 0) {
    $query .= " AND r.rating >= ?";
    $params[] = $min_rating;
}

if ($business_filter > 0) {
    $query .= " AND r.business_id = ?";
    $params[] = $business_filter;
}

// Apply sorting
switch ($sort) {
    case 'oldest':
        $query .= " ORDER BY r.created_at ASC";
        break;
    case 'highest_rated':
        $query .= " ORDER BY r.rating DESC";
        break;
    case 'lowest_rated':
        $query .= " ORDER BY r.rating ASC";
        break;
    case 'business':
        $query .= " ORDER BY b.name ASC";
        break;
    default:
        $query .= " ORDER BY r.created_at DESC";
}

// Add pagination
$query .= " LIMIT ? OFFSET ?";
$params[] = $results_per_page;
$params[] = $offset;

// Prepare and execute query
$stmt = $conn->prepare($query);
if ($params) {
    foreach ($params as $index => $value) {
        $stmt->bindValue($index + 1, $value); // Bind parameters using 1-based index
    }
}
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Use fetchAll for PDO
$reviews = $result;

// Get total count
$count_query = "SELECT COUNT(*) FROM reviews r WHERE 1=1";
if (!empty($search_term)) $count_query .= " AND r.review_text LIKE '%$search_term%'";
if ($min_rating > 0) $count_query .= " AND r.rating >= $min_rating";
if ($business_filter > 0) $count_query .= " AND r.business_id = $business_filter";

$count_stmt = $conn->prepare($count_query);
$count_stmt->execute();
$total_reviews = $count_stmt->fetch(PDO::FETCH_NUM)[0]; // Use fetch with FETCH_NUM to get the count
$total_pages = ceil($total_reviews / $results_per_page);

// Get businesses for filter dropdown
$businesses = $conn->query("SELECT business_id, name FROM businesses ORDER BY name")->fetchAll(PDO::FETCH_ASSOC); // Use fetchAll for PDO
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - Nkozi Online</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        /* Inherit existing admin styles */
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
        /* Style for navigation links */
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
        }
        .filters {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }
        .filter-group {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        select, input[type="text"], input[type="number"] {
            padding: 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            font-family: inherit;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .rating-stars {
            color: #ffd700;
        }
        .delete-form {
            display: inline-block;
        }
        .delete-button {
            background-color: #dc3545;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .delete-button:hover {
            background-color: #bb2d3b;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            gap: 0.5rem;
        }
        .pagination a {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            background-color: #e2e8f0;
            color: #2d3748;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .pagination a:hover {
            background-color: #cbd5e0;
        }
        .pagination .active {
            background-color: #007BFF;
            color: white;
        }
        footer {
            margin-top: 10rem;
            text-align: center;
            color: #888;
            font-size: 0.9rem;
            padding: 1rem;
            background-color: #f0f4f8;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <header>
        <h1>Nkozi Online</h1>
        <nav>
            <!-- Updated navigation links with consistent styling -->
            <a href="admin_dashboard.php">Admin Dashboard</a>
            <a href="manage_businesses.php">Manage Businesses</a>
            <a href="manage_reviews.php">Manage Reviews</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <h2>Manage Reviews</h2>
        
        <div class="filters">
            <form method="GET">
                <div class="filter-group">
                    <input type="text" name="search" placeholder="Search reviews..." 
                           value="<?php echo htmlspecialchars($search_term); ?>">
                    <select name="min_rating">
                        <option value="0" <?php echo $min_rating == 0 ? 'selected' : ''; ?>>All Ratings</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?php echo $i; ?>" <?php echo $min_rating == $i ? 'selected' : ''; ?>>
                                <?php echo $i; ?>+ Stars
                            </option>
                        <?php endfor; ?>
                    </select>
                    <select name="business_id">
                        <option value="0">All Businesses</option>
                        <?php foreach ($businesses as $business): ?>
                            <option value="<?php echo $business['business_id']; ?>"
                                <?php echo $business_filter == $business['business_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($business['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select name="sort">
                        <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                        <option value="oldest" <?php echo $sort == 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                        <option value="highest_rated" <?php echo $sort == 'highest_rated' ? 'selected' : ''; ?>>Highest Rated</option>
                        <option value="lowest_rated" <?php echo $sort == 'lowest_rated' ? 'selected' : ''; ?>>Lowest Rated</option>
                        <option value="business" <?php echo $sort == 'business' ? 'selected' : ''; ?>>Business Name</option>
                    </select>
                    <button type="submit" class="submit-button">Apply Filters</button>
                </div>
            </form>
        </div>

        <?php if (count($reviews) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Business</th>
                        <th>User</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($review['business_name']); ?></td>
                            <td><?php echo htmlspecialchars($review['username']); ?></td>
                            <td>
                                <span class="rating-stars">
                                    <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($review['review_text']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($review['created_at'])); ?></td>
                            <td>
                                <form class="delete-form" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this review?');">
                                    <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                    <button type="submit" name="delete_review" class="delete-button">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_term); ?>&min_rating=<?php echo $min_rating; ?>&business_id=<?php echo $business_filter; ?>&sort=<?php echo $sort; ?>"
                       class="<?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php else: ?>
            <p>No reviews found matching the current filters.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online</p>
    </footer>
</body>
</html>