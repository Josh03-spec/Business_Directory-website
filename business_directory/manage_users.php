<?php
session_start();

// Ensure only admin users can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: unauthorized.php");
    exit;
}

include 'db_connection.php';

// Generate CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success_message = "";
$error_message = "";

// Process POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = "Invalid CSRF token.";
    } else {
        // --- Handle Update User ---
        if (isset($_POST['update_user'])) {
            $user_id = intval($_POST['user_id']);
            $username = trim($_POST['username']);
            $role = trim($_POST['role']);

            // Validate allowed roles
            $allowed_roles = ['admin', 'editor', 'business', 'user'];
            if (!in_array($role, $allowed_roles)) {
                $error_message = "Invalid role selected.";
            } else {
                $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE user_id = ?");
                $stmt->bindValue(1, $username, PDO::PARAM_STR);
                $stmt->bindValue(2, $role, PDO::PARAM_STR);
                $stmt->bindValue(3, $user_id, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $success_message = "User updated successfully.";
                } else {
                    $error_message = "Error updating user.";
                }
                $stmt = null;
            }
        }

        // --- Handle Delete User ---
        if (isset($_POST['delete_user'])) {
            $user_id = intval($_POST['user_id']);

            // Prevent deleting the currently logged-in admin
            if ($user_id == $_SESSION['user_id']) {
                $error_message = "You cannot delete yourself.";
            } else {
                $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
                $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $success_message = "User deleted successfully.";
                } else {
                    $error_message = "Error deleting user.";
                }
                $stmt = null;
            }
        }

        // --- Handle Add User ---
        if (isset($_POST['add_user'])) {
            $new_username = trim($_POST['new_username']);
            $new_password = trim($_POST['new_password']);
            $new_role = trim($_POST['new_role']);

            // Initialize error messages for addition
            $username_err = "";
            $password_err = "";
            $role_err = "";

            if (empty($new_username)) {
                $username_err = "Please enter a username.";
            } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $new_username)) {
                $username_err = "Username can only contain letters, numbers, and underscores.";
            } else {
                // Check if username is taken
                $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
                $stmt->execute([$new_username]);
                if ($stmt->rowCount() > 0) {
                    $username_err = "This username is already taken.";
                }
            }

            if (empty($new_password)) {
                $password_err = "Please enter a password.";
            } elseif (strlen($new_password) < 8) {
                $password_err = "Password must be at least 8 characters long.";
            }

            $allowed_roles = ['admin', 'editor', 'business', 'user'];
            if (empty($new_role)) {
                $role_err = "Please select a role.";
            } elseif (!in_array($new_role, $allowed_roles)) {
                $role_err = "Invalid role selected.";
            }

            if (empty($username_err) && empty($password_err) && empty($role_err)) {
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
                $stmt->bindValue(1, $new_username, PDO::PARAM_STR);
                $stmt->bindValue(2, $password_hash, PDO::PARAM_STR);
                $stmt->bindValue(3, $new_role, PDO::PARAM_STR);
                if ($stmt->execute()) {
                    $success_message = "User added successfully.";
                } else {
                    $error_message = "Error adding user.";
                }
            } else {
                $error_message = "Please correct the errors in the form. " . $username_err . " " . $password_err . " " . $role_err;
            }
        }
    }
}

