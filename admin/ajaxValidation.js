$(document).ready(function () {
    $("#checkValidation").click(function () {
        var username = $("#userEmail").val().trim();
        var password = $("#userPassword").val().trim();
        var error = "";

        // Validate username
        if (username === "") {
            error = "<font color='red'>Username is required.</font>";
            $("#message").html(error);
            return false; // stop execution
        }

        // Validate password
        if (password === "") {
            error = "<font color='red'>Password is required.</font>";
            $("#message").html(error);
            return false; // stop execution
        }

        // If validation passes, proceed with AJAX
        $.ajax({
            url: 'showData.php',
            type: 'post',
            data: { username: username, password: password },
            success: function (response) {
                if (response == 1) {
                    window.location = "index.php";
                } else {
                    $("#message").html("<font color='red'>Invalid username or password.</font>");
                }
            }
        });
    });
});

