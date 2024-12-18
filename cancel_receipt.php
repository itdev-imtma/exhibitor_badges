<?php
    include('connect.php');
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';
    require 'phpmailer/src/Exception.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $response = [];

    ob_clean();

    if (isset($_POST['receipt_id'])) {
        $receipt_id = $_POST['receipt_id'];
        date_default_timezone_set('Asia/Kolkata');
        $cancel_date = date('Y-m-d H:i:s');

        $query1 = "UPDATE receipts SET cancelled = 1, cancelled_date = ? WHERE id = ?";
        $stmt1 = $conn->prepare($query1);

        $stmt1->bind_param('si', $cancel_date, $receipt_id);
        $stmt1->execute();

        $query = "SELECT r.*, e.company_name FROM receipts r
                  INNER JOIN exhibitors e ON r.exhibitor_id = e.exhibitor_id
                  WHERE r.id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            $response['status'] = 'error';
            $response['message'] = 'Failed to prepare the SQL query: ' . $conn->error;
            echo json_encode($response);
            exit;
        }

        $stmt->bind_param('i', $receipt_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $exhibitor_company_name = $row['company_name'];

            $insert_query = "INSERT INTO receipts (receipt_no, exhibitor_id, hall_no, stand_number, no_of_badges, total_amount, transaction_type, transaction_ref_no, created_date, cancelled, cancelled_date, status)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, 'Cancelled')";
            $insert_stmt = $conn->prepare($insert_query);

            if ($insert_stmt === false) {
                $response['status'] = 'error';
                $response['message'] = 'Failed to prepare the INSERT query: ' . $conn->error;
                echo json_encode($response);
                exit;
            }

            $insert_stmt->bind_param('isssiissss', 
                $row['receipt_no'], 
                $row['exhibitor_id'], 
                $row['hall_no'], 
                $row['stand_number'], 
                $row['no_of_badges'], 
                $row['total_amount'], 
                $row['transaction_type'], 
                $row['transaction_ref_no'],
                $row['created_date'],
                $cancel_date
            );

            $receipt_no = $row['receipt_no'];

            if ($insert_stmt->execute()) {
                $inserted_id = $conn->insert_id;
                $email = 'accounts@imtma.in'; 

                $subject = 'Cancelled Receipt Confirmation #' . $receipt_no . ' - ' . $exhibitor_company_name;
                $message = "
                    <html>
                    <head>
                        <title>Receipt Cancelled</title>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; }
                        </style>
                    </head>
                    <body>
                        <p>Dear Team,</p>
                        <p>We would like to inform you that your receipt <a href='https://www.imtex.in/exhibitor-badges/display_receipt.php?id=" . $inserted_id . "'>#{$receipt_no}</a> has been cancelled.</p>
                        <p>Thanks for your support.</p>
                        <p>Regards,<br> IMTEX Team</p>
                    </body>
                    </html>";

                $mail = new PHPMailer(true);
                try {
                    // Set up PHPMailer
                    $mail->isSMTP();
                    $mail->Host = 'mail.imtex.in';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'exhibitorbadges@imtex.in';
                    $mail->Password = 'P?w5(gesjWO{';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('exhibitorbadges@imtex.in', 'IMTEX 2025');
                    $mail->addAddress($email);

                    // Subject & Body
                    $mail->Subject = $subject;
                    $mail->Body    = $message;
                    $mail->isHTML(true);

                    // Send email
                    if ($mail->send()) {
                        $response['status'] = 'success';
                        $response['redirect_url'] = 'https://www.imtex.in/exhibitor-badges/display_receipt.php?id=' . $receipt_id;
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = 'Failed to send email';
                    }

                } catch (Exception $e) {
                    $response['status'] = 'error';
                    $response['message'] = 'Email Error: ' . $mail->ErrorInfo;
                }

            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to cancel the receipt';
            }

            $insert_stmt->close();
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Receipt not found';
        }

        $stmt->close();
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Receipt ID is missing';
    }

    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($response);
?>
