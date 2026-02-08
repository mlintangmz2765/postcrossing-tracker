# International Usage Guide 🌍

This project was originally developed with an Indonesian context. While the UI is primarily in English, some internal components (database schema, currency logic) use Indonesian conventions. This guide helps international users adapt the system.

## 💱 Currency System (IDR)

The application uses **IDR (Indonesian Rupiah)** as the internal base currency for cost tracking.

### How to use with your own currency:
You don't necessarily need to change the code to use your local currency. You can treat the "IDR" fields as your "Home Currency":
1. When the app asks for an "Exchange Rate to IDR", enter the rate from the foreign currency to **your local currency**.
2. All calculations (`Original Value` × `Rate`) will result in your local currency value, even if the label says IDR.

### Hardcoded Logic:
- **Automated Fetching**: `app/Services/CurrencyService.php` is hardcoded to fetch exchange rates against **IDR**. If you use a different base currency, the automatic "Fetch Rate" feature will still retrieve IDR rates.
- **Model Casting**: `app/Models/Postcard.php` casts the exchange rate column as `kurs_idr`.
- **Database Column**: The column is named `kurs_idr` in the `postcards` table.

> 🛠️ **Developer Note:** To change the automated base currency, you need to modify the `getHistoricalRate` method in `app/Services/CurrencyService.php` and replace all instances of `'IDR'` with your preferred ISO currency code.

---

## 🗺️ Database Translation Map

Below is a translation of all Indonesian column names across all tables. Use this as a reference when modifying the database or reading the source code.

### `postcards` Table (Primary data for sent/received postcards)
| Indonesian Column | English Context | Notes |
|-------------------|-----------------|-------|
| `contact_id` | Contact Reference | Links to `contacts.id` |
| `country_id` | Country Reference | Links to `countries.id` |
| `tanggal_kirim` | Send Date | |
| `tanggal_terima` | Receive Date | |
| `deskripsi_gambar` | Image Description | |
| `pesan_penerima` | Message from Recipient | |
| `biaya_prangko` | Stamp Cost (Base Currency) | |
| `nilai_asal` | Original Value (Foreign Currency) | |
| `mata_uang` | Currency Code | |
| `kurs_idr` | Exchange Rate (to Base) | |
| `foto_depan` | Front Photo | |
| `foto_belakang` | Back Photo | |
| `notif_read` | Notification Read Status | |

### `contacts` Table (Address book & PII Storage)
| Indonesian Column | English Context | Notes |
|-------------------|-----------------|-------|
| `nama_kontak` | Contact Name | **🔒 Encrypted** |
| `alamat` | Address | **🔒 Encrypted** |
| `country_id` | Country ID | Links to `countries.id` |
| `nomor_telepon` | Phone Number | **🔒 Encrypted** |
| `lat` | Latitude | **🔒 Encrypted** |
| `lng` | Longitude | **🔒 Encrypted** |

### `countries` Table (Country data for maps & stats)
| Indonesian Column | English Context |
|-------------------|-----------------|
| `nama_indonesia` | Country Name (Indonesian) |
| `nama_inggris` | Country Name (English) |
| `kode_iso` | ISO 3166-1 Alpha-2 Code |
| `benua` | Continent |
| `subbenua` | Subregion / Subcontinent |

### `postcard_stamps` Table (Stamp photos for each postcard)
| Indonesian Column | English Context |
|-------------------|-----------------|
| `foto_prangko` | Stamp Photo |

---

## 🌍 Country Mapping

The application uses a strict relational mapping system:
- **Foreign Key**: Postcards and Contacts link to the `countries` table via `country_id`.
- **Validation**: When registering or importing, the app looks up the `nama_indonesia` field in the `countries` table to find the correct ID.
- **Display**: The UI uses `nama_indonesia` for internal logic but can display `nama_inggris` (English) in reports.

---

## � CSV Import Structure

The Mass Data Import feature uses a CSV file with Indonesian header names. Here is the translation:

| CSV Column | English Context | Notes |
|------------|-----------------|-------|
| `type` | Type | "sent" or "received" |
| `postcard_id` | Postcrossing ID | e.g., ID-123456 |
| `tgl_kirim` | Send Date | DD/MM/YYYY |
| `tgl_terima` | Receive Date | DD/MM/YYYY (optional) |
| `deskripsi` | Description | Subject or picture description |
| `nama` | Contact Name | |
| `alamat` | Address | Full address for geocoding |
| `negara` | Country | Must match `nama_indonesia` in `countries` table |
| `telepon` | Phone Number | **ONLY for "sent" type (11 cols)** |
| `biaya_asal` | Original Cost | Digits only (e.g., 10000 or 1.50) |
| `mata_uang` | Currency Code | ISO 4217 code (USD, EUR, CNY, etc.) |

> ⚠️ **Important**: The semicolon (`;`) is used as the column separator, not a comma.

---

## 🖼️ UI / User-Facing Layer

### Home Page Language Switcher
The **Public Home Page** (`resources/views/livewire/home.blade.php`) defaults to **Indonesian (ID)** but includes a built-in language switcher supporting:
- **ID** (Bahasa Indonesia) - Default
- **EN** (English)
- **CN** (Chinese / 中文)

The language preference is stored in `localStorage`. If you want to change the default language to English, modify line 517:
```javascript
const savedLang = localStorage.getItem('preferredLang') || 'en'; // Change 'id' to 'en'
```

### Stamp Decoration Text
The decorative CSS stamp on the home page displays `"IDN"` (ISO code for Indonesia). This is purely cosmetic (CSS `::after` content) in line 255 of `home.blade.php`.

---

## 💻 Source Code Layer

### Indonesian CSS Comments
Several Blade files contain **Indonesian CSS comments**. These are development notes and do not affect functionality:

| File | Example Comment |
|------|-----------------|
| `home.blade.php` | `/* --- SETUP FONT LOKAL --- */`, `/* --- DEKORASI PRANGKO CSS --- */` |
| `public-gallery.blade.php` | `/* --- CSS UTAMA --- */` |

### Image Asset Names
Some image files are named in Indonesian:
- `images/prangko.png` → "Stamp" image
- `images/cardboard.png` → Cardboard texture

These are purely cosmetic and do not require changes.

---

## 🛠️ Contribution
We are working towards making the database schema and currency service fully dynamic. If you'd like to help translate the remaining Indonesian comments or refactor the currency logic, pull requests are welcome!
