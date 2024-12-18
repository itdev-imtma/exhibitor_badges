<?php
    include 'connect.php';

    $receipt_id = $_GET['id'];

    $sql = "SELECT e.company_name, r.* FROM receipts r
            inner join exhibitors e on r.exhibitor_id = e.exhibitor_id 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $receipt_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $receipt = $result->fetch_assoc();
    } else {
        die("Receipt not found.");
    }

    function convertNumberToWords($number) {
        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ' ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = [
            0 => 'Zero',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
            20 => 'Twenty',
            30 => 'Thirty',
            40 => 'Forty',
            50 => 'Fifty',
            60 => 'Sixty',
            70 => 'Seventy',
            80 => 'Eighty',
            90 => 'Ninety',
            100 => 'Hundred',
            1000 => 'Thousand',
            1000000 => 'Million',
            1000000000 => 'Billion',
            1000000000000 => 'Trillion',
            1000000000000000 => 'Quadrillion',
            1000000000000000000 => 'Quintillion',
        ];
    
        if (!is_numeric($number)) {
            return false;
        }
    
        if ($number < 0) {
            return $negative . convertNumberToWords(abs($number));
        }
    
        $string = $fraction = null;
    
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }
    
        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int)($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[(int)$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . convertNumberToWords($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int)($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = convertNumberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= convertNumberToWords($remainder);
                }
                break;
        }
    
        if ($fraction !== null && is_numeric($fraction)) {
            $string .= $decimal;
            $words = [];
            foreach (str_split((string)$fraction) as $digit) {
                $words[] = $dictionary[$digit];
            }
            $string .= implode(' ', $words);
        }
    
        return $string;
    }

    $stmt->close();
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Payment Receipt</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <meta name="description" content="Admin template that can be used to build dashboards for CRM, CMS, etc." />
        <meta name="author" content="Potenza Global Solutions" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- app favicon -->
        <link rel="shortcut icon" href="assets/img/imtex_favicon.png">
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <style>
            .receipt_font {
                font-family:HELVETICA; font-size:14px;
            }    
        </style>
    </head>

    <body style="margin:0px 0px 40px 0px;height:auto;" >
        <div id="subscription_full_bg" style="margin-bottom:0px;font-size:12px;" class="table_style">
            <table width="900" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="900" class="subscription_top_part" style="padding-left:0px !important; background:none;"><img src="assets/img/img_receipt.png"></td>
                </tr>
            </table>
            <table width="900" height="" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #000;border-width:thin;margin-top:0px;border-radius: 5px;margin-bottom: 0px;">
                <tr>
                    <td width="440" valign="top">
                        <table width="440" border="0" cellspacing="0" cellpadding="0" class="table_style">
                            <tr>
                                <td class="receipt_font" style="padding-left:10px;padding-top: 9px;" height="30" valign="top"><strong>Received With thanks from</strong></td>
                            </tr>
                            
                            <tr>
                                <td  class="receipt_font" style="border-bottom:1px solid #000; padding-left:10px;" height="40"><strong><?= htmlspecialchars($receipt['company_name']) ?></strong></td>
                            </tr>  
                            <tr>
                                <td  class="receipt_font" valign="top" style="padding-left:20px; padding-top:10px; padding-bottom:10px;" height="10"><strong>The Sum of:</strong>  </td>
                            </tr>  
                            <tr>
                                <td  class="receipt_font" valign="top" style="border-bottom: 1px solid #000; padding-left:20px; padding-top:10px; padding-bottom:10px;" height="20">
                                    <b><?= convertNumberToWords($receipt['total_amount']) ?> Rupees only</b></td>
                            </tr>
                            <tr>
                                <td valign="bottom" height="10" style="padding-left:20px;padding-top:10px;"  class="receipt_font"><b>On account of</b></td>
                            </tr>
                            <tr>
                                <td valign="bottom" height="10" style="padding-left:20px;padding-top:15px;" class="receipt_font" ><b>SALE OF EXHIBITOR BADGES DURING IMTEX 2025</b></td>
                            </tr>
                        </table>
                    </td>
                    <td width="473" align="left" valign="top" style="border-left: 1px solid #000">
                        <table width="450" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="testing">
                                    <table width="457" border="0" cellspacing="0" cellpadding="0" class="table_style">
                                        <tr height="35">
                                            <td style="border-bottom:1px solid #000;border-right:1px solid #000;" width="137" align="center" valign="middle" class="receipt_font"><b>Receipt No.</b></td>
                                            <td style="border-bottom:1px solid #000;border-right:1px solid #000;" width="134" align="center" valign="middle" class="receipt_font"><b>Dated</b></td>
                                            <td style="border-bottom:1px solid #000;border-right:0px solid #000;" width="179" align="center" valign="middle" class="receipt_font"><b>Amount (Rs.)</b> </td>
                                        </tr>
                                        <tr height="45">
                                            <td style="border-bottom:1px solid #000;border-right:1px solid #000;" align="center" valign="middle" class="receipt_font"><strong><?= htmlspecialchars($receipt['receipt_no']) ?></strong></td>
                                            <td style="border-bottom:1px solid #000;border-right:1px solid #000;" align="center" valign="middle" class="receipt_font"><b><?= htmlspecialchars(date('d-m-Y', strtotime($receipt['created_date']))) ?></b></td>
                                            <td style="border-bottom:1px solid #000;border-right:0px solid #000;" align="center" valign="middle" class="receipt_font">
                                            <?php 
                                                $cancelled = isset($receipt['cancelled']) ? $receipt['cancelled'] : null;
                                                $total_amount = isset($receipt['total_amount']) ? $receipt['total_amount'] : null;

                                                if ($cancelled === 1) {
                                                    // Ensure 'total_amount' is not null
                                                    $total_amount_display = $total_amount !== null ? htmlspecialchars($total_amount) : '0'; // Default to 0 if null
                                                    echo '<b>- ' . $total_amount_display . '/-</b>';
                                                } else {
                                                    // Ensure 'total_amount' is not null
                                                    $total_amount_display = $total_amount !== null ? htmlspecialchars($total_amount) : '0'; // Default to 0 if null
                                                    echo '<b>' . $total_amount_display . '/-</b>';
                                                }
                                            ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-left:10px;">
                                    <table width="425" border="0" cellspacing="0" cellpadding="0" class="table_style">
                                        <tr>
                                            <td>
                                                <table width="100%">
                                                    <tr>
                                                        <td valign="top" height="18" style="padding-top: 0px;" width="30%"> 
                                                            <table width="100%">
                                                                <tr>
                                                                    <td height="18" class="receipt_font" width="14%"><b>Mode of Payment</b></td>
                                                                    <td width="1%">:&nbsp;</td>
                                                                    <td class="receipt_font" width="74%">
                                                                        <b><?= htmlspecialchars($receipt['transaction_type']) . ' - ' . htmlspecialchars($receipt['transaction_ref_no'])  ?></b>
                                                                    </td>
                                                                    <td> 
                                                                        <?php 
                                                                            if ($cancelled === 1) {
                                                                                echo '<span style="color: red; font-size: 20px;"><b>CANCELLED</b></span>';
                                                                            } else {
                                                                                echo '<span style="color: green; font-size: 20px;"><b>PAID</b></span>';
                                                                            }
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="3"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="receipt_font" width="30%"><b>No. of Badges</b></td>
                                                                    <td width="1%">:&nbsp;</td>
                                                                    <td class="receipt_font" width="74%">
                                                                        <b><?= htmlspecialchars($receipt['no_of_badges']) ?></b>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr> 
                                        <tr>
                                            <td height="12"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td height="131" style="border-top:1px solid #000;">
                                    <table width="450" border="0" cellspacing="0" cellpadding="0" >
                                        <tr>
                                            <td>
                                                <div class="receipt_font" style="margin:10px 0px 0px 10px;font-size:13px;"><b>GSTIN (KA): 29AAACI1369M1Z7 | PAN No.: AAACI1369M</b>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <table width="450" border="0" cellspacing="0" cellpadding="0" class="table_style">
                                        <tr>
                                            <td height="10"></td>
                                        </tr>
                                        <tr>
                                            <td width="150" valign="top" align="right">
                                                <table>
                                                    <tr>
                                                        <td align="right" style="padding-right: 105px;" class="receipt_font"><strong>For IMTMA</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td height="20"></td>
                                                    </tr>
													<tr>
														<td align="right" style="font-size:15px; padding-right:20px"><strong>Authorised Signatory</strong></td>
													</tr>
													<tr>
														<td style="font-size: 14px;">This is a computer-generated receipt, no signature required.</td>
													</tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr><td height="15"></td></tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
    <script type="text/javascript">
        $(document).ready(function() {
            window.print();
        });
    </script>
</html>
