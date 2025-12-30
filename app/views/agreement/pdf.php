<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Partner Agreement</title>
    <style>
        @page { margin: 0px; }
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #333;
            margin: 0px;
        }
        .header {
            background-color: #6A5ACD;
            color: white;
            padding: 30px 40px;
            text-align: center;
            margin-bottom: 30px;
        }
        .content {
            padding: 0 40px 40px 40px;
        }
        .logo {
            max-height: 50px;
            margin-bottom: 10px;
            filter: brightness(0) invert(1); /* Make logo white */
        }
        h1 {
            font-size: 24px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        h2 {
            font-size: 14px;
            font-weight: normal;
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #6A5ACD;
            border-bottom: 2px solid #E6E6FA;
            padding-bottom: 5px;
            margin-top: 25px;
            margin-bottom: 10px;
        }
        .data-field {
            font-weight: bold;
            color: #000;
        }
        ul {
            padding-left: 18px;
            margin-top: 5px;
        }
        li {
            margin-bottom: 5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .label {
            width: 120px;
            color: #666;
            font-weight: bold;
        }
        .signature-box {
            background-color: #F8F9FD;
            border: 1px solid #E6E6FA;
            border-radius: 5px;
            padding: 20px;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-row {
            width: 100%;
            margin-top: 20px;
        }
        .sig-col {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            text-align: center;
        }
        .sig-img {
            height: 50px;
            margin-bottom: 5px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background-color: #f1f1f1;
            text-align: center;
            line-height: 30px;
            font-size: 9px;
            color: #888;
            border-top: 1px solid #ddd;
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
        <!-- Using white logo if possible, or just the image -->
        <img src="<?php echo get_base64_image($logo_path); ?>" class="logo"><br>
        <h1>Business Partnership Agreement</h1>
        <h2>Sunglory Software Private Limited (Incomekaro)</h2>
    </div>

    <div class="content">
        <p>This Business Partnership Agreement (“Agreement”) is entered into between <strong>Sunglory Software Private Limited</strong>, operating under the brand name <strong>Incomekaro</strong> (hereinafter referred to as the “Company”), and the Partner detailed below, effective from <span class="data-field"><?php echo date('jS F Y \a\t H:i:s', strtotime($partner['agreement_accepted_at'] ?? 'now')); ?></span>.</p>

        <div class="section-title">Partner Details</div>
        <table class="info-table">
            <tr><td class="label">Name:</td><td class="data-field"><?php echo $partner['profile']['full_name']; ?></td></tr>
            <tr><td class="label">Partner ID:</td><td class="data-field"><?php echo $partner['id']; ?></td></tr>
            <tr><td class="label">Aadhaar:</td><td class="data-field"><?php echo $partner['identity']['aadhaar'] ?: 'N/A'; ?></td></tr>
            <tr><td class="label">PAN:</td><td class="data-field"><?php echo $partner['identity']['pan'] ?: 'N/A'; ?></td></tr>
        </table>

        <div class="section-title">Registered Office</div>
        <p>Astra Tower, Unit No. ASO-303, 3rd Floor, Astra Tower, New Town, North 24 Parganas – 700161, West Bengal, India</p>

        <div class="section-title">Partner’s Office Address</div>
        <p class="data-field">
            <?php
                $addr = $partner['address_office']['address'] ? $partner['address_office'] : $partner['address_permanent'];
                echo $addr['address'] . ', ' . $addr['city'] . ', ' . $addr['state'] . ' – ' . $addr['pincode'];
            ?>
        </p>

        <div class="section-title">Objective</div>
        <p>The objective of this partnership is to create mutual growth opportunities through collaboration in the Indian financial services sector. Both parties agree to work jointly toward establishing a strong market presence by offering financial products such as loans, insurance, credit cards, and other financial services, excluding group loans.</p>

        <div class="section-title">Terms of Software & Services</div>
        <ul>
            <li>The software platform shall be provided exclusively by Sunglory Software Private Limited.</li>
            <li>All services shall be delivered through the Incomekaro platform.</li>
            <li>All payments made by the Partner are non-refundable.</li>
            <li>The Partner is granted only a limited software usage license, free of charge, strictly for authorized business purposes.</li>
            <li>Ownership of the software and platform remains entirely with Sunglory Software Private Limited.</li>
        </ul>

        <div class="section-title">Non-Disclosure Agreement (NDA)</div>
        <p>Any information shared by the Company—whether written, verbal, digital, or physical—shall be treated as confidential and proprietary. This includes, but is not limited to:</p>
        <ul>
            <li>Trade secrets</li>
            <li>Pricing and commission models</li>
            <li>Marketing strategies</li>
            <li>Client and financial data</li>
            <li>Technical knowledge and operational processes</li>
        </ul>
        <p>The Partner agrees not to disclose, reproduce, copy, or distribute any confidential information except when required for authorized business transactions.</p>

        <div class="section-title">Company’s Obligations</div>
        <ul>
            <li>Provide updated information on available products, services, and commission structures.</li>
            <li>Pay commissions to the Partner upon confirmed disbursement by banks or NBFCs.</li>
            <li>Adjust commissions in cases of refunds or chargebacks.</li>
            <li>Resolve commission disputes or technical issues within 30 working days.</li>
        </ul>

        <div class="section-title">Partner’s Obligations</div>
        <ul>
            <li>Refrain from misleading customers or making false promises.</li>
            <li>Avoid collecting advance fees or charges from customers without written approval.</li>
            <li>Ensure all submitted customer documentation is genuine and verified.</li>
            <li>Maintain professional conduct and comply with all applicable guidelines.</li>
        </ul>

        <div class="section-title">Commission Structure</div>
        <ul>
            <li>Commissions shall be paid according to the Partner’s selected plan.</li>
            <li>Commission rates are subject to change based on bank/NBFC policies.</li>
            <li>All commission payouts are subject to applicable taxes and statutory deductions.</li>
        </ul>

        <div class="section-title">Termination</div>
        <p>The Company reserves the right to terminate this Agreement immediately in cases of misrepresentation, breach of confidentiality, unethical conduct, or violation of financial norms.</p>

        <div class="section-title">Signature & Acceptance</div>
        <p>IN WITNESS WHEREOF, the parties have executed this Agreement as of the effective date mentioned below.</p>
        <p><strong>Effective Date:</strong> <?php echo date('jS F Y, H:i:s', strtotime($partner['agreement_accepted_at'] ?? 'now')); ?></p>

        <div class="signature-box">
            <div style="text-align: center; color: #6A5ACD; font-weight: bold; margin-bottom: 15px;">WELCOME TO THE FAMILY OF SUNGLORY SOFTWARE PRIVATE LIMITED</div>

            <div class="signature-row">
                <div class="sig-col">
                    <img src="<?php echo get_base64_image($pratap_sig_path); ?>" class="sig-img"><br>
                    <strong>Pratap Mondal</strong><br>
                    <span style="font-size: 10px; color: #666;">CEO</span>
                </div>
                <div class="sig-col">
                    <img src="<?php echo get_base64_image($suraj_sig_path); ?>" class="sig-img"><br>
                    <strong>Suraj Kar</strong><br>
                    <span style="font-size: 10px; color: #666;">CEO</span>
                </div>
            </div>

            <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #888;">
                Note: This Agreement has been digitally signed and accepted by the Partner via email-based authentication.
            </div>
        </div>
    </div>

    <div class="footer">
        Incomekaro | https://incomekaro.in | © <?php echo date('Y'); ?> Sunglory Software Private Limited. All Rights Reserved.
    </div>
</body>
</html>
