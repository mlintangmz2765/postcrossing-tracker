<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postcard Arrived!</title>
    <style>
        /* Base styles */
        body {
            margin: 0;
            padding: 0;
            background-color: #fdf6e3;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* Container with Airmail Border */
        .wrapper {
            width: 100%;
            background-color: #fdf6e3;
            padding: 40px 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            position: relative;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        /* Airmail decorative border */
        .airmail-header {
            height: 12px;
            background: repeating-linear-gradient(
                -45deg,
                #457b9d 0, #457b9d 15px,
                #fff 15px, #fff 25px,
                #e63946 25px, #e63946 40px,
                #fff 40px, #fff 50px
            );
        }

        /* Content Area */
        .content {
            padding: 40px;
            background-image: linear-gradient(rgba(0,0,0,0.01) 1px, transparent 1px);
            background-size: 100% 30px;
        }

        h1 {
            font-family: 'Dancing Script', cursive, serif;
            color: #2c3e50;
            font-size: 32px;
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
        }

        .intro {
            color: #4a5568;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
            text-align: center;
        }

        /* Postcard-like details box */
        .postcard-box {
            background-color: #fcf9f2;
            border: 2px solid #e2e8f0;
            border-radius: 4px;
            padding: 25px;
            position: relative;
        }

        .detail-row {
            margin-bottom: 12px;
            border-bottom: 1px dashed #cbd5e0;
            padding-bottom: 5px;
        }

        .label {
            font-size: 11px;
            text-transform: uppercase;
            color: #718096;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .value {
            color: #2d3748;
            font-size: 18px;
            font-family: 'Courier New', Courier, monospace;
        }

        .message-box {
            margin-top: 25px;
            font-style: italic;
            color: #4a5568;
            font-size: 17px;
            line-height: 1.5;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #edf2f7;
            border-radius: 4px;
            position: relative;
        }

        .quote-icon {
            color: #cbd5e0;
            font-size: 40px;
            position: absolute;
            top: -10px;
            left: 10px;
            opacity: 0.5;
            font-family: serif;
        }

        /* Footer */
        .footer {
            padding: 25px;
            text-align: center;
            color: #a0aec0;
            font-size: 12px;
        }

        .btn {
            display: inline-block;
            background-color: #457b9d;
            color: #ffffff;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 30px;
        }

        /* Responsive */
        @media screen and (max-width: 640px) {
            .content { padding: 25px; }
            .postcard-box { padding: 15px; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="airmail-header"></div>
            
            <div class="content">
                <h1>📬 Greeting from Afar!</h1>
                
                <p class="intro">
                    Hello, <strong>{{ $ownerName }}</strong>!<br>
                    Your postcard from <strong>{{ $postcard->country?->nama_indonesia ?? 'somewhere special' }}</strong> has just arrived safely in your mailbox.
                </p>

                <div class="postcard-box">
                    <div class="detail-row">
                        <div class="label">Postcard ID</div>
                        <div class="value">{{ $postcard->postcard_id ?? 'Direct Swap' }}</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="label">From / Recipient Name</div>
                        <div class="value">{{ $postcard->contact?->nama_kontak ?? 'Anonymous' }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="label">Arrival Date</div>
                        <div class="value">{{ $postcard->tanggal_terima->format('d M Y') }}</div>
                    </div>

                    <div class="message-box">
                        <span class="quote-icon">“</span>
                        {{ $senderMessage }}
                    </div>
                </div>

                <div style="text-align: center;">
                    <a href="{{ config('app.url') }}/view/{{ $postcard->id }}" class="btn">View Collection</a>
                </div>
            </div>

            <div class="footer">
                Sent automatically by <strong>Postcard Tracker</strong><br>
                &copy; {{ date('Y') }} mlintangmz. Every postcard tells a story.
            </div>
        </div>
    </div>
</body>
</html>
