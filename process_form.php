<?php
    include 'connect.php';

    // Start session and check for login (optional, depending on your setup)
    session_start();
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }

    // Function to generate the next available receipt number
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

        $date = date('Y-m-d H:i:s');

        $badge_no = $_POST['badge_no'];
        $price_per_badge = $_POST['price_per_badge'];
        $total_amount = $_POST['total_amount'];
        $transaction_type = $_POST['transaction_type'];
        $transaction_ref_no = isset($_POST['transaction_ref_no']) ? $_POST['transaction_ref_no'] : '';

        $sql = "SELECT exhibitor_id FROM exhibitors WHERE company_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $exhibitor);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $exhibitor = $result->fetch_assoc();
            $exhibitor_id = $exhibitor['exhibitor_id'];
        } else {
            echo "<script>console.log('Exhibitor not found');</script>";
            die('Exhibitor not found');
        }

        // Prepare the SQL query to insert data
        $sql = "INSERT INTO receipts (
                    receipt_no, exhibitor_id, hall_no, stand_number, created_date, 
                    no_of_badges, total_amount, transaction_type, transaction_ref_no
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?
                )";

        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("issssiiss", $receipt_no, $exhibitor_id, $hall_no, $stand_number, $date, 
                $badge_no, $total_amount, $transaction_type, $transaction_ref_no);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Receipt saved successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error saving the receipt: ' . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error preparing the query: ' . $conn->error]);
        }
    }
?>