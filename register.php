<?php
include_once "Database.php";
session_start();

if (isset($_POST['submit'])) {

    // SQL Injection Prevention
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile   = mysqli_real_escape_string($conn, $_POST['number']);
    $city     = mysqli_real_escape_string($conn, $_POST['city']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // File name sanitizing
    $filename = mysqli_real_escape_string($conn, $_FILES['image']['name']);
    $location = 'admin/image/' . $filename;

    // File validation
    $file_extension = strtolower(pathinfo($location, PATHINFO_EXTENSION));
    $image_ext = array('jpg', 'png', 'jpeg', 'gif');
    $response = 0;

    if (in_array($file_extension, $image_ext)) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $location)) {
            $response = $location;
        }
    }

    echo $response;

    // SECURE ARGON2ID HASHING
    $argonOptions = [
        'memory_cost' => 65536,
        'time_cost'   => 2,
        'threads'     => 4,
        'hash_length' => 16,
        'salt_length' => 16
    ];

    $hashedPassword = password_hash(
        $password,
        PASSWORD_ARGON2ID,
        $argonOptions
    );

    // Insert user record (SQL Injection Prevented)
    $insert_record = mysqli_query(
        $conn,
        "INSERT INTO user (`username`, `email`, `mobile`, `city`, `password`, `image`)
         VALUES ('$username', '$email', '$mobile', '$city', '$hashedPassword', '$filename')"
    );

    if (!$insert_record) {
        echo "not inserted";
    } else {
        echo "<script>
                alert('Registration successful!');
                window.location = 'login_form.php';
              </script>";
    }
}
?>