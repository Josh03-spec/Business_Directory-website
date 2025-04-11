<?php
include 'db_connection.php';

if (!isset($_GET['business_id'])) {
    die("Business ID not provided.");
}

$business_id = $_GET['business_id'];

// Get business details
$business_stmt = $conn->prepare("
    SELECT b.*, c.category_name, u.username 
    FROM businesses b
    LEFT JOIN categories c ON b.category_id = c.category_id
    LEFT JOIN users u ON b.user_id = u.user_id
    WHERE b.business_id = :business_id AND b.is_approved = TRUE
");
$business_stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
$business_stmt->execute();
$business = $business_stmt->fetch(PDO::FETCH_ASSOC);

if (!$business) {
    die("Business not found or not approved.");
}

// Get average rating and review count
$rating_stmt = $conn->prepare("
    SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
    FROM reviews 
    WHERE business_id = :business_id
");
$rating_stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
$rating_stmt->execute();
$rating_info = $rating_stmt->fetch(PDO::FETCH_ASSOC);

// Set default rating values
$avg_rating = $rating_info['avg_rating'] ?? 0;
$review_count = $rating_info['review_count'] ?? 0;

// Pagination setup for reviews
$reviews_per_page = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $reviews_per_page;

// Get reviews
$reviews_stmt = $conn->prepare("
    SELECT r.*, u.username 
    FROM reviews r
    JOIN users u ON r.user_id = u.user_id
    WHERE r.business_id = :business_id
    ORDER BY r.created_at DESC
    LIMIT :offset, :reviews_per_page
");
$reviews_stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
$reviews_stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$reviews_stmt->bindParam(':reviews_per_page', $reviews_per_page, PDO::PARAM_INT);
$reviews_stmt->execute();
$reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total reviews for pagination
$total_reviews_stmt = $conn->prepare("SELECT COUNT(*) FROM reviews WHERE business_id = :business_id");
$total_reviews_stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
$total_reviews_stmt->execute();
$total_reviews = $total_reviews_stmt->fetchColumn();
$total_pages = ceil($total_reviews / $reviews_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($business['name']); ?> - Nkozi Online</title>
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
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
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
        .alert {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0.5rem;
            border: 1px solid transparent;
        }
        .alert.success {
            background-color: #d1e7dd;
            color: #0f5132;
            border-color: #badbcc;
        }
        .alert.error {
            background-color: #f8d7da;
            color: #842029;
            border-color: #f5c2c7;
        }
        .review-section {
            margin-top: 2rem;
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .review-form {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background-color: #f8f9fa;
            border-radius: 0.5rem;
        }
        .review-form textarea {
            width: 100%;
            padding: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            margin: 1rem 0;
            font-family: inherit;
        }
        .rating-stars select {
            padding: 0.5rem;
            border-radius: 0.375rem;
            border: 1px solid #e2e8f0;
        }
        .submit-button {
            background-color: #007BFF;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .submit-button:hover {
            background-color: #0056b3;
        }
        .review-card {
            background-color: white;
            padding: 1.5rem;
            margin: 1rem 0;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .review-author {
            font-weight: 600;
            color: #2d3748;
        }
        .review-date {
            color: #718096;
            font-size: 0.9rem;
        }
        .star-rating {
            color: #ffd700;
            font-size: 1.2rem;
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
        .delete-button {
            background-color: #dc3545;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .delete-button:hover {
            background-color: #bb2d3b;
        }
        @media (max-width: 768px) {
            header nav {
                flex-direction: column;
                align-items: center;
            }
            header nav a {
                margin: 0.5rem 0;
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
        <?php if (isset($_GET['success'])): ?>
            <div class="alert success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <h2><?php echo htmlspecialchars($business['name']); ?></h2>
        <div class="business-info">
            <p><strong>Category:</strong> <?php echo htmlspecialchars($business['category_name']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($business['description']); ?></p>
            <p><strong>Contact:</strong> <?php echo htmlspecialchars($business['contact_phone']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($business['address']); ?></p>
            <?php if (!empty($business['website'])): ?>
                <p><strong>Website:</strong> <a href="<?php echo htmlspecialchars($business['website']); ?>" target="_blank"><?php echo htmlspecialchars($business['website']); ?></a></p>
            <?php endif; ?>
        </div>

        <div class="review-section">
            <div class="rating-summary">
                <h3>Customer Reviews</h3>
                <div class="average-rating">
                    <?php
                    $avg_rating = round($avg_rating, 1);
                    $full_stars = floor($avg_rating);
                    $half_star = ($avg_rating - $full_stars) >= 0.5;
                    ?>
                    <div class="star-rating">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <span class="star"><?php echo ($i < $full_stars) ? '★' : ($half_star && $i == $full_stars ? '½' : '☆'); ?></span>
                        <?php endfor; ?>
                        <span>(<?php echo $avg_rating; ?> / 5)</span>
                    </div>
                    <p>Based on <?php echo $review_count; ?> reviews</p>
                </div>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="review-form">
                    <h4>Write a Review</h4>
                    <form action="submit_review.php" method="POST">
                        <input type="hidden" name="business_id" value="<?php echo $business_id; ?>">
                        <div class="rating-stars">
                            <select name="rating" required>
                                <option value="">Select Rating</option>
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> Stars</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <textarea name="review_text" rows="4" placeholder="Write your review..." required></textarea>
                        <button type="submit" class="submit-button">Submit Review</button>
                    </form>
                </div>
            <?php else: ?>
                <p><a href="login.php">Log in</a> to write a review</p>
            <?php endif; ?>

            <div class="reviews-list">
                <?php if (count($reviews) > 0): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <span class="review-author"><?php echo htmlspecialchars($review['username']); ?></span>
                                <div class="review-meta">
                                    <span class="star-rating">
                                        <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?>
                                    </span>
                                    <span class="review-date">
                                        <?php echo date('M j, Y', strtotime($review['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                            <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <form action="manage_reviews.php" method="POST" style="margin-top: 1rem;">
                                    <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                    <button type="submit" class="delete-button">Delete Review</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No reviews yet. Be the first to write one!</p>
                <?php endif; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?business_id=<?php echo $business_id; ?>&page=<?php echo $i; ?>" 
                           class="<?php echo $i == $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online</p>
    </footer>
</body>
</html>