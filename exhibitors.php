<?php
    include 'connect.php';
    $filename = "exhibitors-list";

    if (isset($_POST['export'])) {
        $sql = "SELECT * FROM exhibitors";
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
        'Exhibitor ID',
        'Company',
        'Email',
        'Mobile Number',
        'Hall Number',
        'Stand Number',
    ];

    echo implode($sep, $custom_column_names) . "\n";

    $fields_count = mysqli_num_fields($result);
    while($row = mysqli_fetch_row($result)) {
        $schema_insert = [];
        for ($j = 0; $j < $fields_count; $j++) { 
            if (!isset($row[$j])) {
                $schema_insert[] = "NULL";
            } elseif ($row[$j] != "") {
                $schema_insert[] = '"' . str_replace('"', '""', $row[$j]) . '"'; 
            } else {
                $schema_insert[] = "";
            }
        }
        echo implode($sep, $schema_insert) . "\n";
    }

    $conn->close();
?>
