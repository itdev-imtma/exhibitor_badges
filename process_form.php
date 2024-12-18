<?php
    include 'connect.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';
    require 'phpmailer/src/Exception.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    session_start();
    if (!isset($_SESSION['user'])) {
        $response = ['status' => 'error', 'message' => 'User not logged in.'];
        echo json_encode($response);
        exit();
    }

    $response = [];
    
    function generateReceiptNo($conn) {
        $sql = "SELECT MAX(CAST(receipt_no AS UNSIGNED)) AS max_receipt_no FROM receipts";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['max_receipt_no'] + 1;
        } else {
            return 1; // Start from 1 if there are no records
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $receipt_no = generateReceiptNo($conn);
        $exhibitor = $_POST['exhibitorSelect'];
        $hall_no = $_POST['hall_no'];
        $stand_number = $_POST['stand_number'];

        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');

        $badge_no = $_POST['badge_no'];
        $price_per_badge = $_POST['price_per_badge'];
        $total_amount = $_POST['total_amount'];
        $transaction_type = $_POST['transaction_type'];
        $transaction_ref_no = isset($_POST['transaction_ref_no']) ? $_POST['transaction_ref_no'] : '';

        $sql = "SELECT exhibitor_id, company_name FROM exhibitors WHERE company_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $exhibitor);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $exhibitor = $result->fetch_assoc();
            $exhibitor_id = $exhibitor['exhibitor_id'];
            $exhibitor_company_name = $exhibitor['company_name'];
        } else {
            $response = ['status' => 'error', 'message' => 'Exhibitor not found'];
            echo json_encode($response);
            exit();
        }

        $sql = "INSERT INTO receipts (
                    receipt_no, exhibitor_id, hall_no, stand_number, created_date, 
                    no_of_badges, total_amount, transaction_type, transaction_ref_no, status
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Issued'
                )";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("issssiiss", $receipt_no, $exhibitor_id, $hall_no, $stand_number, $date, 
                $badge_no, $total_amount, $transaction_type, $transaction_ref_no);

            if ($stmt->execute()) {
                $inserted_receipt_no = $conn->insert_id;
                $email = 'accounts@imtma.in'; 

                // Send Email using PHPMailer
                $mail = new PHPMailer(true);
                try {
                    // Configure PHPMailer settings
                    $mail->isSMTP();
                    $mail->Host = 'mail.imtex.in';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'exhibitorbadges@imtex.in';
                    $mail->Password = 'P?w5(gesjWO{';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Set email sender and recipient
                    $mail->setFrom('exhibitorbadges@imtex.in', 'IMTEX 2025');
                    $mail->addAddress($email);

                    // Email subject and body
                    $mail->Subject = 'New Receipt Confirmation #' . $receipt_no . ' - ' . $exhibitor_company_name;
                    $mail->Body = "
                        <html>
                        <head>
                            <title>Receipt Confirmation</title>
                        </head>
                        <body>
                            <p>Dear Team,</p>
                            <p>Receipt #{$receipt_no} has been successfully created for {$exhibitor_company_name}</p>
                            <p>You can view your receipt at <a href='https://www.imtex.in/exhibitor-badges/display_receipt.php?id={$inserted_receipt_no}'>this link</a>.</p>
                            <p>Regards, <br> IMTEX Team</p>
                        </body>
                        </html>";

                    $mail->isHTML(true);

                    // Send the email
                    if ($mail->send()) {
                        $response = ['status' => 'success', 'receipt_id' => $inserted_receipt_no];
                    } else {
                        $response = ['status' => 'error', 'message' => 'Failed to send email'];
                    }
                } catch (Exception $e) {
                    $response = ['status' => 'error', 'message' => 'Error sending email: ' . $mail->ErrorInfo];
                }
            } else {
                $response = ['status' => 'error', 'message' => 'Error saving the receipt: ' . $stmt->error];
            }

            $stmt->close();
        } else {
            $response = ['status' => 'error', 'message' => 'Error preparing the query: ' . $conn->error];
        }
    }

    // Send the response back as JSON
    echo json_encode($response);
?>
