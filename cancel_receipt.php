<?php
    include('connect.php');

    if (isset($_POST['receipt_id'])) {
        $receipt_id = $_POST['receipt_id'];

        $cancel_date = date('Y-m-d H:i:s');

        $query = "UPDATE receipts SET cancelled = 1, cancelled_date = ? WHERE id = ?";
        $stmt = $conn->prepare($query);

        $stmt->bind_param('si', $cancel_date, $receipt_id);
        
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }

        $stmt->close();
    }

    $conn->close();
?>
