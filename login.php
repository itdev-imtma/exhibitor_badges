<?php
    session_start();

    // Simulate a password validation based on hall selection
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Map halls to their correct passwords
        $password = "test@123";

        // Get POST data
        $enteredPassword = $_POST['password'];

        if ($enteredPassword === $password) {
            $_SESSION['user'] = $_POST['username'];
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid password.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Exhibitors Badges - Login</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="Admin template that can be used to build dashboards for CRM, CMS, etc." />
    <meta name="author" content="Potenza Global Solutions" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- app favicon -->
    <link rel="shortcut icon" href="assets/img/imtex_favicon.png">
    <!-- google fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- plugin stylesheets -->
    <link rel="stylesheet" type="text/css" href="assets/css/vendors.css" />
    <!-- app style -->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css" />
</head>

<body class="bg-white">
    <!-- begin app -->
    <div class="app">
        <!-- begin app-wrap -->
        <div class="app-wrap">
            <!-- begin pre-loader -->
            <div class="loader">
                <div class="h-100 d-flex justify-content-center">
                    <div class="align-self-center">
                        <img src="assets/img/loader/loader.svg" alt="loader">
                    </div>
                </div>
            </div>
            <!-- end pre-loader -->

            <!--start login content-->
            <div class="app-content">
                <div class="bg-white">
                    <div class="container-fluid p-0">
                        <div class="row no-gutters">
                            <div class="col-sm-6 col-lg-5 col-xxl-3 align-self-center order-2 order-sm-1">
                                <div class="d-flex align-items-center h-100-vh">
                                    <div class="login p-50">
                                        <h1 class="mb-2">Welcome to Exhibitor Badges</h1>
                                        <p>Welcome back, please login to your account.</p>
                                        
                                        <!-- Display error message if session error exists -->
                                        <?php if (isset($_SESSION['error'])): ?>
                                            <div class="alert alert-danger">
                                                <?php echo $_SESSION['error']; ?>
                                            </div>
                                            <?php unset($_SESSION['error']); ?>
                                        <?php endif; ?>

                                        <!-- Form submits to the same page -->
                                        <form action="" method="POST" class="mt-3 mt-sm-5">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="control-label">User Name*</label>
                                                        <input type="text" name="username" class="form-control" id="username" placeholder="Username" value="accounts" readonly/>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">Password*</label>
                                                        <input type="password" name="password" class="form-control" id="password" placeholder="Password" required/>
                                                        <input type="checkbox" onclick="togglePassword()"/> Show Password
                                                    </div>
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <button type="submit" class="btn btn-primary text-uppercase">Sign In</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xxl-9 col-lg-7 bg-gradient o-hidden order-1 order-sm-2">
                                <div class="row align-items-center h-100">
                                    <div class="col-12 mx-auto ">
                                        <img class="img-fluid" src="assets/img/bg/IMTEX.jpeg" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end login content-->
        </div>
        <!-- end app-wrap -->
    </div>
    <!-- end app -->

    <!-- plugins -->
    <script src="assets/js/vendors.js"></script>

    <!-- custom app -->
    <script src="assets/js/app.js"></script>
    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var toggleIcon = document.getElementById("toggle-icon");

            // Toggle the password field type
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }

        $.ajax({
                            
                    url: "https://visitor-registration.imtma.in/get-otp", // URL to send the request to
                    method: 'POST',
                    data:formData,
                    processData: false, // Prevent jQuery from automatically processing the data
                    contentType: false, // Prevent jQuery from setting the content type
                    success: function(response) {
                        // Handle the response from the server
                        //alert('Form submitted successfully: ' + response.message);
                        //console.log(response)
                        if(response.status === true) {
                            $('#get-otp').hide();
                            $('#vis_id').val(response.response);
                        	setTimeout(() => {
                                $('#get-otp').show();
                                $('#get-otp').html('Resend OTP');
                            },60000);   
                            
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors
                        alert('Form submission failed: ' + xhr.responseText);
                    }
                });
    </script>
</body>
</html>
