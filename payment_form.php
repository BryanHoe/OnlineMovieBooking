<?php
session_start();
include "Database.php";

//  Check session token & expiry
if (!isset($_POST['token']) || !isset($_SESSION['payment_token'])) {
    echo "Invalid payment request (missing token).";
    exit();
}

if ($_POST['token'] !== $_SESSION['payment_token']) {
    echo "Invalid session token.";
    exit();
}

if (time() - $_SESSION['payment_start'] > 600) { // 10 minutes
    unset($_SESSION['payment_token']);
    echo "Payment session expired. Please restart the payment process.";
    exit();
}

// Validate required fields
if (!empty($_POST['card_name']) && !empty($_POST['card_number']) && !empty($_POST['ex_date'])) {

    $movie = mysqli_real_escape_string($conn, $_POST['movie']);
    $show = mysqli_real_escape_string($conn, $_POST['show']);
    $seat = mysqli_real_escape_string($conn, $_POST['seat']);
    $totalseat = mysqli_real_escape_string($conn, $_POST['totalseat']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $card_name = mysqli_real_escape_string($conn, $_POST['card_name']);
    $card_number = mysqli_real_escape_string($conn, $_POST['card_number']);
    $ex_date = mysqli_real_escape_string($conn, $_POST['ex_date']);

    // Basic validations
    if (!preg_match("/^[a-zA-Z ]+$/", $card_name)) { echo "Card owner name must contain letters only."; exit(); }
    if (!preg_match("/^\d{16,19}$/", $card_number)) { echo "Card number must be 16-19 digits."; exit(); }

    // Retrieve user info
    $username = $_SESSION['uname'];
    $userData = mysqli_query($conn, "SELECT * FROM user WHERE username='$username'");
    $user = mysqli_fetch_assoc($userData);
    $uid = $user['id'];

    $custemer_id = mt_rand();
    $payment_date = date("Y-m-d");
    $booking_date = date("Y-m-d", strtotime('tomorrow'));
    $_SESSION['custemer_id'] = $custemer_id;

    // AES-256-GCM Encryption for sensitive info (card owner)
    $key = random_bytes(32); 
    $iv = random_bytes(12); // unique IV
    $tag = null;

    $enc_card_name = openssl_encrypt($card_name, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    $enc_card_name_db = base64_encode($enc_card_name);
    $iv_db = base64_encode($iv);
    $tag_db = base64_encode($tag);

    // Store only the last 4 digits of the card number
    $last4_card_number = substr($card_number, -4);

    // Insert into database
    $insert = mysqli_query($conn, "
        INSERT INTO customers
        (uid, movie, show_time, seat, totalseat, price, payment_date, booking_date, card_name, card_number, ex_date, custemer_id, enc_iv, enc_tag)
        VALUES
        ('$uid','$movie','$show','$seat','$totalseat','$price','$payment_date','$booking_date','$enc_card_name_db','$last4_card_number','$ex_date','$custemer_id','$iv_db','$tag_db')
    ");

    if ($insert) {
        unset($_SESSION['payment_token']);
        unset($_SESSION['payment_start']);
        echo 1; // success
    } else {
        echo "Failed to process payment.";
    }

} else {
    echo "All fields are required.";
}
?>
