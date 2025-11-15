<?php 
session_start();  
if (!isset($_SESSION['uname'])) {
    header("location:index.php");
    exit();
}
include "Database.php";

// Generate and store payment token and timestamp
$_SESSION['payment_token'] = bin2hex(random_bytes(16)); // unique token
$_SESSION['payment_start'] = time();

// Calculate total price and get user info
$username = $_SESSION['uname'];
$totalPrice = 0;
$seatsSelected = isset($_POST['seat']) ? $_POST['seat'] : [];
$totalseat = isset($_POST['totalseat']) ? $_POST['totalseat'] : 0;
$movie = isset($_POST['movie']) ? $_POST['movie'] : '';
$show = isset($_POST['show']) ? $_POST['show'] : '';

$userData = mysqli_query($conn,"SELECT * FROM user WHERE username='$username'");
$user = mysqli_fetch_assoc($userData);

// Calculate price per seat
foreach($seatsSelected as $seat) {
    $rowLetter = substr($seat, 0, 1);
    $price = 0;
    switch($rowLetter) {
        case 'A': $price = 300; break;
        case 'B': case 'C': case 'D': case 'E': case 'F': $price = 150; break;
        case 'G': case 'H': case 'I': $price = 100; break;
    }
    $totalPrice += $price;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Page</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <style>
        .front {margin:5px 4px 45px 0;background-color:#EDF979;color:#000;padding:9px 0;border-radius:3px;}
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="text-center mb-4">BOOKING SUMMARY</h2>

    <div class="alert alert-warning text-center">
        Please complete your payment within <strong>10 minutes</strong>.  
        <br>Time remaining: <span id="countdown">10:00</span>
    </div>

    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card p-3">
                <h5>User & Booking Info</h5>
                <p>Username: <?= $user['username'] ?></p>
                <p>Email: <?= $user['email'] ?></p>
                <p>Phone: <?= $user['mobile'] ?></p>
                <p>City: <?= $user['city'] ?></p>
                <p>Movie: <?= $movie ?></p>
                <p>Seats: <?= implode(", ", $seatsSelected) ?></p>
                <p>Total Seats: <?= $totalseat ?></p>
                <p>Show Time: <?= $show ?></p>
                <p>Payment Date: <?= date("d-m-Y") ?></p>
            </div>

            <div class="front mt-3">
                <strong>Total Amount: RM <?= $totalPrice ?></strong>
            </div>

            <div class="card mt-3 p-3">
                <h5>Credit Card Details</h5>
                <div class="form-group">
                    <label>Card Owner</label>
                    <input type="text" id="card_name" class="form-control">
                    <div id="validatecardname" class="text-danger"></div>
                </div>
                <div class="form-group">
                    <label>Card Number</label>
                    <input type="text" id="card_number" class="form-control">
                    <div id="validatecardnumber" class="text-danger"></div>
                </div>
                <div class="row">
                    <div class="col-sm-8">
                        <div class="form-group">
                            <label>Expiration Date</label>
                            <input type="date" id="ex_date" class="form-control">
                            <div id="validateexdate" class="text-danger"></div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>CVV</label>
                            <input type="number" id="cvv" class="form-control">
                            <div id="validatecvv" class="text-danger"></div>
                        </div>
                    </div>
                </div>

                <button id="payment" class="btn btn-primary btn-block mt-3">Confirm Payment</button>
                <div id="msg" class="text-danger mt-2"></div>
            </div>
        </div>
    </div>
</div>

<!-- Expiration Modal -->
<div class="modal fade" id="expireModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content text-center">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title w-100">Session Expired</h5>
      </div>
      <div class="modal-body">
        <p>Your 10-minute payment session has expired.<br>Please restart your booking.</p>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
    let timeLeft = 600; // 10 minutes in seconds
    const countdown = setInterval(function(){
        let minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;
        $("#countdown").text(`${minutes}:${seconds < 10 ? '0' + seconds : seconds}`);
        timeLeft--;

        if (timeLeft < 0) {
            clearInterval(countdown);
            $("#expireModal").modal('show');
            setTimeout(() => { window.location = "index.php"; }, 3000);
        }
    }, 1000);

    $("#payment").click(function(){
        var card_name = $("#card_name").val().trim();
        var card_number = $("#card_number").val().trim();
        var ex_date = $("#ex_date").val().trim();
        var cvv = $("#cvv").val().trim();

        if(card_name == '' || !/^[a-zA-Z ]+$/.test(card_name)){
            $("#validatecardname").html("Card owner name must contain letters only.");
            return false;
        } else { $("#validatecardname").html(''); }

        if(card_number == '' || !/^\d{16,19}$/.test(card_number)){
            $("#validatecardnumber").html("Card number must be 16-19 digits.");
            return false;
        } else { $("#validatecardnumber").html(''); }

        if(ex_date == ''){
            $("#validateexdate").html("Expiration date required.");
            return false;
        } else { $("#validateexdate").html(''); }

        if(cvv == '' || !/^\d{3}$/.test(cvv)){
            $("#validatecvv").html("CVV must be exactly 3 digits.");
            return false;
        } else { $("#validatecvv").html(''); }

        $.ajax({
            url:'payment_form.php',
            type:'post',
            data:{
                movie: "<?= $movie ?>",
                show: "<?= $show ?>",
                seat: "<?= implode(',', $seatsSelected) ?>",
                totalseat: "<?= $totalseat ?>",
                price: "<?= $totalPrice ?>",
                card_name: card_name,
                card_number: card_number,
                ex_date: ex_date,
                cvv: cvv,
                token: "<?= $_SESSION['payment_token'] ?>"
            },
            success:function(response){
                if(response == 1){
                    window.location = "tickes.php";
                } else if (response.includes("expired")) {
                    $("#expireModal").modal('show');
                    setTimeout(() => { window.location = "index.php"; }, 3000);
                } else {
                    $("#msg").html(response);
                }
            }
        });
    });
});
</script>

</body>
</html>
