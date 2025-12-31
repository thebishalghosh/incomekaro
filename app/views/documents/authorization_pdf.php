<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Letter of Authorisation</title>
    <style>
        @page { margin: 80px 60px; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            line-height: 1.6;
            color: #222;
        }
        .header {
            position: fixed;
            top: -60px;
            left: 0px;
            right: 0px;
            height: 50px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        .logo {
            float: left;
            height: 50px;
        }
        .company-details {
            float: right;
            text-align: right;
            font-size: 10px;
            color: #555;
        }
        .footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 50px;
            font-size: 9px;
            text-align: center;
            color: #888;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            text-decoration: underline;
            margin-top: 30px;
            margin-bottom: 40px;
        }
        .date {
            text-align: right;
            margin-bottom: 30px;
        }
        .data-field {
            font-weight: bold;
        }
        .signature-section {
            margin-top: 60px;
            width: 100%;
            page-break-inside: avoid;
        }
        .sig-col {
            width: 48%;
            display: inline-block;
            text-align: center;
        }
        .sig-img {
            height: 50px;
            margin-bottom: 5px;
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

    <div class="header">
        <img src="<?php echo get_base64_image($logo_path); ?>" class="logo">
        <div class="company-details">
            <strong>Sunglory Software Private Limited</strong><br>
            Astra Tower, Unit No. ASO-303, 3rd Floor, New Town<br>
            North 24 Parganas – 700161, West Bengal, India<br>
            CIN: U62013WB2025PTC276552
        </div>
    </div>

    <div class="footer">
        IncomeKaro | https://incomekaro.in | All Rights Reserved © <?php echo date('Y'); ?>
    </div>

    <main>
        <div class="title">Letter of Authorisation</div>

        <div class="date">
            <strong>Date:</strong> <?php echo date('d F Y'); ?>
        </div>

        <div class="content">
            <p>To,</p>
            <p>
                <span class="data-field"><?php echo $partner['profile']['full_name']; ?></span><br>
                Partner ID: <span class="data-field"><?php echo $partner['id']; ?></span><br>
                <?php
                    $addr = $partner['address_office']['address'] ? $partner['address_office'] : $partner['address_permanent'];
                    echo $addr['address'] . ', ' . $addr['city'] . ',<br>' . $addr['state'] . ' - ' . $addr['pincode'];
                ?>
            </p>

            <p><strong>Subject: Authorisation to act as Sub-DSA for IncomeKaro</strong></p>

            <p>Dear <span class="data-field"><?php echo $partner['profile']['full_name']; ?></span>,</p>

            <p>This letter authorizes you to act as a Sub-DSA (Direct Selling Agent) for IncomeKaro to market financial products and services as categorized and identified under our official guidelines only.</p>

            <p>Your details as per our records are:</p>
            <ul>
                <li><strong>Aadhar No:</strong> <?php echo $partner['identity']['aadhaar'] ?: 'N/A'; ?></li>
                <li><strong>PAN No:</strong> <?php echo $partner['identity']['pan'] ?: 'N/A'; ?></li>
            </ul>

            <p>This authorisation is subject to the terms and conditions outlined in the Business Partnership Agreement accepted by you.</p>

            <p>We look forward to a successful partnership.</p>

            <p>Sincerely,</p>
        </div>

        <div class="signature-section">
            <div class="sig-col">
                <img src="<?php echo get_base64_image($pratap_sig_path); ?>" class="sig-img"><br>
                <hr style="margin:0; border-color: #555;">
                <strong>Pratap Mondal</strong><br>
                <span style="font-size: 11px; color: #666;">CEO, IncomeKaro</span>
            </div>
            <div class="sig-col">
                <img src="<?php echo get_base64_image($suraj_sig_path); ?>" class="sig-img"><br>
                <hr style="margin:0; border-color: #555;">
                <strong>Suraj Kar</strong><br>
                <span style="font-size: 11px; color: #666;">CFO, IncomeKaro</span>
            </div>
        </div>
    </main>
</body>
</html>
