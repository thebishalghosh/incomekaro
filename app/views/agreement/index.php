<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Agreement - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <style>
        body {
            background-color: var(--light-bg);
        }
        .agreement-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .agreement-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .agreement-content {
            padding: 40px;
            background-color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.7;
            color: #444;
            max-height: 600px;
            overflow-y: auto;
            border-bottom: 1px solid #eee;
        }
        /* Custom Scrollbar */
        .agreement-content::-webkit-scrollbar {
            width: 8px;
        }
        .agreement-content::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .agreement-content::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }
        .agreement-content::-webkit-scrollbar-thumb:hover {
            background: #aaa;
        }

        .agreement-title {
            text-align: center;
            text-transform: uppercase;
            font-weight: 800;
            margin-bottom: 5px;
            color: var(--primary-color);
            letter-spacing: 1px;
        }
        .agreement-subtitle {
            text-align: center;
            font-weight: 600;
            margin-bottom: 40px;
            color: #666;
        }
        .section-title {
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 30px;
            margin-bottom: 15px;
            color: var(--dark-color);
            border-left: 4px solid var(--accent-color);
            padding-left: 10px;
        }
        .data-field {
            font-weight: 700;
            color: var(--primary-color);
        }
        .signature-block {
            margin-top: 40px;
        }
        .signature-block img {
            max-height: 70px;
            margin-bottom: 10px;
        }
        .welcome-box {
            background-color: var(--light-bg);
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 25px;
            margin-top: 40px;
        }
        .btn-accept {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 12px 40px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: transform 0.2s;
        }
        .btn-accept:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="text-center mb-4">
                <img src="<?php echo asset('images/logo.png'); ?>" alt="<?php echo SITE_NAME; ?> Logo" style="max-height: 60px;">
            </div>

            <div class="card agreement-card">
                <div class="agreement-header">
                    <h3 class="mb-0 fw-bold"><i class="fas fa-file-contract me-2"></i> Review & Accept Agreement</h3>
                    <p class="mb-0 opacity-75">Please read the terms carefully before proceeding.</p>
                </div>

                <div class="agreement-content">

                    <h4 class="agreement-title">BUSINESS PARTNERSHIP AGREEMENT</h4>
                    <div class="agreement-subtitle">Sunglory Software Private Limited (Operating as “Incomekaro”)</div>

                    <p>This Business Partnership Agreement (“Agreement”) is entered into between <strong>Sunglory Software Private Limited</strong>, operating under the brand name <strong>Incomekaro</strong> (hereinafter referred to as the “Company”), and the Partner detailed below, effective from <span class="data-field"><?php echo date('jS F Y \a\t H:i:s'); ?></span>.</p>

                    <div class="section-title">PARTNER DETAILS</div>
                    <p>
                        Name: <span class="data-field"><?php echo $partner['profile']['full_name']; ?></span><br>
                        Partner ID: <span class="data-field"><?php echo $partner['id']; ?></span><br>
                        Aadhaar Number: <span class="data-field"><?php echo $partner['identity']['aadhaar'] ?: 'N/A'; ?></span><br>
                        PAN Number: <span class="data-field"><?php echo $partner['identity']['pan'] ?: 'N/A'; ?></span>
                    </p>

                    <div class="section-title">REGISTERED OFFICE OF THE COMPANY</div>
                    <p>
                        Astra Tower, Unit No. ASO-303,<br>
                        3rd Floor, Astra Tower, New Town,<br>
                        North 24 Parganas – 700161, West Bengal, India
                    </p>

                    <div class="section-title">PARTNER’S OFFICE ADDRESS</div>
                    <p>
                        <span class="data-field">
                            <?php
                                $addr = $partner['address_office']['address'] ? $partner['address_office'] : $partner['address_permanent'];
                                echo $addr['address'] . ',<br>' . $addr['city'] . ',<br>' . $addr['state'] . ' – ' . $addr['pincode'];
                            ?>
                        </span>
                    </p>

                    <div class="section-title">OBJECTIVE</div>
                    <p>The objective of this partnership is to create mutual growth opportunities through collaboration in the Indian financial services sector. Both parties agree to work jointly toward establishing a strong market presence by offering financial products such as loans, insurance, credit cards, and other financial services, excluding group loans.</p>

                    <div class="section-title">TERMS OF SOFTWARE & SERVICES</div>
                    <ul>
                        <li>The software platform shall be provided exclusively by Sunglory Software Private Limited.</li>
                        <li>All services shall be delivered through the Incomekaro platform.</li>
                        <li>All payments made by the Partner are non-refundable.</li>
                        <li>The Partner is granted only a limited software usage license, free of charge, strictly for authorized business purposes.</li>
                        <li>Ownership of the software and platform remains entirely with Sunglory Software Private Limited.</li>
                    </ul>

                    <div class="section-title">NON-DISCLOSURE AGREEMENT (NDA)</div>
                    <p>Any information shared by the Company—whether written, verbal, digital, or physical—shall be treated as confidential and proprietary. This includes, but is not limited to:</p>
                    <ul>
                        <li>Trade secrets</li>
                        <li>Pricing and commission models</li>
                        <li>Marketing strategies</li>
                        <li>Client and financial data</li>
                        <li>Technical knowledge and operational processes</li>
                    </ul>
                    <p>The Partner agrees not to disclose, reproduce, copy, or distribute any confidential information except when required for authorized business transactions.</p>
                    <p>Confidentiality obligations shall not apply to information that:</p>
                    <ul>
                        <li>Is publicly available at the time of disclosure, or</li>
                        <li>Was already lawfully known to the Partner without breach of obligation.</li>
                    </ul>
                    <p>Any unauthorized disclosure shall result in legal action.</p>

                    <div class="section-title">COMPANY’S OBLIGATIONS</div>
                    <p>The Company agrees to:</p>
                    <ul>
                        <li>Provide updated information on available products, services, and commission structures.</li>
                        <li>Pay commissions to the Partner upon confirmed disbursement by banks or NBFCs, as per the selected plan (monthly or instant).</li>
                        <li>Adjust commissions in cases of refunds or chargebacks, deducting such amounts from subsequent payouts.</li>
                        <li>Resolve commission disputes or technical issues within 30 working days of the complaint being raised.</li>
                        <li>Apply upgraded commission plans immediately from the date of upgrade.</li>
                        <li>Deduct unpaid EMIs or defaults of end consumers from future commissions, where applicable.</li>
                    </ul>

                    <div class="section-title">PARTNER’S OBLIGATIONS</div>
                    <p>The Partner agrees to:</p>
                    <ul>
                        <li>Refrain from misleading customers or making false promises. Loan approval and disbursement are subject to bank/NBFC policies and cannot be guaranteed.</li>
                        <li>Avoid collecting advance fees or charges from customers without written approval from the Company.</li>
                        <li>Ensure all submitted customer documentation, including KYC and leads, is genuine and verified.</li>
                        <li>Maintain professional conduct and comply with all applicable financial and regulatory guidelines.</li>
                    </ul>

                    <div class="section-title">COMMISSION STRUCTURE</div>
                    <ul>
                        <li>Commissions shall be paid according to the Partner’s selected plan (monthly or instant).</li>
                        <li>Upon upgrading from Silver or Gold (monthly) to Platinum (instant) plans, instant commission payments shall apply from the upgrade date. Pending monthly commissions shall be settled accordingly.</li>
                        <li>Commission rates are subject to change based on bank/NBFC policies or product-specific variations.</li>
                        <li>Insurance commissions may vary up to 35%, depending on the product.</li>
                        <li>All commission payouts are subject to applicable taxes and statutory deductions.</li>
                    </ul>

                    <div class="section-title">INTELLECTUAL PROPERTY RIGHTS</div>
                    <ul>
                        <li>All intellectual property, including software, applications, designs, trademarks, and trade names, shall remain the exclusive property of Sunglory Software Private Limited (Incomekaro).</li>
                        <li>The Partner is granted limited usage rights solely for approved business operations.</li>
                        <li>Any copying, modification, reverse engineering, or unauthorized distribution is strictly prohibited and may result in termination and legal action.</li>
                    </ul>

                    <div class="section-title">CONFIDENTIALITY & SECURITY</div>
                    <p>Both parties agree to maintain strict confidentiality of all proprietary business information. Any unauthorized use, sharing, or disclosure shall result in immediate termination and legal remedies.</p>

                    <div class="section-title">GOVERNING LAW & DISPUTE RESOLUTION</div>
                    <p>This Agreement shall be governed by the laws applicable within the jurisdiction of Sunglory Software Private Limited (Incomekaro). All disputes shall be resolved through arbitration, following procedures prescribed by the Company.</p>

                    <div class="section-title">TERMINATION</div>
                    <p>The Company reserves the right to terminate this Agreement immediately in cases of:</p>
                    <ul>
                        <li>Misrepresentation or misleading commitments</li>
                        <li>Breach of confidentiality</li>
                        <li>Unethical or unprofessional conduct</li>
                        <li>Violation of financial or regulatory norms</li>
                    </ul>

                    <div class="section-title">MISCELLANEOUS</div>
                    <ul>
                        <li>This Agreement is executed and accepted via internet-based OTP authentication.</li>
                        <li>The Company does not guarantee loan approvals or disbursements, as final decisions rest solely with banks/NBFCs.</li>
                    </ul>

                    <div class="section-title">SIGNATURE & ACCEPTANCE</div>
                    <p>IN WITNESS WHEREOF, the parties have executed this Agreement as of the effective date mentioned below.</p>
                    <p><strong>Effective Date:</strong> <?php echo date('jS F Y, H:i:s'); ?></p>

                    <div class="welcome-box">
                        <h5 class="text-center fw-bold" style="color: var(--primary-color);">WELCOME TO THE FAMILY OF SUNGLORY SOFTWARE PRIVATE LIMITED (INCOMEKARO)</h5>

                        <div class="row mt-5">
                            <div class="col-6 text-center signature-block">
                                <img src="<?php echo asset('images/PratapMondal.png'); ?>" alt="Pratap Mondal Signature">
                                <p class="mt-2 mb-0"><strong>Pratap Mondal</strong></p>
                                <p class="small text-muted">CEO</p>
                            </div>
                            <div class="col-6 text-center signature-block">
                                <img src="<?php echo asset('images/SurajKar.png'); ?>" alt="Suraj Kar Signature">
                                <p class="mt-2 mb-0"><strong>Suraj Kar</strong></p>
                                <p class="small text-muted">CEO</p>
                            </div>
                        </div>

                        <p class="text-center mt-4 small text-muted"><i class="fas fa-lock me-1"></i> This Agreement has been digitally signed and accepted by the Partner via email-based authentication.</p>
                    </div>

                    <div class="text-center mt-4 small text-muted">
                        Incomekaro | https://incomekaro.in<br>
                        © <?php echo date('Y'); ?> Sunglory Software Private Limited. All Rights Reserved.
                    </div>

                </div>
                <div class="card-footer text-center p-4 bg-white">
                    <form action="<?php echo url('agreement/accept'); ?>" method="POST">
                        <div class="form-check d-inline-block mb-3 p-3 border rounded bg-light">
                            <input class="form-check-input" type="checkbox" id="agreeCheck" required>
                            <label class="form-check-label fw-bold" for="agreeCheck">
                                I have read and agree to the terms and conditions stated above.
                            </label>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-accept btn-lg text-white shadow"><i class="fas fa-check-circle me-2"></i>I Accept the Agreement</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
