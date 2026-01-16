<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 0; size: landscape; }
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .container {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 20px;
        }
        .border-outer {
            border: 15px solid #6A5ACD; /* Theme Color */
            height: 92%; /* Adjusted for padding */
            padding: 5px;
        }
        .border-inner {
            border: 2px solid #E6E6FA;
            height: 94%; /* Adjusted for padding */
            padding: 30px;
            text-align: center;
        }
        .logo {
            max-height: 70px;
            margin-bottom: 15px;
        }
        .title {
            font-size: 32px;
            font-weight: bold;
            text-transform: uppercase;
            color: #6A5ACD;
            margin: 0 0 10px 0;
            letter-spacing: 2px;
        }
        .subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 25px;
            text-transform: uppercase;
        }
        .recipient {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            border-bottom: 1px solid #ccc;
            display: inline-block;
            padding-bottom: 5px;
            margin-bottom: 20px;
            min-width: 400px;
        }
        .description {
            font-size: 13px;
            color: #555;
            line-height: 1.5;
            margin-bottom: 30px;
            padding: 0 40px;
        }
        .signatures-table {
            width: 100%;
            margin-top: 30px;
        }
        .signatures-table td {
            text-align: center;
            vertical-align: bottom;
            width: 33%; /* Adjusted for potential 3 columns or spacing */
            padding: 0 10px;
        }
        .sig-line {
            border-top: 1px solid #333;
            width: 80%;
            margin: 10px auto 5px auto;
        }
        .sig-name {
            font-weight: bold;
            font-size: 12px;
        }
        .sig-title {
            font-size: 10px;
            color: #666;
        }
        .footer {
            position: absolute;
            bottom: 15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
    </style>
</head>
<body>
    <?php
        // Helper to get base64 image
        function get_img($path_or_url) {
            if (strpos($path_or_url, 'http') === 0) return $path_or_url;
            if (file_exists($path_or_url)) {
                $type = pathinfo($path_or_url, PATHINFO_EXTENSION);
                $data = file_get_contents($path_or_url);
                return 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            return '';
        }

        $is_white_label = defined('IS_WHITE_LABEL') && IS_WHITE_LABEL;
        $pratap_sig_path = APP_ROOT . '/public/images/PratapMondal.png';
        $suraj_sig_path = APP_ROOT . '/public/images/SurajKar.png';
    ?>

    <div class="container">
        <div class="border-outer">
            <div class="border-inner">

                <img src="<?php echo get_img($company['logo']); ?>" class="logo">

                <div class="title">Certificate of Partnership</div>
                <div class="subtitle">This certificate is proudly presented to</div>

                <div class="recipient"><?php echo $partner['profile']['full_name']; ?></div>

                <div class="description">
                    For successfully registering as an authorized Business Partner with <strong><?php echo $company['name']; ?></strong>.<br>
                    We recognize your commitment to excellence and look forward to a prosperous partnership.<br>
                    <br>
                    <strong>Partner ID:</strong> <?php echo $partner['id']; ?>
                </div>

                <table class="signatures-table">
                    <tr>
                        <?php if (!$is_white_label): ?>
                            <!-- Main Site: 2 Signatures -->
                            <td>
                                <div style="height: 50px;">
                                    <img src="<?php echo get_img($pratap_sig_path); ?>" style="max-height: 50px;">
                                </div>
                                <div class="sig-line"></div>
                                <div class="sig-name">Pratap Mondal</div>
                                <div class="sig-title">CEO</div>
                            </td>
                            <td>
                                <div style="height: 50px;">
                                    <img src="<?php echo get_img($suraj_sig_path); ?>" style="max-height: 50px;">
                                </div>
                                <div class="sig-line"></div>
                                <div class="sig-name">Suraj Kar</div>
                                <div class="sig-title">CEO</div>
                            </td>
                        <?php else: ?>
                            <!-- White Label: 1 Signature + Date -->
                            <td>
                                <div style="height: 50px;">
                                    <?php if (!empty($company['signature_url'])): ?>
                                        <img src="<?php echo get_img($company['signature_url']); ?>" style="max-height: 50px;">
                                    <?php endif; ?>
                                </div>
                                <div class="sig-line"></div>
                                <div class="sig-name"><?php echo $company['signatory_name']; ?></div>
                                <div class="sig-title"><?php echo $company['signatory_designation']; ?></div>
                            </td>
                            <td>
                                <div style="height: 50px; padding-top: 20px; font-size: 14px; font-weight: bold; color: #555;">
                                    <?php echo date('d M Y', strtotime($partner['created_at'])); ?>
                                </div>
                                <div class="sig-line"></div>
                                <div class="sig-name">Date of Issue</div>
                            </td>
                        <?php endif; ?>
                    </tr>
                </table>

                <div class="footer">
                    Certificate Generated on <?php echo date('d F Y'); ?> | <?php echo $_SERVER['HTTP_HOST']; ?>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
