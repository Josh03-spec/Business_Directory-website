<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch users from the database
try {
    $stmt = $conn->prepare("SELECT user_id, username, role FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error_message = "An error occurred while fetching users.";
}

// Handle role update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    // Validate the new role
    $allowed_roles = ['admin', 'editor', 'business', 'user'];
    if (!in_array($new_role, $allowed_roles)) {
        $error_message = "Invalid role selected.";
    } else {
        try {
            $stmt = $conn->prepare("UPDATE users SET role = :role WHERE user_id = :user_id");
            $stmt->bindParam(':role', $new_role);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            // Refresh the user list after updating
            $stmt = $conn->prepare("SELECT user_id, username, role FROM users");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $success_message = "User role updated successfully.";
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $error_message = "An error occurred while updating user role.";
        }
    }
}

// Handle user deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    // Prevent deleting the currently logged-in admin
    if ($user_id == $_SESSION['user_id']) {
        $error_message = "You cannot delete yourself.";
    } else {
        try {
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
             // Refresh the user list after deleting
            $stmt = $conn->prepare("SELECT user_id, username, role FROM users");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $success_message = "User deleted successfully.";
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $error_message = "An error occurred while deleting user.";
        }
    }
}

// Handle user addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $new_username = $_POST['new_username'];
    $new_password = $_POST['new_password'];
    $new_role = $_POST['new_role'];

    // Validate the new user data
    $username_err = "";
    $password_err = "";
    $role_err = "";

     if (empty(trim($new_username))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($new_username))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        // Check if the username is already taken
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = :username");
        $trimmed_username = trim($new_username); // Store the trimmed value in a variable
        $stmt->bindParam(':username', $trimmed_username); // Pass the variable to bindParam()
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $username_err = "This username is already taken.";
        }
    }

    if (empty(trim($new_password))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($new_password)) < 8) {
        $password_err = "Password must be at least 8 characters long.";
    }

    if (empty($new_role)) {
        $role_err = "Please select a role.";
    }
    
     $allowed_roles = ['admin', 'editor', 'business', 'user'];
    if (!in_array($new_role, $allowed_roles)) {
        $role_err = "Invalid role selected.";
    }

    if (empty($username_err) && empty($password_err) && empty($role_err)) {
        // Hash the password
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        try {
            // Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (:username, :password_hash, :role)");
            $stmt->bindParam(':username', $new_username);
            $stmt->bindParam(':password_hash', $password_hash);
            $stmt->bindParam(':role', $new_role);
            $stmt->execute();

            $success_message = "User added successfully.";
             // Refresh the user list after adding new user
            $stmt = $conn->prepare("SELECT user_id, username, role FROM users");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
           

        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $error_message = "An error occurred while adding user.";
        }
    }else{
         $error_message = "Please correct the errors in the form.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nkozi Online - Manage Users</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
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
        header nav a[href="admin_dashboard.php"]:hover {
            background-color: #28a745; /* Green on hover */
        }
        header nav a[href="logout.php"]:hover {
            background-color: #dc3545; /* Red on hover */
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
            overflow: hidden; /* for rounded corners with box-shadow */
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
        .users-list table td select {
            padding: 0.5rem;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            width: 150px;
        }
        .users-list table td select:focus {
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
            margin-left: auto;
            margin-right: auto;
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
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <?php if (isset($success_message)): ?>
            <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>
        <div class="users-list">
            <?php if (isset($users) && count($users) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td>
                                    <form method="POST" action="manage_users.php">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                                        <select name="role">
                                            <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                            <option value="editor" <?php echo ($user['role'] == 'editor') ? 'selected' : ''; ?>>Editor</option>
                                            <option value="business" <?php echo ($user['role'] == 'business') ? 'selected' : ''; ?>>Business</option>
                                             <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                                        </select>
                                        <button type="submit" name="update_role" class="update">Update</button>
                                    </form>
                                    <form method="POST" action="manage_users.php">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                                        <button type="submit" name="delete_user" class="delete" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
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
                <span class="error-message"><?php echo isset($role_err) ? $role_err: ''; ?></span>
                <button type="submit" name="add_user">Add User</button>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2025 Nkozi Online</p>
    </footer>
</body>
</html>
