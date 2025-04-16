<?php
include 'db_connection.php'; // Include database connection

// Start session if not already started (needed for user ID if logged in)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize variables
$error = '';
$success = '';
$business_id = null;
$business_name = ''; // To display on the form page

// --- Handle POST Request (Form Submission) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $business_id = filter_input(INPUT_POST, 'business_id', FILTER_VALIDATE_INT);

    // --- IMPORTANT: Get user ID from session ---
    // Assuming you store user ID in session upon login
    if (!isset($_SESSION['user_id'])) {
        // Redirect to login or handle not-logged-in users
        // For now, let's set an error and stop
        // In a real app, you'd redirect: header('Location: login.php'); exit;
         $error = "You must be logged in to submit a review.";
         // We still need business_id for the redirect below, try to get it if possible
         if (!$business_id) {
             $business_id = filter_input(INPUT_GET, 'business_id', FILTER_VALIDATE_INT); // Fallback for redirect
         }
         // Redirect back with error even if not logged in
         $redirect_url = "business_detail.php?business_id=" . urlencode($business_id ?? '');
         $redirect_url .= "&error=" . urlencode($error);
         header("Location: $redirect_url");
         exit;
    }
    $user_id = $_SESSION['user_id']; // Get logged-in user's ID

    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 5]
    ]);
    $review_text = trim($_POST['review_text'] ?? '');

    // Validate inputs
    if (!$business_id || !$rating) {
        $error = "Invalid input data. Please select a rating.";
    } elseif (empty($review_text)) {
        $error = "Review text cannot be empty.";
    } else {
        try {
            // Check if the user has already reviewed this business (optional but good practice)
            $check_stmt = $conn->prepare("SELECT review_id FROM reviews WHERE business_id = :business_id AND user_id = :user_id");
            $check_stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
            $check_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $check_stmt->execute();

            if ($check_stmt->fetch()) {
                $error = "You have already submitted a review for this business.";
            } else {
                // Insert review into database using user_id
                $stmt = $conn->prepare("
                    INSERT INTO reviews
                    (business_id, user_id, rating, review_text)
                    VALUES (:business_id, :user_id, :rating, :review_text)
                ");

                $stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); // Use user_id from session
                $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
                $stmt->bindParam(':review_text', $review_text);

                if ($stmt->execute()) {
                    // Update business average rating (function defined below or in db_connection.php)
                    update_business_rating($conn, $business_id);
                    $success = "Review submitted successfully!";
                } else {
                    $error = "Error submitting review. Please try again.";
                }
            }
        } catch (PDOException $e) {
            // Log the error instead of showing detailed message to user in production
            error_log("Database error in submit_review.php: " . $e->getMessage());
            $error = "An unexpected database error occurred. Please try again later.";
        }
    }

    // Redirect back to business page with status message AFTER processing POST
    $redirect_url = "business_detail.php?business_id=" . urlencode($business_id ?? '');
    if (!empty($error)) {
        $redirect_url .= "&error=" . urlencode($error);
    } elseif (!empty($success)) {
        $redirect_url .= "&success=" . urlencode($success);
    }
    header("Location: $redirect_url");
    exit;
}

// --- Handle GET Request (Display Form) ---
else {
    // Get business_id from the URL query string
    $business_id = filter_input(INPUT_GET, 'business_id', FILTER_VALIDATE_INT);

    if (!$business_id) {
        die("Business ID not provided or invalid."); // Stop if no valid ID
    }

    // Fetch business name to display on the page
    try {
        $stmt = $conn->prepare("SELECT name FROM businesses WHERE business_id = :business_id AND is_approved = TRUE");
        $stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
        $stmt->execute();
        $business = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($business) {
            $business_name = $business['name'];
        } else {
            die("Business not found or not approved."); // Stop if business doesn't exist
        }
    } catch (PDOException $e) {
         error_log("Database error fetching business name in submit_review.php: " . $e->getMessage());
         die("Error fetching business details.");
    }

     // --- Check if user is logged in ---
     if (!isset($_SESSION['user_id'])) {
         // Redirect to login page, passing the intended destination
         $return_url = urlencode("submit_review.php?business_id=" . $business_id);
         header("Location: login.php?redirect=" . $return_url . "&message=" . urlencode("Please log in to submit a review."));
         exit;
     }
}

