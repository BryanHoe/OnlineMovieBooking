<?php
// AJAX USERNAME CHECK (same file)
if (isset($_POST['username_check'])) {
    include "Database.php";
    $uname = mysqli_real_escape_string($conn, $_POST['username_check']);

    $query = mysqli_query($conn, "SELECT * FROM user WHERE username='$uname'");
    echo (mysqli_num_rows($query) > 0) ? "taken" : "available";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Responsive Registration Form | CodingLab</title>

    <link rel="stylesheet" href="css/register.css">
    <script src="js/jquery.min.js"></script>

    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
          crossorigin="anonymous">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="container">
    <center><a href="./index.html">
        <img src="img/logo.png" alt="" style="margin-top: 80px; width: 50%;">
    </a></center>

    <div class="title">Registration</div>

    <div class="content">
        <form id="form" action="register.php" method="post"
              enctype="multipart/form-data" onsubmit="return validate();">

            <div class="user-details">

                <!-- USERNAME -->
                <div class="input-box">
                    <span class="details">UserName</span>
                    <input type="text" id="username" name="username" placeholder="Enter your name">
                    <p id="nameerror"></p>
                    <p id="userCheck"></p>
                </div>

                <!-- EMAIL -->
                <div class="input-box">
                    <span class="details">Email</span>
                    <input type="text" id="email" name="email" placeholder="Enter your Email">
                    <p id="emailerror"></p>
                </div>

                <!-- PHONE -->
                <div class="input-box">
                    <span class="details">Phone Number</span>
                    <input type="text" id="number" name="number" placeholder="Enter your Phone Number">
                    <p id="numbererror"></p>
                </div>

                <!-- CITY -->
                <div class="input-box">
                    <span class="details">City</span>
                    <input type="text" id="city" name="city" placeholder="Enter your City">
                    <p id="cityerror"></p>
                </div>

                <!-- PASSWORD -->
                <div class="input-box">
                    <span class="details">Password</span>
                    <input type="password" id="password" name="password" placeholder="Enter your password">
                    <p id="passworderror"></p>
                </div>

                <!-- CONFIRM PASSWORD -->
                <div class="input-box">
                    <span class="details">Confirm Password</span>
                    <input type="password" id="cpassword" name="cpassword" placeholder="Confirm your password">
                    <p id="cpassworderror"></p>
                </div>

                <!-- IMAGE UPLOAD -->
                <div class="input-box">
                    <span class="details">Image uploaded (Option)</span>
                    <input type="file" id="image" name="image">
                </div>

            </div>

            <p id="error_para"></p>
            <div id="err"></div>

            <div class="button">
                <input type="submit" value="Register" id="submit" name="submit">
            </div>

        </form>
    </div>

</div>

<!--   FRONTEND VALIDATION        -->

<script type="text/javascript">
function validate() {
    var error = "";
    var name = document.getElementById("username");
    var email = document.getElementById("email");
    var number = document.getElementById("number");
    var city = document.getElementById("city");
    var password = document.getElementById("password");
    var cpassword = document.getElementById("cpassword");

    // Username validation
    if (name.value.trim() === "") {
        document.getElementById("nameerror").innerHTML =
            "<font color='red'>Require Username Field.</font>";
        return false;
    } else {
        document.getElementById("nameerror").innerHTML = "";
    }

    // Prevent submit if username is taken
    if ($("#userCheck").attr("data-status") === "taken") {
        alert("Username already exists. Please choose another.");
        return false;
    }

    // Email validation
    if (email.value.trim() === "") {
        document.getElementById("emailerror").innerHTML =
            "<font color='red'>Require Email Field.</font>";
        return false;
    }

    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email.value)) {
        document.getElementById("emailerror").innerHTML =
            "<font color='red'>Invalid Email Format.</font>";
        return false;
    }

    // Phone validation
    if (number.value.trim() === "") {
        document.getElementById("numbererror").innerHTML =
            "<font color='red'>Require Phone Number Field.</font>";
        return false;
    }

    var phoneRegex = /^01\d{8,9}$/;
    if (!phoneRegex.test(number.value)) {
        document.getElementById("numbererror").innerHTML =
            "<font color='red'>Phone number must start with 01 and be 10 to 11 digits long.</font>";
        return false;
    }

    // City validation
    if (city.value.trim() === "") {
        document.getElementById("cityerror").innerHTML =
            "<font color='red'>Require City Field.</font>";
        return false;
    }

    // Password validation
    if (password.value.trim() === "") {
        document.getElementById("passworderror").innerHTML =
            "<font color='red'>Require Password Field.</font>";
        return false;
    }

    var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
    if (!passwordRegex.test(password.value)) {
        document.getElementById("passworderror").innerHTML =
            "<font color='red'>Password must be at least 8 characters, include upper/lowercase, number, and special character.</font>";
        return false;
    }

    // Confirm password
    if (password.value !== cpassword.value) {
        document.getElementById("cpassworderror").innerHTML =
            "<font color='red'>Password and Confirm Password do not match.</font>";
        return false;
    }

    return true;
}
</script>

<!-- AJAX: Username live checking -->
<script>
$(document).ready(function () {

    $("#username").keyup(function () {
        var uname = $(this).val().trim();

        if (uname.length < 3) {
            $("#userCheck").html("<font color='red'>Username too short.</font>");
            $("#userCheck").attr("data-status", "short");
            return;
        }

        $.ajax({
            url: "register_form.php",
            type: "POST",
            data: { username_check: uname },
            success: function (data) {
                if (data === "taken") {
                    $("#userCheck").html("<font color='red'>Username already taken.</font>");
                    $("#userCheck").attr("data-status", "taken");
                } else {
                    $("#userCheck").html("<font color='green'>Username available.</font>");
                    $("#userCheck").attr("data-status", "available");
                }
            }
        });
    });

});
</script>

</body>
</html>
