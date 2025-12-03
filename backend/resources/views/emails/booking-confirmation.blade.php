<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .booking-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            color: #667eea;
        }
        .bank-details {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .bank-details h3 {
            margin-top: 0;
            color: #856404;
        }
        .bank-row {
            padding: 8px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎵 LiveNetStudios</h1>
            <p>Booking Confirmation</p>
        </div>
        
        <div class="content">
            <h2>Thank you for booking with LiveNetStudios!</h2>
            <p>Your booking has been confirmed. Below are the details:</p>
            
            <div class="booking-details">
                <div class="detail-row">
                    <span class="detail-label">Booking Reference:</span>
                    <span>{{ $booking->booking_reference }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Service:</span>
                    <span>{{ $booking->service->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Start Date:</span>
                    <span>{{ $booking->booking_start->format('d M Y, H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">End Date:</span>
                    <span>{{ $booking->booking_end->format('d M Y, H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span><strong>R{{ number_format($booking->total_amount, 2) }}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Method:</span>
                    <span>{{ $booking->paymentMethod->name }}</span>
                </div>
            </div>

            @if(in_array($booking->paymentMethod->code, ['bank_transfer', 'eft']) && $bankDetails)
                <div class="bank-details">
                    <h3>💳 Payment Instructions - Bank Transfer/EFT</h3>
                    <p>Please transfer the amount to:</p>
                    <div class="bank-row">
                        <strong>Account Name:</strong> {{ $bankDetails->account_holder_name }}
                    </div>
                    <div class="bank-row">
                        <strong>Bank:</strong> {{ $bankDetails->bank_name }}
                    </div>
                    <div class="bank-row">
                        <strong>Account Number:</strong> {{ $bankDetails->account_number }}
                    </div>
                    <div class="bank-row">
                        <strong>Branch Code:</strong> {{ $bankDetails->branch_code }}
                    </div>
                    <div class="bank-row">
                        <strong>Reference:</strong> {{ $booking->booking_reference }}
                    </div>
                    <p style="margin-top: 15px; color: #856404;">
                        ⚠️ <strong>Important:</strong> Please email your Proof of Payment to 
                        <a href="mailto:diamondlipaz@gmail.com">diamondlipaz@gmail.com</a>
                    </p>
                </div>
            @endif

            @if($booking->paymentMethod->code === 'cash_send' && $bankDetails)
                <div class="bank-details">
                    <h3>📱 Payment Instructions - Cash Send</h3>
                    <p>Please send cash to:</p>
                    <div class="bank-row">
                        <strong>Mobile Number:</strong> {{ $bankDetails->cash_send_mobile }}
                    </div>
                    <div class="bank-row">
                        <strong>Reference:</strong> {{ $booking->booking_reference }}
                    </div>
                    <p style="margin-top: 15px; color: #856404;">
                        ⚠️ <strong>Important:</strong> Please email the Cash Send reference/PIN to 
                        <a href="mailto:diamondlipaz@gmail.com">diamondlipaz@gmail.com</a>
                    </p>
                </div>
            @endif

            @if($booking->paymentMethod->code === 'cash')
                <div class="bank-details">
                    <h3>💵 Payment Instructions - Cash</h3>
                    <p>Please bring <strong>R{{ number_format($booking->total_amount, 2) }}</strong> in cash to the studio.</p>
                    <p style="margin-top: 10px; color: #856404;">
                        ⚠️ Payment must be made before the session begins.
                    </p>
                </div>
            @endif

            <p style="margin-top: 30px;">
                If you have any questions, please contact us at 
                <a href="mailto:diamondlipaz@gmail.com">diamondlipaz@gmail.com</a>
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} LiveNetStudios. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
