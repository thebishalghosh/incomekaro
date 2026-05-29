<?php
require_once APP_PATH . '/models/partner.php';
require_once APP_PATH . '/models/white_label.php';
require_once '../vendor/autoload.php'; // Load DomPDF

use Dompdf\Dompdf;
use Dompdf\Options;

function invoice_download() {
    require_login();

    $user = find_user_by_id($_SESSION['user_id']);
    if (empty($user['partner_id'])) {
        die('Access Denied: Not a partner.');
    }

    // Fetch Partner & Subscription Data
    $partner = get_partner_by_id($user['partner_id']);

    if (empty($partner['subscription'])) {
        die('No active subscription found.');
    }

    // Determine Company Details (Issuer)
    $company = [
        'name' => SITE_NAME,
        'address' => 'Astra Tower, Unit No. ASO-303, 3rd Floor, New Town, Kolkata - 700161',
        'gstin' => '19AABCS1234D1Z5', // Example GSTIN
        'email' => 'support@incomekaro.in',
        'phone' => '+91 786-4951-543',
        'logo' => get_logo_url() // Helper function handles WL logic
    ];

    // If White Label Partner, override company details
    if (!empty($partner['white_label_id'])) {
        $wl = get_white_label_by_id($partner['white_label_id']);
        if ($wl) {
            $company['name'] = $wl['company_name'];
            $company['email'] = $wl['support_email'];
            $landing = !empty($wl['landing_page_data']) ? json_decode($wl['landing_page_data'], true) : [];
            if (!empty($landing['contact_address'])) {
                $company['address'] = $landing['contact_address'];
            }
            if (!empty($wl['logo_url'])) {
                $company['logo'] = asset($wl['logo_url']);
            }
        }
    }

    // Calculate Tax
    $sub = $partner['subscription'];
    $amount = $sub['payment_amount'];
    $gst_rate = $sub['gst_rate'] ?? 18;
    $base_amount = $amount / (1 + ($gst_rate / 100));
    $tax_amount = $amount - $base_amount;

    // Generate Invoice Number
    $invoice_no = 'INV-' . strtoupper(substr($partner['id'], -6)) . '-' . date('ym', strtotime($sub['created_at']));

    // HTML Template
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; color: #555; font-size: 14px; line-height: 1.6; }
            .container { padding: 20px; }

            /* Header */
            .header { width: 100%; margin-bottom: 40px; border-bottom: 2px solid #E6E6FA; padding-bottom: 20px; }
            .logo { max-height: 70px; }
            .company-info { text-align: right; color: #6A5ACD; }
            .company-name { font-size: 20px; font-weight: bold; color: #483D8B; }

            /* Meta Info */
            .invoice-meta { width: 100%; margin-bottom: 40px; }
            .invoice-title { font-size: 28px; font-weight: bold; color: #6A5ACD; text-transform: uppercase; letter-spacing: 2px; }
            .bill-to-title { color: #999; font-size: 12px; text-transform: uppercase; font-weight: bold; margin-bottom: 5px; }
            .bill-to { width: 50%; vertical-align: top; }
            .invoice-details { width: 50%; text-align: right; vertical-align: top; }

            /* Table */
            .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
            .table th { background-color: #F8F9FD; color: #6A5ACD; font-weight: bold; padding: 15px; text-align: left; border-bottom: 2px solid #E6E6FA; text-transform: uppercase; font-size: 12px; }
            .table td { padding: 15px; border-bottom: 1px solid #F0F0F0; }
            .text-right { text-align: right; }

            /* Totals */
            .total-row td { font-weight: bold; font-size: 16px; color: #483D8B; border-top: 2px solid #E6E6FA; background-color: #F8F9FD; }

            /* Footer */
            .footer { position: fixed; bottom: 0; left: 0; right: 0; height: 50px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; padding-top: 15px; }

            /* Stamps */
            .status-paid { color: #28a745; font-weight: bold; border: 3px solid #28a745; padding: 10px 20px; border-radius: 8px; display: inline-block; transform: rotate(-15deg); position: absolute; top: 180px; right: 40px; font-size: 24px; opacity: 0.2; font-family: "Courier New", Courier, monospace; }
            .status-due { color: #dc3545; font-weight: bold; border: 3px solid #dc3545; padding: 10px 20px; border-radius: 8px; display: inline-block; transform: rotate(-15deg); position: absolute; top: 180px; right: 40px; font-size: 24px; opacity: 0.2; font-family: "Courier New", Courier, monospace; }
        </style>
    </head>
    <body>
        <div class="container">
            <table class="header">
                <tr>
                    <td valign="top"><img src="' . get_image_src($company['logo']) . '" class="logo"></td>
                    <td valign="top" class="company-info">
                        <div class="company-name">' . $company['name'] . '</div>
                        ' . nl2br($company['address']) . '<br>
                        ' . $company['email'] . '<br>
                    </td>
                </tr>
            </table>

            <table class="invoice-meta">
                <tr>
                    <td class="bill-to">
                        <div class="bill-to-title">Billed To</div>
                        <strong>' . $partner['profile']['full_name'] . '</strong><br>
                        ' . ($partner['address_office']['address'] ?? $partner['address_permanent']['address']) . '<br>
                        ' . ($partner['address_office']['city'] ?? '') . ', ' . ($partner['address_office']['state'] ?? '') . ' - ' . ($partner['address_office']['pincode'] ?? '') . '<br>
                        Phone: ' . $partner['profile']['mobile'] . '<br>
                    </td>
                    <td class="invoice-details">
                        <div class="invoice-title">INVOICE</div>
                        <strong>Invoice #:</strong> ' . $invoice_no . '<br>
                        <strong>Date:</strong> ' . date('d M, Y', strtotime($sub['created_at'])) . '<br>
                        <strong>Status:</strong> <span style="color: ' . ($sub['due_amount'] <= 0 ? '#28a745' : '#dc3545') . '">' . ucfirst($sub['status']) . '</span>
                    </td>
                </tr>
            </table>

            ' . ($sub['due_amount'] <= 0 ? '<div class="status-paid">PAID</div>' : '<div class="status-due">DUE</div>') . '

            <table class="table">
                <thead>
                    <tr>
                        <th width="60%">Description</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>' . $sub['plan_name'] . ' Subscription Plan</strong><br>
                            <span style="color: #888; font-size: 12px;">Lifetime Access to Financial Services Panel</span>
                        </td>
                        <td class="text-right">Rs. ' . number_format($base_amount, 2) . '</td>
                    </tr>
                    <tr>
                        <td class="text-right" style="border-bottom: none; padding-bottom: 5px;">CGST (' . ($gst_rate/2) . '%)</td>
                        <td class="text-right" style="border-bottom: none; padding-bottom: 5px;">Rs. ' . number_format($tax_amount/2, 2) . '</td>
                    </tr>
                    <tr>
                        <td class="text-right" style="border-top: none; padding-top: 5px;">SGST (' . ($gst_rate/2) . '%)</td>
                        <td class="text-right" style="border-top: none; padding-top: 5px;">Rs. ' . number_format($tax_amount/2, 2) . '</td>
                    </tr>
                    <tr class="total-row">
                        <td class="text-right">Total Amount</td>
                        <td class="text-right">Rs. ' . number_format($amount, 2) . '</td>
                    </tr>
                </tbody>
            </table>

            <div style="margin-top: 40px; background-color: #F8F9FD; padding: 15px; border-radius: 8px; border-left: 4px solid #6A5ACD;">
                <p style="margin: 0; font-size: 12px; color: #666;">
                    <strong>Payment Information:</strong><br>
                    Mode: ' . ucfirst($sub['payment_mode']) . ' | Transaction ID: ' . ($sub['transaction_id'] ?: 'N/A') . '<br>
                    Date: ' . date('d M, Y h:i A', strtotime($sub['created_at'])) . '
                </p>
            </div>

            <div class="footer">
                Thank you for your business!<br>
                This is a computer-generated invoice and does not require a physical signature.
            </div>
        </div>
    </body>
    </html>
    ';

    // Initialize DomPDF
    $options = new Options();
    $options->set('isRemoteEnabled', true); // Allow loading images from URL
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Output PDF
    $dompdf->stream("Invoice-$invoice_no.pdf", ["Attachment" => true]);
}
