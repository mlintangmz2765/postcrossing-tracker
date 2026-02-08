# Configuration Guide

This document explains the unique features and configuration options of Postcard Tracker.

## Table of Contents
- [Site Owner Configuration](#site-owner-configuration)
- [Home Location (Distance Calculation)](#home-location-distance-calculation)
- [Dual Map System (Google Maps & AMap)](#dual-map-system)
- [Currency Conversion](#currency-conversion)
- [China Viewer Detection](#china-viewer-detection)
- [Security & PII Encryption](#security--pii-encryption)

---

## Site Owner Configuration

Personalize the site by setting these values in your `.env` file:

```env
OWNER_NAME="Your Full Name"
OWNER_EMAIL="your_email@example.com"
OWNER_USERNAME="your_postcrossing_username"
OWNER_CONTACT_EMAIL="public_contact@example.com"
```

| Variable | Used For |
|----------|----------|
| `OWNER_NAME` | Email notifications, footer credits |
| `OWNER_EMAIL` | Arrival notification recipient |
| `OWNER_USERNAME` | Public gallery header, footer |
| `OWNER_CONTACT_EMAIL` | Public contact button (can differ from OWNER_EMAIL) |

---

## Home Location (Distance Calculation)

The app calculates travel distance from your home location to each postcard destination.

### Configuration

Set your home coordinates in `.env`:
```env
HOME_LAT=0.000000
HOME_LNG=0.000000
```

> 💡 **Tip:** Find your coordinates at [latlong.net](https://www.latlong.net/)

> ⚠️ **Privacy:** These values are in `.env` which is git-ignored, so your exact location won't be exposed in the public repository.

---

## The Map System

This site uses two map providers to make sure everyone can see the data correctly:

- **Google Maps**: Used for everywhere except China.
- **AMap (Gaode)**: Used for Chinese addresses and viewers because Google Maps is blocked there.

### How it works:

1. **Geocoding** (`app/Services/GeocodingService.php`):
   The app checks if an address looks like it's in China. If it is, it uses AMap; otherwise, it hits the Google Maps API.

2. **Map Display** (Blade views):
   The site tries to guess where the visitor is from using the Cloudflare `CF-IPCountry` header. If they're in China, it loads the AMap JS SDK. You can manually force this by adding `?china=1` or `?china=0` to the URL.

### API Keys:

```env
# Google (Global)
GOOGLE_API_KEY=your_key

# AMap (China)
AMAP_JS_KEY=your_js_key
AMAP_WEB_KEY=your_web_service_key
```

---

## Tracking Costs & Currency

When you get a card, you can record exactly what the stamp cost in the sender's currency.

- `nilai_asal`: The price on the stamp (e.g., 5.00).
- `mata_uang`: The currency code (USD, EUR, CNY, etc.).
- `kurs_idr`: The exchange rate you used at that time.
- `biaya_prangko`: The final cost in IDR (calculated as `nilai_asal × kurs_idr`).

---

## China Viewer Detection

Since Google Maps doesn't work in mainland China, we have to detect those visitors automatically.

1. **URL Override**: If you share a link with `?china=1`, it'll always show AMap.
2. **Session**: Once detected, we save it in your session so the site stays consistent.
3. **Cloudflare**: We use the `CF-IPCountry` header to catch visitors from China even if they don't have the URL param.

---

## Customization Tips

### Adding New Countries
Edit `countries` table in database. Required fields:
- `nama_indonesia` - Country name in Indonesian
- `nama_inggris` - Country name in English  
- `kode_iso` - ISO 3166-1 alpha-2 code
- `benua` - Continent
- `subbenua` - Subregion

### Changing Theme Colors
Edit `resources/css/app.css` - the app uses a vintage postcard theme with warm colors.

### Email Notifications
Configure in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=465
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="your_email"
MAIL_FROM_NAME="Postcard Tracker"
```

Recipient email uses `OWNER_EMAIL` from your `.env` file, configured in `config/app.php`.

---

## Database Normalization (3NF)

The database structure is designed with strict relational normalization (3rd Normal Form):

| Change | Before | After |
|--------|--------|-------|
| **Contacts** | String name/phone in `postcards` | Unified `contacts` table |
| **Countries** | String name in `postcards` | Relation to `countries` table (`country_id`) |
| **Coordinates** | Snapshotted in `postcards` | Centralized in `contacts` |

### Benefits:
- **Efficiency**: Standard SQL JOINs allow for faster global stats and filtering.
- **Data Integrity**: Country names are validated against the master `countries` list.
- **Maintenance**: Updating a contact's address automatically reflects (if desired) or can be managed versioned.

---

## Security & PII Encryption

The app implements **Encryption at Rest** for Personal Identifiable Information (PII).

### Encrypted Fields:
- **Contacts**: `nama_kontak`, `alamat`, `nomor_telepon`, `lat`, `lng`

### Architectural Impact:
Because fields are encrypted in the database, SQL-level operations like `WHERE alamat LIKE '%...%'` are no longer possible directly in the database.

**The app handles this by:**
1. Fetching candidate records based on non-encrypted fields (like `country_id`).
2. Decrypting PII in the Laravel Model.
3. Performing secondary searches and distance calculations in **PHP memory**.

### artisan commands:
- `php artisan migrate`: Applies the database schema and structural foundations.
- `php artisan pii:encrypt`: Scans the database for plain-text PII and encrypts it using your `APP_KEY` (useful for initial data population).

> ⚠️ **CRITICAL:** Do NOT change your `APP_KEY` after data is encrypted. If you lose the key, you lose access to the data.
