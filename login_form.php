<html>
<head>
<title>Login Page</title>
<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="site.css" />
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
<div class="parent-container">
    <table width="100%" height="100%">
        <tr>
            <td align="center" valign="middle">
                <div class="loginholder">
                    <table style="background-color:white;" class="table-condensed">
                        <tr>
                            <a href="./index.html"><img src="img/logo.png" alt="" width="180px"></a>
                        </tr>
                        <tr>
                            <td><b>Username:</b></td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" class="inputbox" id="username"/>
                                <br><p id="nameerror"></p>
                            </td>
                        </tr>
                        <tr>
                            <td><b>Password:</b></td>
                        </tr>
                        <tr>
                            <td>
                                <input type="password" class="inputbox" id="password" />
                                <br><p id="passerror"></p>
                                <div id="msg"></div>
                            </td>
                        </tr>
                        <tr>
                            <td align="center"><br />
                                <button class="btn-normal" id="login">LOGIN</button>
                            </td>
                        </tr>
                        <tr>
                            <td align="left"><br />
                                <span class="forgetpassword"><a href="forget_password.php"> Forget your Password ?</a></span>
                            </td>
                        </tr>
                        <tr>
                            <td><a href="register_form.php"> Register now</a></td>
                        </tr>
                        <tr>
                            <td><hr style="background-color:blue;height:1px;margin:0px;"/></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $("#login").click(function(e){
        e.preventDefault(); // prevent form submit
        var username = $("#username").val().trim();
        var password = $("#password").val().trim();

        // Validate username
        if(username === "") {
            $("#nameerror").html("<font color='red'>Username is required.</font>");
            return false;
        } else {
            $("#nameerror").html("");
        }

        // Validate password
        if(password === "") {
            $("#passerror").html("<font color='red'>Password is required.</font>");
            return false;
        } else {
            $("#passerror").html("");
        }

        // AJAX request
        $.ajax({
            url: 'login.php',
            type: 'POST',
            data: {username: username, password: password},
            success: function(response){
                if(response.trim() === "success"){
                    alert("Login Successful!");
                    window.location = "index.php";
                } else {
                    $("#msg").html("<font color='red'>Invalid username or password.</font>");
                }
            }
        });
    });
});
</script>
</body>
</html>
