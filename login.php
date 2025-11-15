<?php
include "Database.php";
session_start();

// Check for empty fields
if (empty($_POST['username']) || empty($_POST['password'])) {
    foreach ($_POST as $key => $value) {
        if (empty($value)) {
            echo "<li>Please enter " . htmlspecialchars($key) . ".</li>";
        }
    }
    exit();
}

$uname = mysqli_real_escape_string($conn, $_POST['username']);
$password = $_POST['password']; // plaintext password entered by user

// Retrieve the hash from the database
$stmt = $conn->prepare("SELECT password FROM user WHERE username = ?");
$stmt->bind_param("s", $uname);
$stmt->execute();
$result = $stmt->get_result();

// Check if username exists
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $stored_hash = $row['password'];

    // Verify Argon2id password hash
    if (password_verify($password, $stored_hash)) {
        $_SESSION['uname'] = $uname;
        echo "success"; // success
    } else {
        echo "<li>Invalid username or password.</li>";
    }
} else {
    echo "<li>Invalid username or password.</li>";
}

$stmt->close();
$conn->close();
?>