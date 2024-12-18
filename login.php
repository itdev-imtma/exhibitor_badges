<?php
    session_start();

    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';
    require 'phpmailer/src/Exception.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $fixedEmail = "naveenabs@imtma.in";

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['get_otp'])) {
        $username = "Naveen";

        // Generate a 6-digit OTP
        $otp = rand(100000, 999999);

        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'mail.imtex.in';
            $mail->SMTPAuth = true;
            $mail->Username = 'exhibitorbadges@imtex.in';
            $mail->Password = 'P?w5(gesjWO{';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('exhibitorbadges@imtex.in', 'IMTEX 2025');
            $mail->addAddress($fixedEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body    = "Dear $username,<br><br>Your OTP code is: <strong>$otp</strong>";

            // Send email
            $mail->send();

            $_SESSION['otp'] = $otp;
            $_SESSION['otp_sent'] = true;

        } catch (Exception $e) {
            $_SESSION['error'] = "Failed to send OTP: " . $mail->ErrorInfo;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_otp'])) {
        $enteredOtp = $_POST['otp'];

        if ($_SESSION['otp'] == $enteredOtp) {
            $_SESSION['user'] = $fixedEmail;
            $_SESSION['username'] = "accounts";
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid OTP.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Exhibitors Badges - Login</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="Admin template for exhibitors badges" />
    <meta name="author" content="Potenza Global Solutions" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="assets/img/imtex_favicon.png">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/vendors.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/style.css" />
</head>

<body class="bg-white">
    <div class="app">
        <div class="app-wrap">
            <div class="app-content">
                <div class="bg-white">
                    <div class="container-fluid p-0">
                        <div class="row no-gutters">
                            <div class="col-sm-6 col-lg-5 col-xxl-3 align-self-center">
                                <div class="d-flex align-items-center h-100-vh">
                                    <div class="login p-50">
                                        <h1 class="mb-2">Receipt Generation for Exhibitor Badges</h1>
                                        <p>Welcome back, please login to your account.</p>

                                        <?php if (isset($_SESSION['error'])): ?>
                                            <div class="alert alert-danger">
                                                <?php echo $_SESSION['error']; ?>
                                            </div>
                                            <?php unset($_SESSION['error']); ?>
                                        <?php endif; ?>

                                        <!-- OTP Form -->
                                        <?php if (!isset($_SESSION['otp_sent'])): ?>
                                            <form action="" method="POST" class="mt-3 mt-sm-5">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Username*</label>
                                                            <input type="text" name="username" class="form-control" value="accounts" readonly/>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <button type="submit" name="get_otp" class="btn btn-primary text-uppercase">Get OTP</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        <?php else: ?>
                                            <!-- After OTP sent, show the OTP input form -->
                                            <div class="alert alert-info">
                                                OTP has been sent to your email. Please check your inbox.
                                            </div>
                                            <form action="" method="POST" class="mt-3 mt-sm-5">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Username*</label>
                                                            <input type="text" name="username" class="form-control" value="accounts" readonly/>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Enter OTP*</label>
                                                            <input type="text" name="otp" class="form-control" placeholder="OTP" required/>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mt-3">
                                                        <button type="submit" name="verify_otp" id="sign-in-btn" class="btn btn-primary text-uppercase" disabled>SIGN IN</button>
                                                    </div>
                                                </div>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xxl-9 col-lg-7 bg-gradient o-hidden">
                                <div class="row align-items-center h-100">
                                    <div class="col-12 mx-auto">
                                        <img class="img-fluid" src="assets/img/bg/IMTEX.jpeg" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.querySelector('[name="otp"]');
            const signInButton = document.getElementById('sign-in-btn');

            if (otpInput && signInButton) {
                otpInput.addEventListener('input', function() {
                    if (otpInput.value.length === 6) {
                        signInButton.disabled = false;
                    } else {
                        signInButton.disabled = true;
                    }
                });
            }
        });
    </script>

    <script src="assets/js/vendors.js"></script>
    <script src="assets/js/app.js"></script>
</body>

</html>
