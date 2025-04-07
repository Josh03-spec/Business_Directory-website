<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Access Denied</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color:rgb(27, 209, 194);
            color: #721c24;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color:rgb(198, 245, 223);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        a {
            color: #721c24;
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Unauthorized Access</h1>
        <p>You do not have permission to view this page.</p>
        <p><a href="index.php">Go back to home</a></p>
    </div>
</body>
</html>
