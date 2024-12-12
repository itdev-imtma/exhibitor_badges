<?php
    include 'connect.php';

    header('Content-Type: application/json');

    if (isset($_GET['company_name'])) {
        $company_name = $_GET['company_name'];

        $sql = "SELECT hall_no, stand_number FROM exhibitors WHERE company_name = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('s', $company_name);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($hall_no, $stand_number);
                $stmt->fetch();

                echo json_encode([
                    'hall_no' => $hall_no,
                    'stand_number' => $stand_number
                ]);
            } else {
                echo json_encode(['error' => 'No data found']);
            }

            $stmt->close();
        } else {
            echo json_encode(['error' => 'Query preparation failed']);
        }

        $conn->close();
    } else {
        echo json_encode(['error' => 'Company name not provided']);
    }
?>
