<?php
    include 'connect.php';
    $filename = "consolidated-report";

    if (isset($_POST['export'])) {
        $sql = "SELECT e.company_name, i.receipt_no, i.hall_no, i.stand_number, i.no_of_badges, 
                i.total_amount, i.transaction_type, i.transaction_ref_no, i.created_date, i.status, 
                i.cancelled_date, i.id 
                FROM receipts i 
                INNER JOIN exhibitors e ON i.exhibitor_id = e.exhibitor_id
                ORDER BY CAST(i.receipt_no AS UNSIGNED) ASC";

        $result = $conn->query($sql);
        if (!$result) {
            die("Couldn't execute query: " . mysqli_error($conn));
        }
    }

    $file_ending = "csv";
    
    header("Content-Type: text/csv");    
    header("Content-Disposition: attachment; filename=$filename.csv");  
    header("Pragma: no-cache"); 
    header("Expires: 0");

    $sep = ","; 

    $custom_column_names = [
        'Company Name',
        'Receipt Number',
        'Hall Number',
        'Stand Number',
        'No. of Badges',
        'Total Amount',
        'Transaction Type',
        'Transaction Ref No.',
        'Created Date',
        'Status',
        'Cancelled Date',
        'Link'
    ];

    echo implode($sep, $custom_column_names) . "\n";

    $fields_count = mysqli_num_fields($result);
    while($row = mysqli_fetch_row($result)) {
        $schema_insert = [];

        for ($j = 0; $j < $fields_count; $j++) { 
            if ($j == 5) { 
                if ($row[9] === 'Cancelled') { 
                    $schema_insert[] = '"-' . str_replace('"', '""', $row[$j]) . '"'; 
                } else {
                    $schema_insert[] = '"' . str_replace('"', '""', $row[$j]) . '"'; 
                }
            } else if ($j == 4) {
                if ($row[9] === 'Cancelled') { 
                    $schema_insert[] = '"-' . str_replace('"', '""', $row[$j]) . '"'; 
                } else {
                    $schema_insert[] = '"' . str_replace('"', '""', $row[$j]) . '"'; 
                }
            }
            else {
                if (!isset($row[$j])) {
                    $schema_insert[] = "NULL";
                } elseif ($j == 11) { 
                    $link = 'http://www.imtex.in/exhibitor-badges/display_receipt.php?id=' . $row[$j];
                    $schema_insert[] = '"' . str_replace('"', '""', $link) . '"'; 
                } elseif ($row[$j] != "") {
                    $schema_insert[] = '"' . str_replace('"', '""', $row[$j]) . '"'; 
                } else {
                    $schema_insert[] = "";
                }
            }
        }
        echo implode($sep, $schema_insert) . "\n";
    }

    $conn->close();
?>
