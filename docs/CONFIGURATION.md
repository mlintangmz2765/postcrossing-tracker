# Configuration Guide

This document explains the unique features and configuration options of Postcard Tracker.

## Table of Contents
- [Site Owner Configuration](#site-owner-configuration)
- [Home Location (Distance Calculation)](#home-location-distance-calculation)
- [Dual Map System (Google Maps & AMap)](#dual-map-system)
- [Currency Conversion](#currency-conversion)
- [China Viewer Detection](#china-viewer-detection)

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
HOME_LAT=-7.756378
HOME_LNG=110.376618
```

> 💡 **Tip:** Find your coordinates at [latlong.net](https://www.latlong.net/)

> ⚠️ **Privacy:** These values are in `.env` which is git-ignored, so your exact location won't be exposed in the public repository.

---

## Dual Map System

This app uses **two map providers** for optimal coverage:

| Provider | Usage | Why |
|----------|-------|-----|
| **Google Maps** | Non-China addresses | Better global coverage |
| **AMap (Gaode)** | China addresses | Google Maps blocked in China |

### How it works:

1. **Geocoding Service** (`app/Services/GeocodingService.php`):
   - Detects if address contains China-related keywords
   - Uses AMap API for China, Google Maps for others

2. **Map Display** (Blade views):
   - Detects viewer location via Cloudflare `CF-IPCountry` header
   - Shows AMap for China viewers, Google Maps for others
   - Manual override: add `?china=1` or `?china=0` to URL

### API Keys Required:

```env
# Google Maps (for non-China)
GOOGLE_API_KEY=your_google_maps_api_key

# AMap/Gaode (for China)
AMAP_JS_KEY=your_amap_javascript_key    # For map display
AMAP_WEB_KEY=your_amap_web_service_key  # For geocoding API
```

### Getting API Keys:

**Google Maps:**
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Enable "Maps JavaScript API" and "Geocoding API"
3. Create API key with HTTP referrer restrictions

**AMap (Gaode):**
1. Register at [AMap Console](https://lbs.amap.com/) (requires Chinese phone)
2. Create application
3. Get JS API key (for frontend) and Web Service key (for geocoding)

---

## Currency Conversion

For received postcards, you can record stamp values in foreign currencies. The app stores:

| Field | Description |
|-------|-------------|
| `nilai_asal` | Original value in foreign currency |
| `mata_uang` | Currency code (e.g., CNY, USD, EUR) |
| `kurs_idr` | Exchange rate to IDR at time of recording |
| `biaya_prangko` | Calculated IDR value (`nilai_asal × kurs_idr`) |

### Usage:
When registering a received postcard:
1. Enter the stamp value in original currency
2. Enter the current exchange rate
3. The IDR value is calculated automatically

---

## China Viewer Detection

The app automatically detects if a visitor is from China to show the appropriate map.

### Detection Methods (in order):

1. **URL Parameter**: `?china=1` or `?china=0` (manual override)
2. **Session**: Previous detection is cached
3. **Cloudflare Header**: `CF-IPCountry` header (if using Cloudflare)

### Why This Matters:
- Google Maps is blocked in mainland China
- Chinese visitors need AMap for maps to load
- The arrival confirmation page (`/receive/{uid}`) uses this detection

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

Recipient email is set in `app/Livewire/ReceiveConfirm.php`:
```php
$message->to('your_email@example.com')
```
