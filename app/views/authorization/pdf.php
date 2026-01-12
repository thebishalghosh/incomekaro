<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; line-height: 1.6; color: #333; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #6A5ACD; padding-bottom: 20px; }
        .logo { max-height: 60px; margin-bottom: 10px; }
        .title { font-size: 20px; font-weight: bold; text-transform: uppercase; color: #6A5ACD; margin: 0; }
        .ref { text-align: right; margin-bottom: 20px; font-weight: bold; }
        .content { margin-bottom: 40px; text-align: justify; }
        .recipient { margin-bottom: 20px; font-weight: bold; font-size: 14px; }
        .subject { font-weight: bold; text-decoration: underline; margin-bottom: 20px; }
        .signature-area { margin-top: 60px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; height: 40px; border-top: 1px solid #ccc; text-align: center; font-size: 10px; color: #666; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <img src="<?php echo $company['logo']; ?>" class="logo"><br>
        <h1 class="title">Letter of Authorization</h1>
        <div><?php echo $company['name']; ?></div>
        <div style="font-size: 10px;"><?php echo $company['address']; ?></div>
    </div>

    <div class="ref">
        Date: <?php echo date('d F Y'); ?><br>
        Ref No: AUTH/<?php echo date('Y'); ?>/<?php echo $partner['id']; ?>
    </div>

    <div class="recipient">
        To,<br>
        <?php echo $partner['profile']['full_name']; ?><br>
        Partner ID: <?php echo $partner['id']; ?><br>
        <?php echo $partner['address_permanent']['city']; ?>, <?php echo $partner['address_permanent']['state']; ?>
    </div>

    <div class="subject">Subject: Authorization to act as Business Partner</div>

    <div class="content">
        <p>Dear Partner,</p>

        <p>We are pleased to inform you that <strong><?php echo $company['name']; ?></strong> has authorized you to act as our official Business Partner for the promotion and distribution of our financial products and services.</p>

        <p>This authorization is valid from <strong><?php echo date('d F Y', strtotime($partner['created_at'])); ?></strong> and shall remain in force subject to the terms and conditions of the Business Partnership Agreement signed by you.</p>

        <p>As an authorized partner, you are permitted to:</p>
        <ul>
            <li>Represent <?php echo $company['name']; ?> for the purpose of sourcing potential clients.</li>
            <li>Collect necessary documentation (KYC) from customers for processing applications.</li>
            <li>Use the provided marketing materials and software platform for business operations.</li>
        </ul>

        <p>Please note that this authorization does not grant you the right to collect cash/fees from customers on behalf of the company unless explicitly approved in writing.</p>

        <p>We look forward to a long and successful association.</p>

        <p>Sincerely,</p>
    </div>

    <div class="signature-area">
        <div style="height: 60px;">
            <?php if (!empty($company['signature_url'])): ?>
                <img src="<?php echo $company['signature_url']; ?>" style="max-height: 60px;">
            <?php endif; ?>
        </div>
        <strong><?php echo $company['signatory_name']; ?></strong><br>
        <?php echo $company['signatory_designation']; ?><br>
        <?php echo $company['name']; ?>
    </div>

    <div class="footer">
        This is a computer-generated document. | <?php echo $_SERVER['HTTP_HOST']; ?>
    </div>

</body>
</html>