// Fetch all users after processing
$stmt = $conn->prepare("SELECT user_id, username, role FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = null; // Release the statement
$conn = null; // Close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nkozi Online - Manage Users</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Your CSS styles retained from original */
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
        header nav a[href="admin_dashboard.php"]:hover {
            background-color: #28a745;
        }
        header nav a[href="logout.php"]:hover {
            background-color: #dc3545;
        }
        main {
            padding: 2rem;
            flex: 1;
            text-align: center;
        }
        main h2 {
            color: #007BFF;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            text-align: left;
        }
        .users-list {
            margin-top: 2rem;
            text-align: left;
        }
        .users-list table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .users-list table thead th {
            background-color: #f0f4f8;
            color: #333;
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }
        .users-list table tbody td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }
        .users-list table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .users-list table tbody tr:hover {
            background-color: #e9ecef;
        }
        .users-list table td select,
        .users-list table td input[type="text"],
        .users-list table td input[type="email"] {
            padding: 0.5rem;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            width: 150px;
            box-sizing: border-box;
        }
        .users-list table td select:focus,
        .users-list table td input[type="text"]:focus,
        .users-list table td input[type="email"]:focus {
            outline: none;
            border-color: #007BFF;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);
        }
        .users-list table td form {
            display: inline-block;
            margin-right: 0.5rem;
        }
        .users-list table td button {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            border: none;
        }
        .users-list table td button.update {
            background-color: #28a745;
            color: white;
        }
        .users-list table td button.update:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        .users-list table td button.delete {
            background-color: #dc3545;
            color: white;
        }
        .users-list table td button.delete:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
        .error-message {
            color: red;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            text-align: left;
        }
        .success-message {
            color: green;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            text-align: left;
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
        .add-user-form {
            margin-top: 2rem;
            padding: 1.5rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: left;
            width: 100%;
            max-width: 400px;
            margin: auto;
        }
        .add-user-form h3 {
            color: #007BFF;
            margin-bottom: 1rem;
            font-size: 1.2rem;
            text-align: center;
        }
        .add-user-form label {
            display: block;
            margin-top: 1rem;
            font-size: 1rem;
            color: #555;
        }
        .add-user-form input, .add-user-form select {
            width: 100%;
            padding: 0.75rem;
            margin: 0.5rem 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        .add-user-form input:focus, .add-user-form select:focus {
            outline: none;
            border-color: #007BFF;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);
        }
        .add-user-form button {
            padding: 0.75rem 1.5rem;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 1.5rem;
            width: 100%;
            text-align: center;
        }
        .add-user-form button:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        .add-user-form .error-message {
            color: red;
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }
         @media (max-width: 768px) {
            .users-list table {
                display: block;
                overflow-x: auto;
            }
            .users-list table thead,
            .users-list table tbody tr {
                display: table-row;
                width: 100%;
                table-layout: fixed;
            }
            .users-list table thead th,
            .users-list table tbody td {
                display: table-cell;
                width: auto;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            header nav {
                flex-direction: column;
            }
            header nav a{
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
            <a href="admin_dashboard.php">Admin Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <h2>Manage Users</h2>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>
        <div class="users-list">
            <?php if (isset($users) && count($users) > 0): ?>
                <table>
                    <thead>
                        <tr>
                                <!-- Removed ID column header -->
                            <th>Username</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <!-- Update Form -->
                                <form method="POST" action="manage_users.php">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id'] ?? ''); ?>">
                                    <!-- Removed ID column -->
                                    <td>
                                        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>">
                                    </td>
                                    <td>
                                        <select name="role">
                                            <option value="admin" <?php echo (isset($user['role']) && $user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                            <option value="editor" <?php echo (isset($user['role']) && $user['role'] == 'editor') ? 'selected' : ''; ?>>Editor</option>
                                            <option value="business" <?php echo (isset($user['role']) && $user['role'] == 'business') ? 'selected' : ''; ?>>Business</option>
                                            <option value="viewer" <?php echo (isset($user['role']) && $user['role'] == 'viewer') ? 'selected' : ''; ?>>Viewer</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="submit" name="update_user" class="update">Update</button>
                                </form>
                                <!-- Delete Form -->
                                <form method="POST" action="manage_users.php" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id'] ?? ''); ?>">
                                    <button type="submit" name="delete_user" class="delete">Delete</button>
                                </form>
                                    </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No users found.</p>
            <?php endif; ?>
        </div>
        <div class="add-user-form">
            <h3>Add New User</h3>
            <form method="POST" action="manage_users.php">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <label for="new_username">Username:</label>
                <input type="text" id="new_username" name="new_username" required>
                <span class="error-message"><?php echo isset($username_err) ? $username_err : ''; ?></span>

                <label for="new_password">Password:</label>
                <input type="password" id="new_password" name="new_password" required>
                <span class="error-message"><?php echo isset($password_err) ? $password_err : ''; ?></span>

                <label for="new_role">Role:</label>
                <select id="new_role" name="new_role" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="editor">Editor</option>
                    <option value="business">Business</option>
                    <option value="user">User</option>
                </select>
                <span class="error-message"><?php echo isset($role_err) ? $role_err : ''; ?></span>
                <button type="submit" name="add_user">Add User</button>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online</p>
    </footer>
</body>
</html>
