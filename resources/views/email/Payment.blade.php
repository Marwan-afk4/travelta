<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Payment Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #0073e6;
            color: #fff;
            text-align: center;
            padding: 10px;
            font-size: 20px;
            border-radius: 8px 8px 0 0;
        }
        .content {
            padding: 20px;
            text-align: left;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            padding: 10px;
            margin-top: 20px;
            border-top: 1px solid #ddd;
        }
        .btn {
            display: inline-block;
            background: #0073e6;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">New Payment Received</div>
        <div class="content">
            <p>Dear Team,</p>
            <p>A new payment has been successfully processed.</p>
            <p><strong>Payment Details:</strong></p>
            <ul>
                <li><strong>Customer Name:</strong> {{ $data['name'] }}</li>
                <li><strong>Amount:</strong> ${{ $data['amount'] }}</li>  
                <li><strong>Date:</strong> {{ {{ $data['payment_date'] }} }}</li>
            </ul>
        </div>
        <div class="footer">
            &copy; {{date('Y')}} {{$data['agent']}} | All rights reserved.
        </div>
    </div>
</body>
</html>