// Function to update business rating (can be moved to a shared file)
function update_business_rating($conn, $business_id) {
    try {
        // Calculate new average rating and count
        $rating_stmt = $conn->prepare("
            SELECT AVG(rating) as avg_rating, COUNT(*) as review_count
            FROM reviews
            WHERE business_id = :business_id
        ");
        $rating_stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
        $rating_stmt->execute();
        $rating_info = $rating_stmt->fetch(PDO::FETCH_ASSOC);

        $avg_rating = $rating_info['avg_rating'] ?? 0;
        $review_count = $rating_info['review_count'] ?? 0;

        // Update the businesses table
        $update_stmt = $conn->prepare("
            UPDATE businesses
            SET avg_rating = :avg_rating, review_count = :review_count
            WHERE business_id = :business_id
        ");
        $update_stmt->bindParam(':avg_rating', $avg_rating); // PDO determines type
        $update_stmt->bindParam(':review_count', $review_count, PDO::PARAM_INT);
        $update_stmt->bindParam(':business_id', $business_id, PDO::PARAM_INT);
        $update_stmt->execute();

    } catch (PDOException $e) {
        // Log error, don't stop execution if rating update fails
         error_log("Failed to update business rating for business_id {$business_id}: " . $e->getMessage());
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review for <?php echo htmlspecialchars($business_name); ?> - Nkozi Online</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        /* Reusing styles similar to business_detail.php for consistency */
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
            max-width: 800px; /* Adjusted max-width for form */
            margin: 2rem auto; /* Added top margin */
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
        .alert.error { /* Only need error display here potentially */
            background-color: #f8d7da;
            color: #842029;
            border-color: #f5c2c7;
        }
        .review-form-container {
            padding: 1.5rem;
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0; /* Added border */
        }
        .form-group {
            margin-bottom: 1.5rem; /* Increased spacing */
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600; /* Bolder labels */
            color: #4a5568; /* Label color */
        }
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem 1rem; /* Adjusted padding */
            border: 1px solid #cbd5e0; /* Slightly darker border */
            border-radius: 0.375rem; /* Standard border radius */
            font-family: inherit;
            font-size: 1rem; /* Standard font size */
            box-sizing: border-box; /* Include padding and border in element's total width and height */
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out; /* Added transition */
        }
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #007BFF; /* Highlight focus */
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25); /* Focus ring */
            outline: none; /* Remove default outline */
        }
        .form-group textarea {
            min-height: 120px; /* Minimum height for textarea */
            resize: vertical; /* Allow vertical resizing */
        }
        .rating-stars {
            display: inline-block; /* Keep label and select together */
        }
        .submit-button {
            background-color: #007BFF;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500; /* Medium font weight */
            transition: background-color 0.3s ease;
            display: block; /* Make button block level */
            width: 100%; /* Full width */
            margin-top: 1rem; /* Add margin top */
        }
        .submit-button:hover {
            background-color: #0056b3;
        }
        .cancel-link {
             display: block;
             text-align: center;
             margin-top: 1rem;
             color: #007BFF;
             text-decoration: none;
        }
         .cancel-link:hover {
             text-decoration: underline;
         }
         h2 {
             text-align: center;
             margin-bottom: 1.5rem;
             color: #1a202c; /* Darker heading */
         }
         @media (max-width: 768px) {
             header nav {
                 flex-direction: column;
                 align-items: center;
             }
             header nav a {
                 margin: 0.5rem 0;
             }
             main {
                 padding: 1rem;
                 margin: 1rem auto;
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
        <h2>Submit Your Review for <?php echo htmlspecialchars($business_name); ?></h2>

        <?php if (!empty($error)): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
            <?php /* Note: Success messages are handled by redirecting back to business_detail.php */ ?>
        <?php endif; ?>

        <div class="review-form-container">
            <form action="submit_review.php" method="POST">
                <input type="hidden" name="business_id" value="<?php echo htmlspecialchars($business_id); ?>">

                 <div class="form-group rating-stars">
                    <label for="rating">Rating:</label>
                    <select name="rating" id="rating" required>
                        <option value="">Select a rating</option>
                        <option value="5">★★★★★ (Excellent)</option>
                        <option value="4">★★★★☆ (Good)</option>
                        <option value="3">★★★☆☆ (Average)</option>
                        <option value="2">★★☆☆☆ (Fair)</option>
                        <option value="1">★☆☆☆☆ (Poor)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="review_text">Your Review:</label>
                    <textarea name="review_text" id="review_text" rows="5" required placeholder="Share your experience..."></textarea>
                </div>

                <button type="submit" class="submit-button">Submit Review</button>
                 <a href="business_detail.php?business_id=<?php echo htmlspecialchars($business_id); ?>" class="cancel-link">Cancel</a>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Nkozi Online</p>
    </footer>
</body>
</html>

