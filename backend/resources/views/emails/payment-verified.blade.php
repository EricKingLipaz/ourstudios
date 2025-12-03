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
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
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
            <h1>✅ Payment Verified</h1>
        </div>
        
        <div class="content">
            <h2>Hello {{ $booking->customer_name }},</h2>
            
            <div class="success-box">
                <h3 style="margin-top: 0; color: #155724;">Payment Confirmed!</h3>
                <p>Your payment for booking <strong>{{ $booking->booking_reference }}</strong> has been verified and confirmed.</p>
            </div>

            <p><strong>Booking Details:</strong></p>
            <ul>
                <li>Service: {{ $booking->service->name }}</li>
                <li>Start Date: {{ $booking->booking_start->format('d M Y, H:i') }}</li>
                <li>Amount Paid: R{{ number_format($booking->total_amount, 2) }}</li>
            </ul>

            <p>We look forward to seeing you at the studio!</p>

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
