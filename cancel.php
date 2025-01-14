<?php
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['user'])) {
        // Redirect to the login page if not logged in
        header("Location: login.php");
        exit();
    }
    
    include 'connect.php';

        $sql = "SELECT i.id, i.receipt_no, i.hall_no, i.stand_number, i.no_of_badges, i.total_amount, i.transaction_type, i.transaction_ref_no, 
                i.created_date, e.company_name
                FROM receipts i LEFT JOIN exhibitors e ON i.exhibitor_id = e.exhibitor_id
                WHERE i.status = 'Issued' and cancelled = 0 order by i.id desc";

    $result = $conn->query($sql);
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
                                <li><a href="re-print.php">Re-print Receipt</a></li>
                                <li><a href="cancel-receipt.php">Issued Receipts</a></li>
                                <li><a href="cancel.php">Cancelled Receipts</a></li>
                                <li><a href="exhibitors-list.php">Exhibitors List</a></li>
                                <li><a href="consolidated.php">Consolidated List</a></li>
                            </ul>
                        </div>
                        <a class="navbar-brand" href="index.php">
                            <img style="width: 30px; height: 30px;" src="assets/img/imtex_favicon.png" class="img-fluid logo-desktop" alt="logo" />
                            <label style="color: aliceblue"> Exhibitor Badges </label>
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
                                <!-- <li class="nav-item dropdown">
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
                                        </ul>
                                    </div>
                                </li> -->
                                <li class="nav-item dropdown user-profile">
                                    <a href="javascript:void(0)" class="nav-link dropdown-toggle " id="navbarDropdown4" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <img src="assets/img/user.png" alt="avtar-img">
                                        <span class="bg-success user-status"></span>
                                    </a>
                                    <div class="dropdown-menu animated fadeIn" aria-labelledby="navbarDropdown">
                                        <div class="bg-gradient px-4 py-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="mr-1">
                                                    <h4 class="text-white mb-0"><?php echo 'Accounts' ?></h4>
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
                            <li class="nav-static-title"><?php echo 'Welcome' ?></li>
                            <li>
                                <a href="index.php" aria-expanded="false">
                                    <i class="nav-icon ti ti-rocket"></i>
                                    <span class="nav-title"> Dashboard</span>
                                </a>
                            </li>
                            <li><a href="receipt-form.php" aria-expanded="false"><i class="nav-icon ti ti-pencil-alt"></i><span class="nav-title">Create New Receipt</span></a> </li>
                            <li><a href="re-print.php" aria-expanded="false"><i class="nav-icon ti ti-hand-open"></i><span class="nav-title">Re-print Receipt</span></a> </li>
                            <li class="active"><a href="cancel.php" aria-expanded="false"><i class="nav-icon ti ti-email"></i><span class="nav-title">Issued Receipts</span></a> </li>
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
                            <li>
                                <a href="consolidated.php" aria-expanded="false">
                                    <i class="nav-icon ti ti-layout-column3-alt"></i>
                                    <span class="nav-title">Consolidated List</span>
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
                                        <h1>Issued Receipts</h1>
                                    </div>
                                    <div class="ml-auto d-flex align-items-center">
                                        <nav>
                                            <ol class="breadcrumb p-0 m-b-0">
                                                <li class="breadcrumb-item">
                                                    <a href="index.php"><i class="ti ti-home"></i></a>
                                                </li>
                                                <li class="breadcrumb-item">
                                                Issued Receipts
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
                        <div class="col-lg-12">
                                <div class="card card-statistics">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="combined-table" class="display compact table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th style="display:none;">ID</th>
                                                        <th>Receipt No.</th>
                                                        <th>Company Name</th>
                                                        <th>Hall Number</th>
                                                        <th>Stand Number</th>
                                                        <th>No. of Badges</th>
                                                        <th>Total Amount</th>
                                                        <th>Transaction Type</th>
                                                        <th>Transaction Ref No.</th>
                                                        <th>Created Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        if ($result->num_rows > 0) {
                                                            while ($row = $result->fetch_assoc()) {
                                                                $company_name = nl2br(wordwrap(htmlspecialchars($row['company_name'] ?? '', ENT_NOQUOTES), 15, "\n", true));
                                                                echo "<tr>";
                                                                echo "<td style='display:none;'>" . htmlspecialchars($row['id']) . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['receipt_no']) . "</td>";
                                                                echo "<td>" . $company_name . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['hall_no']) . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['stand_number']) . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['no_of_badges']) . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['total_amount']) . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['transaction_type']) . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['transaction_ref_no'] ?? '') . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['created_date']) . "</td>";
                                                                echo "<td>
                                                                    <button class='btn btn-sm btn-danger cancel-receipt' data-id='" . $row['id'] . "'><span class='dashicons dashicons-no'></span></button>
                                                                </td>";

                                                                echo "</tr>";
                                                            }
                                                        } else {
                                                            echo "<tr><td colspan='10'>No data available</td></tr>";
                                                        }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->
                    </div>
                    <!-- end container-fluid -->
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

    <script>
        $(document).ready(function() {
            // Toggle mobile menu visibility
            $('.mobile-toggle').click(function() {
                $('.mobile-menu').toggleClass('active');
            });

            // Initialize DataTable
            var table = $('#combined-table').DataTable({
                "bLengthChange": false,
                "searching": true,
                "bPaginate": true,
                "bSortable": true,
                "order": [[0, 'desc']],
                "columnDefs": [
                    {
                        "targets": [8],  // Apply date sorting to column 8
                        "type": "date"
                    }
                ]
            });

            // Use event delegation to handle the cancel receipt action (this works across pages)
            $('#combined-table').on('click', '.cancel-receipt', function() {
                var receiptId = $(this).data('id');
                $('.loader').show();
                if (confirm("Are you sure you want to cancel this receipt?")) {
                    $.ajax({
                        url: 'cancel_receipt.php',
                        type: 'POST',
                        data: { receipt_id: receiptId },
                        success: function(response) {
                            $('.loader').hide();
                            console.log(response);
                            if (response.status === 'success') {
                                alert('Receipt has been cancelled successfully!');

                                // Set the flag in sessionStorage before redirect
                                sessionStorage.setItem('cancelledReceipt', 'true');
                                
                                // Redirect to the new URL
                                window.location.href = response.redirect_url;

                                // Modify the browser history (replace the state to avoid back button issues)
                                history.replaceState(null, '', response.redirect_url);
                            } else {
                                $('.loader').hide();
                                alert('Failed to cancel the receipt: ' + response.message);
                                location.reload();
                            }
                        },
                        error: function(xhr, status, error) {
                            $('.loader').hide();
                            console.log('AJAX Error:', error);
                            alert('An error occurred. Please try again.');
                            location.reload();
                        }
                    });
                }
            });

            // Check if the page was loaded after the redirect
            if (sessionStorage.getItem('cancelledReceipt') === 'true') {
                // If the flag is present, reload the page and remove the flag
                sessionStorage.removeItem('cancelledReceipt');
                location.reload(); // Force reload the page
            }
        });
    </script>
</body>
</html>

<?php
    $conn->close();
?>