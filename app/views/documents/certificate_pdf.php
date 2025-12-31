<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IncomeKaro Certificate</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #333;
        }
        .border-pattern {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 10px double #6A5ACD;
            padding: 40px;
            text-align: center;
        }
        .header-text {
            font-size: 40px;
            font-weight: bold;
            text-transform: uppercase;
            color: #6A5ACD;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }
        .sub-header {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
        }
        .logo {
            height: 80px;
            margin-bottom: 30px;
        }
        .certify-text {
            font-size: 16px;
            margin-bottom: 20px;
            font-style: italic;
        }
        .partner-name {
            font-size: 36px;
            font-weight: bold;
            color: #333;
            margin: 20px 0;
            border-bottom: 2px solid #E6E6FA;
            display: inline-block;
            padding-bottom: 10px;
            min-width: 400px;
        }
        .description {
            font-size: 14px;
            line-height: 1.6;
            margin: 0 auto 40px auto;
            max-width: 700px;
            color: #555;
        }
        .details-row {
            margin-bottom: 50px;
            font-size: 14px;
            font-weight: bold;
            color: #444;
        }
        .signatures {
            width: 100%;
            margin-top: 30px;
        }
        .sig-col {
            width: 40%;
            display: inline-block;
            vertical-align: top;
        }
        .sig-img {
            height: 60px;
            margin-bottom: 5px;
        }
        .footer {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <?php
        function get_base64_image($path) {
            if (!file_exists($path)) return '';
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $logo_path = APP_ROOT . '/public/images/logo.png';
        $pratap_sig_path = APP_ROOT . '/public/images/PratapMondal.png';
        $suraj_sig_path = APP_ROOT . '/public/images/SurajKar.png';
    ?>

    <div class="border-pattern">
        <div class="header-text">Certificate of Partnership</div>
        <div class="sub-header">Presented by</div>

        <img src="<?php echo get_base64_image($logo_path); ?>" class="logo">

        <div class="certify-text">This is to certify that</div>

        <div class="partner-name"><?php echo $partner['profile']['full_name']; ?></div>

        <div class="description">
            has demonstrated exceptional dedication and outstanding achievement as a Partner of IncomeKaro.
            This certificate is awarded in recognition of their remarkable contribution to our community.
        </div>

        <div class="details-row">
            Partner ID: <?php echo $partner['id']; ?> &nbsp;&nbsp;|&nbsp;&nbsp;
            Issue Date: <?php echo date('d/m/Y'); ?>
        </div>

        <div class="signatures">
            <div class="sig-col">
                <img src="<?php echo get_base64_image($pratap_sig_path); ?>" class="sig-img"><br>
                <strong>Pratap Mondal</strong><br>
                <span style="font-size: 12px; color: #666;">CEO, IncomeKaro</span>
            </div>

            <div class="sig-col">
                <img src="<?php echo get_base64_image($suraj_sig_path); ?>" class="sig-img"><br>
                <strong>Suraj Kar</strong><br>
                <span style="font-size: 12px; color: #666;">CEO, IncomeKaro</span>
            </div>
        </div>

        <div class="footer">
            IncomeKaro | https://incomekaro.in | All Rights Reserved © <?php echo date('Y'); ?>
        </div>
    </div>
</body>
</html>
