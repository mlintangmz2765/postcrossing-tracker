<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Postcard Tracker</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Special+Elite&family=Quicksand:wght@400;600&display=swap');

        body {
            background-color: #fdf6e3;
            background-image: 
                linear-gradient(#e5e5e5 1.1px, transparent 1.1px), 
                linear-gradient(90deg, #e5e5e5 1.1px, transparent 1.1px);
            background-size: 30px 30px;
            font-family: 'Quicksand', sans-serif;
            color: #2c3e50;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .error-card {
            background: #fff;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            position: relative;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
            text-align: center;
            transform: rotate(-1deg);
        }

        /* Airmail Border Top */
        .error-card::before {
             content: ""; position: absolute; top: 0; left: 0; right: 0; height: 8px;
             background: repeating-linear-gradient(45deg, #e63946, #e63946 15px, #fff 15px, #fff 25px, #457b9d 25px, #457b9d 40px, #fff 40px, #fff 50px);
        }
        /* Airmail Border Bottom */
        .error-card::after {
             content: ""; position: absolute; bottom: 0; left: 0; right: 0; height: 8px;
             background: repeating-linear-gradient(-45deg, #e63946, #e63946 15px, #fff 15px, #fff 25px, #457b9d 25px, #457b9d 40px, #fff 40px, #fff 50px);
        }

        h1 {
            font-family: 'Special Elite', cursive;
            font-size: 6rem;
            color: #e63946;
            margin: 0;
            line-height: 1;
            text-shadow: 3px 3px 0px rgba(0,0,0,0.1);
        }

        h2 {
            font-family: 'Special Elite', cursive;
            color: #2c3e50;
            margin-top: 10px;
            font-size: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 2px dashed #ccc;
            display: inline-block;
            padding-bottom: 5px;
        }

        p { color: #64748b; font-size: 1.1rem; margin-top: 20px; line-height: 1.6; }

        .stamp-mark {
            width: 120px; height: 120px;
            border: 4px double #ccc;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column;
            position: absolute; top: -40px; right: -40px;
            background: rgba(255,255,255,0.9);
            transform: rotate(15deg);
            color: #ccc;
            font-family: 'Special Elite';
            font-weight: bold;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            z-index: 10;
        }

        .btn-home {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 30px;
            background: #2c3e50;
            color: white;
            text-decoration: none;
            font-family: 'Special Elite', cursive;
            font-size: 1rem;
            border-radius: 4px;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-home:hover {
            background: #e63946;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230, 57, 70, 0.3);
        }

        @media(max-width: 600px) {
            .stamp-mark { width: 80px; height: 80px; font-size: 0.7rem; right: -10px; top: -30px; }
            h1 { font-size: 4rem; }
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="stamp-mark">
            @yield('stamp')
        </div>

        <h1>@yield('code')</h1>
        <h2>@yield('message')</h2>
        
        <p>@yield('description')</p>

        <a href="{{ url('/') }}" class="btn-home">Return to Home</a>
    </div>
</body>
</html>
