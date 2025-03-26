<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nkozi Online - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
            color: #333;
            display: flex;
            flex-direction: column; /* Changed to column */
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
        }
        header nav a:hover {
            text-decoration: underline;
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
            flex-direction: column; /* Changed to column */
            justify-content: center;
            align-items: center;
            margin: 2rem auto;
        }

        .login-container h2 {
            color: #007BFF;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .login-form label {
            margin-top: 1rem;
            font-size: 1rem;
            align-self: flex-start;
            color: #555;
        }

        .login-form input {
            padding: 0.75rem;
            margin: 0.5rem 0;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .login-form input:focus {
            outline: none;
            border-color: #007BFF;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);
        }

        .login-form button {
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

        .login-form button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .error-message {
            color: red;
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
            main {
                width: 95%;
            }

            .login-form input {
                width: 100%;
            }

            .login-form button {
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
        <h2>Login</h2>
        <?php if (isset($_GET['error'])): ?>
            <p class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <form class="login-form" action="process_login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
        </form>
        <p style="margin-top: 1rem;">
            <a href="register.php" style="color: #007BFF; text-decoration: none; font-size: 0.9rem;">
                Don't have an account? Register
            </a>
        </p>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online</p>
    </footer>
</body>
</html>
