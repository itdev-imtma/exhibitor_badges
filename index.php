<?php
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['user'])) {
        // Redirect to the login page if not logged in
        header("Location: login.php");
        exit();
    }
    
    include 'connect.php';

    //Total No. of badges
    $query = "SELECT COUNT(id) as badge_count 
            FROM receipts 
            WHERE cancelled = 0";

    $stmt = $conn->query($query);
    $no_of_badges = 0;
    if ($stmt->num_rows > 0) {
        $row = $stmt->fetch_assoc();
        $no_of_badges = $row['badge_count'];
    }

    //Total amount
    $total = "SELECT SUM(total_amount) as total_amount
            FROM receipts 
            WHERE cancelled = 0";

    $total_stmt = $conn->query($total);
    $total_amount = 0;
    if ($total_stmt->num_rows > 0) {
        $row = $total_stmt->fetch_assoc();
        $total_amount = $row['total_amount'];
    }

    //Total Cancelled Badges
    $query1 = "SELECT COUNT(id) as cancelled_count 
            FROM receipts 
            WHERE cancelled = 1";

    $stmt1 = $conn->query($query1);
    $cancelled_badges = 0;
    if ($stmt1->num_rows > 0) {
        $row = $stmt1->fetch_assoc();
        $cancelled_badges = $row['cancelled_count'];
    }

    //Total cancelled amount
    $total1 = "SELECT SUM(total_amount) as cancelled_amount
            FROM receipts 
            WHERE cancelled = 1";

    $total_stmt1 = $conn->query($total1);
    $cancelled_amount = 0;
    if ($total_stmt1->num_rows > 0) {
        $row = $total_stmt1->fetch_assoc();
        $cancelled_amount = $row['cancelled_amount'];
    }
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                            <label style="color: aliceblue"> Exhibitors Badges </label>
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
                            <li class="nav-static-title"><?php echo 'Welcome'; ?></li>
                            <li class="active">
                                <a href="index.php" aria-expanded="false">
                                    <i class="nav-icon ti ti-rocket"></i>
                                    <span class="nav-title"> Dashboard</span>
                                </a>
                            </li>
                            <li><a href="receipt-form.php" aria-expanded="false"><i class="nav-icon ti ti-pencil-alt"></i><span class="nav-title">Create New Receipt</span></a> </li>
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
                                <div class="d-block d-lg-flex flex-nowrap align-items-center">
                                    <div class="page-title mr-4 pr-4 border-right">
                                        <h1>Dashboard</h1>
                                    </div>
                                    <div class="breadcrumb-bar align-items-center">
                                        <nav>
                                            <ol class="breadcrumb p-0 m-b-0">
                                                <li class="breadcrumb-item">
                                                    <a href="index.php"><i class="ti ti-home"></i></a>
                                                </li>
                                                <!-- <li class="breadcrumb-item">
                                                    Dashboard
                                                </li> -->
                                            </ol>
                                        </nav>
                                    </div>
                                </div>
                                <!-- end page title -->
                            </div>
                        </div>
                        <!-- begin row -->
                        <div class="row">
                            <div class="col-xs-6 col-xxl-3 m-b-30">
                                <div class="card card-statistics h-100 m-b-0 bg-pink">
                                    <div class="card-body">
                                        <h2 class="text-white mb-0"><?php echo $no_of_badges; ?></h2>
                                        <p class="text-white">Total Badges</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-xxl-3 m-b-30">
                                <div class="card card-statistics h-100 m-b-0 bg-primary">
                                    <div class="card-body">
                                        <h2 class="text-white mb-0"><?php echo $total_amount; ?></h2>
                                        <p class="text-white">Total Amount </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-xxl-3 m-b-30">
                                <div class="card card-statistics h-100 m-b-0 bg-orange">
                                    <div class="card-body">
                                        <h2 class="text-white mb-0"><?php echo $cancelled_badges; ?></h2>
                                        <p class="text-white">Total Cancelled Receipts </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-xxl-3 m-b-30">
                                <div class="card card-statistics h-100 m-b-0 bg-info">
                                    <div class="card-body">
                                        <h2 class="text-white mb-0"><?php echo $cancelled_amount; ?></h2>
                                        <p class="text-white">Total Amount Cancelled</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-xxl-3">
                                <div class="card card-statistics ecommerce-contant overflow-h">
                                    <div class="card-body p-0">
                                        <div class="d-flex m-b-0 ecommerce-contant-text h-100">
                                            <div class="w-100">
                                                <div class="row p-3">
                                                    <div class="col">
                                                        <h3 class="mb-0"><?php echo $totalIncidents; ?></h3>
                                                        <!-- <small class="d-block">Last 6 months</small> -->
                                                    </div>
                                                    <div class="col text-right">
                                                        <h5 class="text-muted mb-0">Total Incidents</h5>
                                                    </div>
                                                </div>
                                                <div class="apexchart-wrapper">
                                                    <div id="ecommercedemo3" class="chart-fit"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xxl-3">
                                <div class="card card-statistics ecommerce-contant overflow-h">
                                    <div class="card-body p-0">
                                        <div class="d-flex ecommerce-contant-text m-b-0 h-100">
                                            <div class="w-100">
                                                <div class="row p-3">
                                                    <div class="col">
                                                        <h3 class="mb-0"><?php echo $totalcritical; ?></h3>
                                                        <!-- <small class="d-block">Past 6 months</small> -->
                                                    </div>
                                                    <div class="col text-right">
                                                        <h5 class="text-muted mb-0">Critical Incidents</h5>
                                                    </div>
                                                </div>
                                                <div class="apexchart-wrapper">
                                                    <div id="ecommercedemo1" class="chart-fit"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-xxl-3">
                                <div class="card card-statistics ecommerce-contant overflow-h">
                                    <div class="card-body p-0">
                                        <div class="d-flex m-b-0 ecommerce-contant-text h-100">
                                            <div class="w-100">
                                                <div class="w-100">
                                                    <div class="row p-3">
                                                        <div class="col">
                                                            <h3 class="mb-0"><?php echo $totalClosedIncidents; ?></h3>
                                                            <!-- <small class="d-block">Last 6 months</small> -->
                                                        </div>
                                                        <div class="col text-right">
                                                            <h5 class="text-muted mb-0">Closed Incidents</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="apexchart-wrapper">
                                                    <div id="ecommercedemo2" class="chart-fit"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xxl-3">
                                <div class="card card-statistics ecommerce-contant overflow-h">
                                    <div class="card-body p-0">
                                        <div class="d-flex m-b-0 ecommerce-contant-text h-100">
                                            <div class="w-100">
                                                <div class="row p-3">
                                                    <div class="col">
                                                        <h3 class="mb-0"><?php echo $totalOpenIncidents; ?></h3>
                                                        <!-- <small class="d-block">Last 6 months</small> -->
                                                    </div>
                                                    <div class="col text-right">
                                                        <h5 class="text-muted mb-0">Open Incidents</h5>
                                                    </div>
                                                </div>
                                                <div class="apexchart-wrapper">
                                                    <div id="ecommercedemo4" class="chart-fit"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xxl-8 m-b-30">
                                <div class="card card-statistics h-100 mb-0">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div class="card-heading">
                                            <h4 class="card-title">Top 5 Severe Open Incidents</h4>
                                        </div>
                                    </div>
                                    <div class="card-body scrollbar scroll_dark pt-0" style="max-height: 350px;">
                                        <div class="table-responsive">
                                            <table id="datatable" class="display compact table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Incident ID</th>
                                                        <th>Company Name</th>
                                                        <th>Incident</th>
                                                        <th>Created Date</th>
                                                        <th>Hall Number</th>
                                                        <th>Stand Number</th>
                                                        <th>Primary Email</th>
                                                        <th>Secondary Email</th>
                                                        <th>Criticality</th>
                                                        <th>Resend Count</th>
                                                        <th>Images</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        if ($result->num_rows > 0) {
                                                            while ($row = $result->fetch_assoc()) {
                                                                $incident_name = nl2br(wordwrap(htmlspecialchars($row['incident'] ?? ''), 15, "\n", true));
                                                                $company_name = nl2br(wordwrap(htmlspecialchars($row['company_name'] ?? '', ENT_NOQUOTES), 15, "\n", true));
                                                                echo "<tr>";
                                                                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                                                echo "<td>" . $company_name . "</td>";
                                                                echo "<td>" . $incident_name . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['incident_date']) . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['hall_no']) . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['stand_number']) . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['email1'] ?? '') . "</td>";
                                                                $criticality = htmlspecialchars($row['criticality']);
                                                                switch ($criticality) {
                                                                    case 'Critical':
                                                                        echo "<td><span class='badge badge-danger-inverse'>Critical</span></td>";
                                                                        break;
                                                                    case 'High':
                                                                        echo "<td><span class='badge badge-warning-inverse'>High</span></td>";
                                                                        break;
                                                                    case 'Medium':
                                                                        echo "<td><span class='badge badge-info-inverse'>Medium</span></td>";
                                                                        break;
                                                                    case 'Low':
                                                                        echo "<td><span class='badge badge-success-inverse'>Low</span></td>";
                                                                        break;
                                                                    default:
                                                                        echo "<td><span class='badge badge-secondary-inverse'>" .$criticality. "</span></td>";
                                                                }
                                                                echo "<td>" . htmlspecialchars($row['resend_count']) . "</td>";

                                                                $images = htmlspecialchars($row['image_paths']);
                                                                if (!empty($images)) {
                                                                    $imageArray = explode(',', $images);
                                                                    echo "<td>";
                                                                    foreach ($imageArray as $image) {
                                                                        echo "<a href='$image' target='_blank'><img src='$image' alt='Image' style='width:50px;height:50px; margin-right: 5px;'></a>";
                                                                    }
                                                                    echo "</td>";
                                                                } else {
                                                                    echo "<td>No images</td>";
                                                                }

                                                                echo "<td>
                                                                    <input type='checkbox' class='include-secondary-email' data-email1='" . $row['email1'] . "'>
                                                                    <button class='btn btn-sm btn-danger close-incident' data-id='" . $row['id'] . "'><span class='dashicons dashicons-no'></span></button>
                                                                    <button class='btn btn-sm btn-primary resend-mail' 
                                                                        data-id='" . $row['id'] . "' 
                                                                        data-email='" . $row['email'] . "' 
                                                                        data-email1='" . $row['email1'] . "' 
                                                                        data-incident='" . htmlspecialchars($row['incident']) . "' 
                                                                        data-criticality='" . htmlspecialchars($row['criticality']) . "' 
                                                                        data-images='" . htmlspecialchars($row['image_paths']) . "' 
                                                                        data-resend-count='" . $row['resend_count'] . "'>
                                                                        <span class='dripicons dripicons-mail'></span>
                                                                    </button>
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
                        <div class="row">
                            <div class="col-xxl-7 m-b-30">
                                <div class="card card-statistics h-100 mb-0">
                                    <div class="card-header d-flex justify-content-between">
                                        <div class="card-heading">
                                            <h4 class="card-title">Incidents Summary</h4>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- <h5>We only started collecting data from February 2019 </h5> -->
                                        <div class="row mt-4">
                                            <div class="col-lg-8">
                                                <div class="morris-wrapper">
                                                    <div id="morrisecommerce1" style="height: 260px;"></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mt-4">
                                                <div class="mb-3">
                                                    <h3 class="mb-0"><?php echo $totalIncidents; ?></h3>
                                                    <p class="mb-0 text-info">Total Incidents</p>
                                                </div>
                                                <div class="mb-3">
                                                    <h3 class="mb-0"><?php echo $totalOpenIncidents; ?></h3>
                                                    <p class="mb-0 text-primary">Open Incidents</p>
                                                </div>
                                                <div class="mb-3">
                                                    <h3 class="mb-0"><?php echo $totalClosedIncidents; ?></h3>
                                                    <p class="mb-0" style="color: #52dce5">Closed Incidents</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if ($selectedHall == 'All'): ?>
                                <div class="col-xxl-5 m-b-30">
                                    <div class="card card-statistics">
                                        <div class="card-header">
                                            <div class="card-heading">
                                                <h4 class="card-title">Incidents Summary (Hall wise)</h4>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="apexchart-wrapper">
                                                <div id="apexdemo1"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
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
            $('.mobile-toggle').click(function() {
                $('.mobile-menu').toggleClass('active');
            });

            var table = $('#datatable').DataTable({
                "bLengthChange": false,
                "searching": true,
                "bPaginate": true,
                "bSortable": true,
                "order": [[0, 'desc']]
            });

            var morrisecommerce1 = jQuery("#morrisecommerce1");
            if (morrisecommerce1.length > 0) {
                Morris.Donut({
                    element: morrisecommerce1,
                    data: [
                        { label: "Total Incidents", value: <?php echo $totalIncidents; ?> },
                        { label: "Open Incidents", value: <?php echo $totalOpenIncidents; ?> },
                        { label: "Closed Incidents", value: <?php echo $totalClosedIncidents; ?> }
                    ],
                    colors: ['#45aaf2', '#8E54E9', '#52dce5']
                });
            }

            var total_badges = <?php echo json_encode($total_badges); ?>;
            var dataOpenLast6MonthsByDate = <?php echo json_encode($dataOpenLast6MonthsByDate); ?>;
            var dataOpenLast6Months = <?php echo json_encode($dataOpenLast6Months); ?>;
            var dataClosedLast6Months = <?php echo json_encode($dataClosedLast6Months); ?>;

            var hallData = <?php echo $hallDataJson; ?>;
            var halls = [];
            var totalIncidents = [];
            var openIncidents = [];
            var closedIncidents = [];
            var criticalIncidents = [];

            hallData.forEach(function(hall) {
                halls.push(hall.hall_no);
                totalIncidents.push(hall.total);
                openIncidents.push(hall.open);
                closedIncidents.push(hall.closed);
                criticalIncidents.push(hall.critical);
            });

            var apexdemo1 = jQuery('#apexdemo1');
            if (apexdemo1.length > 0) {
                var options = {
                    chart: {
                        height: 350,
                        type: 'line',
                        shadow: {
                            enabled: true,
                            color: '#000',
                            top: 18,
                            left: 7,
                            blur: 10,
                            opacity: 1
                        },
                        toolbar: {
                            show: false
                        }
                    },
                    colors: ['#FF5733', '#8E54E9', '#2bcbba', '#fbaf54'],
                    dataLabels: {
                        enabled: true,
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    series: [
                        {
                            name: "Total Incidents",
                            data: totalIncidents
                        },
                        {
                            name: "Open Incidents",
                            data: openIncidents
                        },
                        {
                            name: "Closed Incidents",
                            data: closedIncidents
                        },
                        {
                            name: "Critical Incidents",
                            data: criticalIncidents
                        }
                    ],
                    grid: {
                        borderColor: '#e7e7e7',
                        row: {
                            colors: ['#f3f3f3', 'transparent'],
                            opacity: 0.5
                        },
                    },
                    markers: {
                        size: 6
                    },
                    xaxis: {
                        categories: halls,  // Dynamic hall names
                        title: {
                            text: 'Hall Number'
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Number of Incidents'
                        },
                        min: 0
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        floating: true,
                        offsetY: -25,
                        offsetX: -5
                    }
                };

                var chart = new ApexCharts(document.querySelector("#apexdemo1"), options);
                chart.render();
            }

            var apexdemo1 = jQuery('#apexdemo1');
            if (apexdemo1.length > 0) {
                var options = {
                    chart: {
                        type: 'bar',
                        width: 120,
                        height: 50,
                        sparkline: {
                            enabled: true
                        }
                    },
                    colors: ['#8E54E9'],
                    plotOptions: {
                        bar: {
                            columnWidth: '20%',
                            endingShape: 'rounded',
                        }
                    },
                    series: [{
                        data: total_badges.counts
                    }],
                    labels: total_badges.dates,
                    xaxis: {
                        type: 'datetime',
                        crosshairs: {
                            width: 1
                        }
                    },
                    tooltip: {
                        fixed: { enabled: false },
                        x: { show: true, format: 'dd MMM yyyy' },  // Show the date in tooltip
                        y: {
                            title: {
                                formatter: function () {
                                    return 'Badges: ';
                                }
                            }
                        },
                        marker: { show: false }
                    },
                    responsive: [
                        {
                            breakpoint: 360,
                            options: {
                                chart: { width: 60, height: 60 }
                            }
                        },
                        {
                            breakpoint: 480,
                            options: {
                                chart: { width: 100, height: 80 }
                            }
                        }
                    ]
                };

                var chart = new ApexCharts(
                    document.querySelector("#analytics7"),
                    options
                );
                chart.render();
            }

            //Critical Incidents
            var ecommercedemo1 = jQuery('#ecommercedemo1')
            if (ecommercedemo1.length > 0) {

                var randomizeArray = function (arg) {
                    var array = arg.slice();
                    var currentIndex = array.length,
                        temporaryValue, randomIndex;

                    while (0 !== currentIndex) {

                        randomIndex = Math.floor(Math.random() * currentIndex);
                        currentIndex -= 1;

                        temporaryValue = array[currentIndex];
                        array[currentIndex] = array[randomIndex];
                        array[randomIndex] = temporaryValue;
                    }

                    return array;
                }

                var options = {
                    chart: {
                        type: 'area',
                        height: 100,
                        sparkline: {
                            enabled: true,
                            offsetY:25,
                            offsetX:25,
                        },
                        },
                        stroke: {
                        curve: 'smooth',
                        width: 3
                        },
                        fill: {
                        opacity: 0.3,
                        gradient: {
                            enabled: true,
                            shadeIntensity: 0.1,
                            inverseColors: false,
                            opacityFrom: 0.6,
                            opacityTo: 0.2,
                            stops: [20, 100, 100, 100]
                        },
                        },
                        series: [{
                            name: 'Critical Incidents',
                            data: dataOpenLast6MonthsByDate.map(function(item) {
                                return { x: item.incident_date, y: item.value };
                            })
                        }],
                        yaxis: {
                            min: 0
                        },
                        colors: ['#8E54E9'],
                }

                var chart = new ApexCharts(
                    document.querySelector("#ecommercedemo1"),
                    options
                );

                chart.render();
            }

            //Closed Incidents past 6 months
            var ecommercedemo2 = jQuery('#ecommercedemo2')
            if (ecommercedemo2.length > 0) {

                var randomizeArray = function (arg) {
                    var array = arg.slice();
                    var currentIndex = array.length,
                        temporaryValue, randomIndex;

                    while (0 !== currentIndex) {

                        randomIndex = Math.floor(Math.random() * currentIndex);
                        currentIndex -= 1;

                        temporaryValue = array[currentIndex];
                        array[currentIndex] = array[randomIndex];
                        array[randomIndex] = temporaryValue;
                    }

                    return array;
                    }

                var options = {
                    chart: {
                        type: 'area',
                        height: 100,
                        sparkline: {
                        enabled: true,
                        offsetY:25,
                        offsetX:25,
                        },
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    fill: {
                        opacity: 0.3,
                        gradient: {
                        enabled: true,
                        shadeIntensity: 0.1,
                        inverseColors: false,
                        opacityFrom: 0.6,
                        opacityTo: 0.2,
                        stops: [20, 100, 100, 100]
                        },
                    },
                    series: [{
                        name: 'Closed Incidents',
                        data: dataClosedLast6Months.map(function(item) {
                            return { x: item.date, y: item.value };
                        })
                    }],
                    yaxis: {
                        min: 0
                    },
                    xaxis: {
                        type: 'datetime',
                        labels: {
                            style: {
                                colors: ['#949494'],
                                fontSize: '12px',
                                fontFamily: 'Roboto',
                            },
                            offsetX: -25
                        },
                        axisBorder: {
                            show: false
                        },
                    },
                    colors: ['#fbaf54'],
                }

                var chart = new ApexCharts(
                    document.querySelector("#ecommercedemo2"),
                    options
                );

                chart.render();
            }

            //Open incidents last 6 months
            var ecommercedemo4 = jQuery('#ecommercedemo4')
            if (ecommercedemo4.length > 0) {

                var randomizeArray = function (arg) {
                    var array = arg.slice();
                    var currentIndex = array.length,
                        temporaryValue, randomIndex;

                    while (0 !== currentIndex) {

                        randomIndex = Math.floor(Math.random() * currentIndex);
                        currentIndex -= 1;

                        temporaryValue = array[currentIndex];
                        array[currentIndex] = array[randomIndex];
                        array[randomIndex] = temporaryValue;
                    }

                    return array;
                }

                var options = {
                    chart: {
                        type: 'area',
                        height: 100,
                        sparkline: {
                          enabled: true,
                          offsetY:25,
                          offsetX:25,
                        },
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    fill: {
                        opacity: 0.3,
                        gradient: {
                          enabled: true,
                          shadeIntensity: 0.1,
                          inverseColors: false,
                          opacityFrom: 0.6,
                          opacityTo: 0.2,
                          stops: [20, 100, 100, 100]
                        },
                    },
                    series: [{
                        name: 'Open Incidents',
                        data: dataOpenLast6Months.map(function(item) {
                            return { x: item.date, y: item.value };
                        })
                    }],
                    yaxis: {
                        min: 0
                    },
                    xaxis: {
                        type: 'datetime',
                        labels: {
                            style: {
                                colors: ['#949494'],
                                fontSize: '12px',
                                fontFamily: 'Roboto',
                            },
                            offsetX: -25
                        },
                        axisBorder: {
                            show: false
                        },
                    },
                    colors: ['#32b432'],
                }

                var chart = new ApexCharts(
                    document.querySelector("#ecommercedemo4"),
                    options
                );

                chart.render();
            }

            $(".close-incident").click(function() {
                var incidentId = $(this).data("id");
                var mobile = $(this).data("mobile");
                // console.log(mobile);

                if (confirm("Are you sure you want to close this incident?")) {
                    $.ajax({
                        url: "close_incident.php",
                        type: "POST",
                        data: { id: incidentId },
                        success: function(response) {
                            var data = JSON.parse(response);
                            if (data.status === "success") {
                                alert("Incident closed and email sent successfully.");

                                var formData = {
                                    id: incidentId,
                                    mobile: mobile
                                };

                                // Send WhatsApp message using AJAX
                                $.ajax({
                                    headers: {
                                        "Content-Type": "application/json",
                                    },
                                    url: "https://prod-31.centralindia.logic.azure.com:443/workflows/f586a0d2c340475b8db98a1b2bbe4e4f/triggers/manual/paths/invoke?api-version=2016-06-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=rVip_O8rGWJVoQfKhWi-pV_zE-WMtd_D6ierXDlIgRY",
                                    type: "POST",
                                    data: JSON.stringify(formData),
                                    success: function(response) {
                                        alert('WhatsApp message sent successfully: ' + response);
                                        location.reload();
                                    },
                                    error: function(xhr, status, error) {
                                        alert('WhatsApp message sending failed: ' + error);
                                    }
                                });
                            } else {
                                alert('Erro closing incident: ' + data.message);
                            }
                        },
                        error: function() {
                            alert("Error with the AJAX request.");
                        }
                    });
                }
            });

            $(".resend-mail").click(function() {
                var incidentId = $(this).data("id");
                var incident = $(this).data("incident");
                var email = $(this).data("email");
                var criticality = $(this).data("criticality");
                var imagePaths = $(this).data("images");
                var resendCount = $(this).data("resend-count");
                // console.log(resendCount);

                var includeSecondaryEmail = $(this).closest('tr').find('.include-secondary-email').is(':checked');
                var secondaryEmail = $(this).data("email1"); 

                if (resendCount >= 3) {
                    alert("Email has already been sent 3 times. No further emails will be sent.");
                    return;
                }

                if (includeSecondaryEmail && !confirm("Do you want to include the secondary email while sending the reminder?")) {
                    return;
                }

                var emailData = {
                    id: incidentId,
                    incident: incident,
                    criticality: criticality,
                    images: imagePaths,
                    email: email,
                    resend_count: resendCount
                };

                if (includeSecondaryEmail) {
                    emailData.secondary_email = secondaryEmail;
                }
                // console.log(emailData);
                if (confirm("Are you sure you want to resend the mail?")) {
                    $.ajax({
                        url: "resend_mail.php", 
                        type: "POST",
                        data: emailData,
                        dataType: "json",
                        success: function(response) {
                            if (response.status === "success") {
                                alert(response.message);
                                location.reload();
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log("AJAX Error Details:");
                            console.log("Status: " + status);
                            console.log("Error: " + error);
                            console.log("Response Text: " + xhr.responseText);
                            alert("Error with the AJAX request. Please try again later.");
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>

<?php
    $conn->close();
?>