<?php
    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }
    
    include 'connect.php';

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    function generateReceiptNo($conn) {
        $sql = "SELECT MAX(CAST(receipt_no AS UNSIGNED)) AS max_receipt_no FROM receipts";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nextReceiptNo = $row['max_receipt_no'] + 1;
        } else {
            $nextReceiptNo = 1;
        }

        return $nextReceiptNo;
    }

    $nextReceiptNo = generateReceiptNo($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Exhibitors Badges</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="Admin template that can be used to build dashboards for CRM, CMS, etc." />
    <meta name="author" content="Potenza Global Solutions" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- app favicon -->
    <link rel="shortcut icon" href="assets/img/imtex_favicon.png">
    <!-- google fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
    <!-- plugin stylesheets -->
    <link rel="stylesheet" type="text/css" href="assets/css/vendors.css" />
    <!-- app style -->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css" />
    <!-- Include Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <style>
        .select2-container--default .select2-selection--single {
            height: 40px !important;
        }

        .select2-container--default .select2-selection--multiple {
            height: 40px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 8px !important;
        }
    </style>

    <script defer>
        // Calculate total amount whenever the number of badges is entered
        function calculateTotal() {
            var badgeCount = document.getElementById('badge_no').value;
            var pricePerBadge = document.getElementById('price_per_badge').value;
            var totalAmount = badgeCount * pricePerBadge;

            // Set the total amount field
            document.getElementById('total_amount').value = totalAmount;
        }
    </script>
</head>

<body>
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
            <!-- begin app-header -->
            <header class="app-header top-bar">
                <!-- begin navbar -->
                <nav class="navbar navbar-expand-md">

                    <!-- begin navbar-header -->
                    <div class="navbar-header d-flex align-items-center">
                        <a href="javascript:;" class="mobile-toggle"><i class="ti ti-align-right"></i></a>
                        <div class="mobile-menu">
                            <ul>
                            <li><a href="index.php">Dashboard</a></li>
                                <li><a href="receipt-form.php">Create New Receipt</a></li>
                                <li><a href="reprint.php">Re-print Receipt</a></li>
                                <li><a href="cancel-receipt.php">Cancel Receipt</a></li>
                                <li><a href="cancel.php">Cancelled Receipts</a></li>
                                <li><a href="exhibitors-list.php">Exhibitors List</a></li>
                            </ul>
                        </div>
                        <a class="navbar-brand" href="index.php">
                            <img style="width: 30px; height: 30px;" src="assets/img/imtex_favicon.png" class="img-fluid logo-desktop" alt="logo" />
                            <label style="color: aliceblue"> Incident Management </label>
                            <!-- <img src="assets/img/logo-icon.png" class="img-fluid logo-mobile" alt="logo" /> -->
                        </a>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="ti ti-align-left"></i>
                    </button>
                    <!-- end navbar-header -->
                    <!-- begin navigation -->
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <div class="navigation d-flex">
                            <ul class="navbar-nav nav-right ml-auto">
                            <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdown3" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fe fe-bell"></i>
                                        <span class="notify">
                                            <span class="blink"></span>
                                            <span class="dot"></span>
                                        </span>
                                    </a>
                                    <div class="dropdown-menu extended animated fadeIn" aria-labelledby="navbarDropdown">
                                        <ul>
                                            <li class="dropdown-header bg-gradient p-4 text-white text-left">Notifications
                                                <!-- <a href="#" class="float-right btn btn-square btn-inverse-light btn-xs m-0">
                                                    <span class="font-13"> Clear all</span>
                                                </a> -->
                                            </li>
                                            <li class="dropdown-body min-h-240 nicescroll">
                                                <ul class="scrollbar scroll_dark max-h-240">
                                                    <?php
                                                    // Assuming $incidentNotifications is fetched from the database as shown before
                                                    if (!empty($incidentNotifications)): 
                                                        foreach ($incidentNotifications as $incident): ?>
                                                            <li>
                                                                <a href="open.php">
                                                                    <div class="notification d-flex flex-row align-items-center">
                                                                        <div class="notify-icon bg-img align-self-center">
                                                                            <div class="bg-type bg-type-md">
                                                                                <span>INC</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="notify-message">
                                                                            <p class="font-weight-bold">Incident ID: <?php echo $incident['id']; ?></p>
                                                                            <small>Company: <?php echo $incident['company_name']; ?> - Reminder Email Pending</small>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                        <?php endforeach; 
                                                    else: ?>
                                                        <li>
                                                            <a href="javascript:void(0)">
                                                                <div class="notification d-flex flex-row align-items-center">
                                                                    <div class="notify-icon bg-img align-self-center">
                                                                        <div class="bg-type bg-type-md">
                                                                            <span>No reminders</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="notify-message">
                                                                        <p class="font-weight-bold">No incidents pending reminder emails</p>
                                                                        <small>All incidents are up to date.</small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </li>
                                            <!-- <li class="dropdown-footer">
                                                <a class="font-13" href="javascript:void(0)"> View All Notifications </a>
                                            </li> -->
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item dropdown user-profile">
                                    <a href="javascript:void(0)" class="nav-link dropdown-toggle " id="navbarDropdown4" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <img src="assets/img/user.png" alt="avtar-img">
                                        <span class="bg-success user-status"></span>
                                    </a>
                                    <div class="dropdown-menu animated fadeIn" aria-labelledby="navbarDropdown">
                                        <div class="bg-gradient px-4 py-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="mr-1">
                                                    <h4 class="text-white mb-0"><?php echo 'Hall ' .$selectedHall; ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="p-4">
                                            <a class="dropdown-item d-flex nav-link" href="logout.php">
                                                <i class="zmdi zmdi-power"></i>Logout
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- end navigation -->
                </nav>
                <!-- end navbar -->
            </header>
            <!-- end app-header -->
            <!-- begin app-container -->
            <div class="app-container">
                <!-- begin app-nabar -->
                <aside class="app-navbar">
                    <!-- begin sidebar-nav -->
                    <div class="sidebar-nav scrollbar scroll_light">
                        <ul class="metismenu " id="sidebarNav">
                            <li class="nav-static-title"><?php echo 'Welcome'; ?></li>
                            <li>
                                <a href="index.php" aria-expanded="false">
                                    <i class="nav-icon ti ti-rocket"></i>
                                    <span class="nav-title"> Dashboard</span>
                                </a>
                            </li>
                            <li class="active"><a href="receipt-form.php" aria-expanded="false"><i class="nav-icon ti ti-pencil-alt"></i><span class="nav-title">Create New Receipt</span></a> </li>
                            <li><a href="re-print.php" aria-expanded="false"><i class="nav-icon ti ti-hand-open"></i><span class="nav-title">Re-print Receipt</span></a> </li>
                            <li><a href="cancel.php" aria-expanded="false"><i class="nav-icon ti ti-email"></i><span class="nav-title">Cancel Receipt</span></a> </li>
                            <li>
                                <a href="cancel-receipt.php" aria-expanded="false">
                                    <i class="nav-icon ti ti-layout-column3-alt"></i>
                                    <span class="nav-title">Cancelled Receipts</span>
                                </a>
                            </li>
                            <li>
                                <a href="exhibitors-list.php" aria-expanded="false">
                                    <i class="nav-icon ti ti-layout-column3-alt"></i>
                                    <span class="nav-title">Exhibitors List</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- end sidebar-nav -->
                </aside>
                <!-- end app-navbar -->
                <!-- begin app-main -->
                <div class="app-main" id="main">
                    <!-- begin container-fluid -->
                    <div class="container-fluid">
                        <!-- begin row -->
                        <div class="row">
                            <div class="col-md-12 m-b-30">
                                <!-- begin page title -->
                                <div class="d-block d-sm-flex flex-nowrap align-items-center">
                                    <div class="page-title mb-2 mb-sm-0">
                                        <h1>Create a new Receipt</h1>
                                    </div>
                                    <div class="ml-auto d-flex align-items-center">
                                        <nav>
                                            <ol class="breadcrumb p-0 m-b-0">
                                                <li class="breadcrumb-item">
                                                    <a href="index.php"><i class="ti ti-home"></i></a>
                                                </li>
                                                <li class="breadcrumb-item">
                                                    Create a new Receipt
                                                </li>
                                            </ol>
                                        </nav>
                                    </div>
                                </div>
                                <!-- end page title -->
                            </div>
                        </div>
                        <!-- end row -->
                        <!-- begin row -->
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card card-statistics">
                                    <div class="card-header">
                                        <div class="card-heading">
                                            <h4 class="card-title">Receipt Form</h4>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                    <form id="mainSubmitButton" method="POST" onsubmit="return validateForm()">
                                        <div class="form-row">
                                            <!-- Receipt No. Field -->
                                            <div class="form-group col-md-6">
                                                <label for="receipt_no">Receipt Number</label>
                                                <input type="text" id="receipt_no" name="receipt_no" class="form-control" value="<?php echo $nextReceiptNo; ?>" readonly required>
                                            </div>
                                            
                                            <!-- Exhibitor Field -->
                                            <div class="form-group col-md-6">
                                                <label for="exhibitorSelect">Exhibitors</label>
                                                <select id="exhibitorSelect" name="exhibitorSelect" class="form-control" required>
                                                    <option value="" hidden>Select an Exhibitor</option>
                                                    <?php
                                                        $sql = "SELECT company_name FROM exhibitors order by company_name asc";
                                                        $result = $conn->query($sql);

                                                        if ($result->num_rows > 0) {
                                                            while ($row = $result->fetch_assoc()) {
                                                                echo "<option>" . $row['company_name'] . "</option>";
                                                            }
                                                        } else {
                                                            echo "<option>No exhibitors found</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <!-- Hall Number Field -->
                                            <div class="form-group col-md-6">
                                                <label for="hall_no">Hall Number</label>
                                                <input type="text" id="hall_no" name="hall_no" class="form-control" readonly required>
                                            </div>

                                            <!-- Stand Number Field -->
                                            <div class="form-group col-md-6">
                                                <label for="stand_number">Stand Number</label>
                                                <input type="text" id="stand_number" name="stand_number" class="form-control" readonly required>
                                            </div>

                                            <!-- Date Field -->
                                            <div class="form-group col-md-6">
                                                <label for="date">Date</label>
                                                <input type="text" id="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly required>
                                            </div>

                                            <!-- Number of Badges Field -->
                                            <div class="form-group col-md-6">
                                                <label for="badge_no">No. of Badges</label>
                                                <input type="number" id="badge_no" name="badge_no" class="form-control" min="1" required oninput="calculateTotal()">
                                            </div>

                                            <!-- Price per Badge (Fixed) -->
                                            <div class="form-group col-md-6">
                                                <label for="price_per_badge">Price per Badge</label>
                                                <input type="text" id="price_per_badge" name="price_per_badge" class="form-control" value="100" readonly required>
                                            </div>

                                            <!-- Total Amount Field -->
                                            <div class="form-group col-md-6">
                                                <label for="total_amount">Total Amount</label>
                                                <input type="text" id="total_amount" name="total_amount" class="form-control" readonly required>
                                            </div>
                                            
                                            <!-- Transaction Type Field -->
                                            <div class="form-group col-md-6">
                                                <label for="transaction_type">Transaction Type </label>
                                                <select id="transaction_type" name="transaction_type" class="form-control" required>
                                                    <option value="" hidden>Select Transaction Type</option>
                                                    <option>UPI</option>
                                                    <option>Cash</option>
                                                    <option>Card</option>
                                                </select>
                                            </div> 

                                            <!-- Transaction Ref No. Field -->
                                            <div class="form-group col-md-6">
                                                <label for="transaction_ref_no">Transaction Ref No.</label>
                                                <input type="text" id="transaction_ref_no" name="transaction_ref_no" class="form-control" required>
                                            </div>                                           
                                        </div>

                                        <div style="text-align: center;">
                                            <button type="submit" id="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end app-main -->
            </div>
            <!-- end app-container -->
            <!-- begin footer -->
            <footer class="footer">
                <div class="row">
                    <div class="col-12 col-sm-6 text-center text-sm-left">
                        <p> <?php echo date("Y"); ?> &copy; IMTMA. All rights reserved.</p>
                    </div>
                </div>
            </footer>
            <!-- end footer -->
        </div>
        <!-- end app-wrap -->
    </div>
    <!-- end app -->

    <!-- plugins -->
    <script src="assets/js/vendors.js"></script>

    <!-- custom app -->
    <script src="assets/js/app.js"></script>

    <!-- Initialize Select2 -->
    <script>
        $(document).ready(function() {
            $('.mobile-toggle').click(function() {
                $('.mobile-menu').toggleClass('active');
            });

            $('#exhibitorSelect').select2();
            $('#exhibitorSelect').change(function() {
                var selectedCompany = $(this).val();
                
                $.ajax({
                    url: 'get_exhibitor_details.php',
                    type: 'GET',
                    data: { company_name: selectedCompany },
                    success: function(response) {
                        if (response) {
                            $('#hall_no').val(response.hall_no);
                            $('#stand_number').val(response.stand_number);
                        } else {
                            alert("No data found for the selected exhibitor.");
                        }
                    },
                    error: function() {
                        alert("Error retrieving data.");
                    }
                });
            });

            function validateForm() {
                // Validate receipt_no (even though it's readonly, we ensure it has a value)
                var receiptNo = document.getElementById('receipt_no').value;
                if (receiptNo === '') {
                    alert('Receipt Number is required.');
                    return false;
                }

                // Validate exhibitorSelect
                var exhibitorSelect = document.getElementById('exhibitorSelect').value;
                if (exhibitorSelect === '') {
                    alert('Please select an exhibitor.');
                    return false;
                }

                // Validate hall_no
                var hallNo = document.getElementById('hall_no').value;
                if (hallNo === '') {
                    alert('Hall Number is required.');
                    return false;
                }

                // Validate stand_number
                var standNumber = document.getElementById('stand_number').value;
                if (standNumber === '') {
                    alert('Stand Number is required.');
                    return false;
                }

                // Validate badge_no
                var badgeNo = document.getElementById('badge_no').value;
                if (badgeNo === '' || badgeNo <= 0) {
                    alert('Please enter a valid number of badges.');
                    return false;
                }

                // Validate transaction_type
                var transactionType = document.getElementById('transaction_type').value;
                if (transactionType === '') {
                    alert('Please select a transaction type.');
                    return false;
                }

                // Validate transaction_ref_no
                var transactionRefNo = document.getElementById('transaction_ref_no').value;
                if (transactionType === 'UPI' && transactionRefNo === '') {
                    alert('Transaction Reference Number is required for UPI.');
                    return false;
                }

                // If all validations pass
                return true;
            }

            function toggleTransactionRefNo() {
                var transactionType = document.getElementById('transaction_type').value;
                var transactionRefNoField = document.getElementById('transaction_ref_no');
                
                if (transactionType === 'UPI') {
                    transactionRefNoField.disabled = false;
                    transactionRefNoField.required = true;
                } else {
                    transactionRefNoField.disabled = true;
                    transactionRefNoField.required = false;
                }
            }

            document.getElementById('transaction_type').addEventListener('change', toggleTransactionRefNo);

            $('#mainSubmitButton').on('submit', function(event) {
                event.preventDefault();

                var formData = new FormData(this);
                
                $.ajax({
                    url: "process_form.php",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        alert('Receipt created successfully');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('Failed to create receipt ' + error);
                    }
                });
            });
        });
    </script>
</body>
</html>
