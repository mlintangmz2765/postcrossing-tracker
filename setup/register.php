<?php
/**
 * First-Time Setup Script - Web Version
 * 
 * Access this via browser to create the first admin account.
 * DELETE THIS ENTIRE 'setup' FOLDER AFTER RUNNING!
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$status = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Username and password are required!";
    } else {
        try {
            // Check if user already exists
            $existingUser = DB::table('users')->where('username', $username)->first();

            if ($existingUser) {
                $error = "User '{$username}' already exists!";
            } else {
                DB::table('users')->insert([
                    'username' => $username,
                    'password' => Hash::make($password),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $status = "success";
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postcard Tracker | Initial Setup</title>
    <link href="https://fonts.googleapis.com/css2?family=Special+Elite&family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f4ece1;
            --paper-color: #fff9f0;
            --border-color: #d1c1a8;
            --accent-red: #b93d3d;
            --accent-blue: #457b9d;
            --text-main: #3d352a;
        }

        body {
            background-color: var(--bg-color);
            background-image: radial-gradient(#d1c1a8 0.5px, transparent 0.5px);
            background-size: 20px 20px;
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .setup-card {
            background: var(--paper-color);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            border: 1px solid var(--border-color);
            box-shadow: 10px 10px 0px rgba(61, 53, 42, 0.05);
            position: relative;
            transform: rotate(-1deg);
        }

        .setup-card::before {
            content: "";
            position: absolute;
            top: 10px; right: 10px;
            width: 60px; height: 75px;
            border: 2px dashed var(--border-color);
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="%23d1c1a8" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>');
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.5;
        }

        h1 {
            font-family: 'Special Elite', cursive;
            font-size: 1.8rem;
            margin-bottom: 5px;
            color: var(--accent-blue);
        }

        .subtitle {
            font-size: 0.9rem;
            opacity: 0.7;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            font-size: 0.85rem;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        input {
            width: 100%;
            padding: 12px;
            background: transparent;
            border: 1px solid var(--border-color);
            box-sizing: border-box;
            font-family: 'Special Elite', cursive;
            font-size: 1.1rem;
        }

        input:focus {
            outline: none;
            border-color: var(--accent-blue);
            background: #fff;
        }

        .btn-submit {
            background: var(--accent-blue);
            color: white;
            border: none;
            padding: 15px 25px;
            font-family: 'Special Elite', cursive;
            font-size: 1.1rem;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            background: #346280;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .alert {
            padding: 15px;
            margin-bottom: 25px;
            border: 1px solid transparent;
            font-family: 'Special Elite', cursive;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .warning-footer {
            margin-top: 30px;
            font-size: 0.8rem;
            color: var(--accent-red);
            font-weight: bold;
            text-align: center;
            border-top: 1px dashed var(--border-color);
            padding-top: 15px;
        }

        .paravion {
            display: inline-block;
            background: var(--accent-blue);
            color: white;
            padding: 2px 10px;
            font-size: 0.7rem;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="setup-card">
    <div class="paravion">BY AIR MAIL • PAR AVION</div>
    <h1>Account Setup</h1>
    <p class="subtitle">Create your initial administrator account.</p>

    <?php if ($status === 'success'): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i> Success! Admin account created.<br>
            <small>You can now login at the portal.</small>
        </div>
        <p style="text-align: center; font-family: 'Special Elite';">
            <a href="../login" style="color: var(--accent-blue); text-decoration: none;">&larr; Go to Login</a>
        </p>
    <?php else: ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Set Username</label>
                <input type="text" name="username" id="username" placeholder="e.g. admin" required>
            </div>
            <div class="form-group">
                <label for="password">Set Password</label>
                <input type="password" name="password" id="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-submit">Initialize System</button>
        </form>
    <?php endif; ?>

    <div class="warning-footer">
        <i class="bi bi-shield-lock-fill"></i> SECURITY WARNING:<br>
        Delete this 'setup' folder immediately after registration.
    </div>
</div>

</body>
</html>
