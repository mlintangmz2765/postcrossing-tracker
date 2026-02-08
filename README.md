# Postcard Tracker 📬

A personal postcard collection management system built with Laravel and Livewire. Track your sent and received postcards, manage stamps, view statistics, and share your collection publicly.

## What's Inside?

### The Basics
- **Postcard Tracking** - Keep track of everything you send and receive in one place.
- **Photos & Stamps** - Upload high-res photos and manage a dedicated stamp gallery.
- **Maps** - Visualize your global postcard journey on interactive maps.
- **Stats & Distances** - Auto-calculate travel distances, delivery durations, and country stats.
- **Bulk Import** - Need to add many records at once? Upload a CSV to import hundreds of postcards in a single action.

### The "Special" Stuff
- **Public Gallery** - A beautiful, filtered gallery to share your collection with the world.
- **One-Click Arrival** - Recipients can confirm arrival via a unique link—no login required.
- **Email Alerts** - Get notified automatically as soon as your card is registered by the recipient.
- **China-Friendly Maps** - The site automatically switches to AMap for visitors from China (since Google Maps is blocked there).
- **Crypto-Safe Privacy** - Contact addresses and phone numbers are AES-encrypted at rest.
- **Any Currency** - Record costs in any currency (USD, EUR, CNY, etc.) with auto-conversion to IDR.

> 📖 See [Configuration Guide](docs/CONFIGURATION.md) and [International Usage Guide](docs/INTERNATIONAL.md) for customization and i18n instructions.

## Requirements

- PHP 8.2+
- MySQL/MariaDB
- Composer
- Node.js 20+

## Installation

### 1. Clone the repository
```bash
git clone https://github.com/mlintangmz2765/postcrossing-tracker.git
cd postcrossing-tracker
```

### 2. Install dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### 3. Build assets (choose one method)

**Option A: Build on server**
```bash
npm install && npm run build
```

**Option B: Upload pre-built assets**
If Node.js is not available on server, build locally then upload:
```bash
# On local machine
npm run build
# Upload public/build folder to server via SCP/SFTP
```

### 3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database and API credentials:
```env
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

GOOGLE_API_KEY=your_google_maps_key
AMAP_JS_KEY=your_amap_js_key
AMAP_WEB_KEY=your_amap_web_key

RECAPTCHA_SITE_KEY=your_recaptcha_site_key
RECAPTCHA_SECRET_KEY=your_recaptcha_secret_key

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=465
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
```

### 4. Email Configuration (SMTP)

This project uses SMTP for notifications. Choose the configuration that matches your server environment.

> **Important:** If using Cloudflare, ensure your `mail` subdomain is **DNS Only** (Grey Cloud) to avoid timeouts.

#### A. Configuration Examples

**Option 1: VPS / Dedicated Server (e.g., HestiaCP)**
```ini
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_USERNAME=postcrossing@yourdomain.com
MAIL_PASSWORD=your_secure_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="postcrossing@yourdomain.com"
```

**Option 2: Shared Hosting / External SMTP (cPanel, Gmail)**
```ini
MAIL_MAILER=smtp
MAIL_HOST=smtp.provider.com  # e.g., smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your_email@gmail.com"
```

#### B. Troubleshooting & Best Practices

| Issue | Cause | Solution |
|-------|-------|----------|
| **SSL Error** | `SSL routines::certificate verify failed` | Your mail server's SSL is invalid or missing. **Fix:** Install Let's Encrypt SSL on your mail hostname (`mail.yourdomain.com`). |
| **Bounce Error** | `550-5.7.25 Missing PTR Record` | Your VPS IP has no Reverse DNS. **Fix:** Set rDNS/PTR record in your VPS panel to match your mail hostname. |
| **Timeout** | Connection timed out | Cloudflare Proxy is ON for mail subdomain. **Fix:** Set `mail.yourdomain.com` to **DNS Only** (Grey Cloud). |

> **Hosting Multiple Domains?**
> You can host multiple email domains on one IP.
> 1. Set **PTR Record** to your main server hostname (e.g., `srv.main.com`).
> 2. For other domains, add an **SPF Record** authorizing that IP: `v=spf1 ip4:YOUR_IP -all`.

#### C. Setting up SPF Record

To ensure your emails land in the Inbox (not Spam), you **MUST** add an SPF record in your DNS Manager (Cloudflare/cPanel).

1.  **Login** to your DNS Provider (e.g., Cloudflare).
2.  **Add Record**:
    *   **Type**: `TXT`
    *   **Name**: `@` (or your domain name)
    *   **Content**: `v=spf1 ip4:YOUR_SERVER_IP -all`
3.  **Save**.

### 5. Setup database
Import the database schema or run migrations:
```bash
php artisan migrate

# 5b. SEED MASTER DATA
php artisan db:seed --class=CountrySeeder
```

### 6. Create storage link
```bash
php artisan storage:link
```

### 7. Create admin user (first-time only)
The application uses standard Laravel authentication. To create your first account:
- **Locally**: You can create a user using a seeder or `php artisan tinker`.
- **On Server**: Use the same methods or if you have the `setup/` legacy folder, it might still function if mapped.

> ⚠️ **SECURITY WARNING:** Ensure `APP_DEBUG=false` in production to prevent leaking sensitive coordinate/PII logic in error traces.

### 8. (Optional) Cache for production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 9. PII Encryption Setup
If you are importing data from plain-text sources, you may need to run:
```bash
php artisan pii:encrypt
```
This will encrypt all `alamat` (address) and `nomor_telepon` (phone) fields. **Always backup your database before running maintenance commands!**

> ⚠️ **WARNING:** Your `APP_KEY` in `.env` is used for encryption. **Never change or lose your APP_KEY**, or you will lose access to all encrypted data.

## API Keys Required

| Service | Purpose | Get Key |
|---------|---------|---------|
| Google Maps | Map display & geocoding (non-China) | [Google Cloud Console](https://console.cloud.google.com/) |
| AMap (Gaode) | Map display & geocoding (China) | [AMap Console](https://lbs.amap.com/) |
| ReCaptcha v2 | Login protection | [Google reCAPTCHA](https://www.google.com/recaptcha/) |

## Screenshots

### Home Page
![Home Page](docs/screenshots/home.png)

### Login
![Login](docs/screenshots/login.png)

### Dashboard
![Dashboard Overview](docs/screenshots/dashboard1.png)
![Dashboard Stats](docs/screenshots/dashboard2.png)
![Dashboard Table](docs/screenshots/dashboard3.png)

### Gallery
![Gallery Grid](docs/screenshots/gallery1.png)
![Gallery Detail](docs/screenshots/gallery2.png)

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Author

Built with ❤️ by [mlintangmz](https://github.com/mlintangmz2765)
