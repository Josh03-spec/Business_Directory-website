<!-- filepath: c:\WAMP_SERVER\www\business_directory\submit_business.php -->
<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $contact_phone = $_POST['contact_phone'];
    $address = $_POST['address'];
    $website = $_POST['website'];

    $stmt = $conn->prepare("INSERT INTO businesses (category_id, user_id, name, description, contact_phone, address, website, is_approved) VALUES (:category_id, :user_id, :name, :description, :contact_phone, :address, :website, FALSE)");
    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':user_id', $user_id); // Assuming you have a way to get the current user ID
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':contact_phone', $contact_phone);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':website', $website);

    if ($stmt->execute()) {
        echo "Business submitted successfully. It will be reviewed for approval.";
    } else {
        echo "Error submitting business.";
    }
}
?>