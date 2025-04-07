<?php
include 'db_connection.php';

// Initialize variables for form data and error messages
$username = "";
$password = "";
$confirm_password = "";
$username_err = "";
$password_err = "";
$confirm_password_err = "";
$general_err = "";
$registration_success = false; // Added for success message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    $trimmed_username = trim($_POST["username"]);
    if (empty($trimmed_username)) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $trimmed_username)) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        // Check if the username is already taken
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = :username");
        $stmt->bindParam(':username', $trimmed_username);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $username_err = "This username is already taken.";
        } else {
            $username = $trimmed_username;
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 8) {
        $password_err = "Password must be at least 8 characters long.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm your password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password != $confirm_password) {
            $confirm_password_err = "Passwords do not match.";
        }
    }

    // If there are no errors, proceed with registration
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password_hash', $password_hash);
            $stmt->execute();
            $registration_success = true;
        } catch (PDOException $e) {
            $general_err = "An error occurred during registration.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nkozi Online - Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
      body {
        font-family: 'Inter', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f0f4f8;
        color: #333;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        transition: background-color 0.5s ease;
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
        background-color: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 90%;
        max-width: 400px;
        text-align: center;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin: 2rem auto;
      }

      .register-container h2 {
        color: #007BFF;
        margin-bottom: 1.5rem;
        font-size: 1.8rem;
      }

      .register-form {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
      }

      .register-form label {
        margin-top: 1rem;
        font-size: 1rem;
        align-self: flex-start;
        color: #555;
      }

      .register-form input {
        padding: 0.75rem;
        margin: 0.5rem 0;
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
      }

      .register-form input:focus {
        outline: none;
        border-color: #007BFF;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);
      }

      .register-form button {
        padding: 0.75rem 1.5rem;
        background-color: #007BFF;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 1.1rem;
        cursor: pointer;
        margin-top: 1.5rem;
        transition: background-color 0.3s ease, transform 0.2s ease;
        width: 100%;
      }

      .register-form button:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
      }

      .error-message {
        color: red;
        margin-top: 1rem;
        font-size: 0.9rem;
        text-align: center;
      }

      .success-message {
        color: green;
        margin-top: 1rem;
        font-size: 0.9rem;
        text-align: center;
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

      @media (max-width: 768px) {
        .register-container {
          width: 95%;
        }

        .register-form input {
          width: 100%;
        }

        .register-form button {
          width: 100%;
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
        </nav>
    </header>
    <main>
        <div class="register-container">
            <h2>Register</h2>
            <?php if ($registration_success): ?>
                <p class="success-message">Registration successful!  You can now <a href="login.php" style="color: #007BFF; text-decoration: none;">login</a>.</p>
            <?php else: ?>
                <?php if (!empty($general_err)): ?>
                    <p class="error-message"><?php echo htmlspecialchars($general_err); ?></p>
                <?php endif; ?>
                <form class="register-form" action="register.php" method="POST">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    <span class="error-message"><?php echo $username_err; ?></span>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <span class="error-message"><?php echo $password_err; ?></span>
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <span class="error-message"><?php echo $confirm_password_err; ?></span>
                    <button type="submit">Register</button>
                </form>
                <p style="margin-top: 1rem;">
                    <a href="login.php" style="color: #007BFF; text-decoration: none; font-size: 0.9rem;">
                        Already have an account? Login
                    </a>
                </p>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online</p>
    </footer>
</body>
</html>
