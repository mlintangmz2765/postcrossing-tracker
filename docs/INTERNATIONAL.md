# International Usage Guide 🌍

This project was originally developed with an Indonesian context. While the UI is primarily in English, some internal components (database schema, currency logic) use Indonesian conventions. This guide helps international users adapt the system.

## 💱 Currency, Timezone & Date Format

| Variable | Default | Purpose |
|----------|---------|---------|
| `APP_BASE_CURRENCY` | `IDR` | Currency costs are recorded and reported in. Rates are fetched to this currency (e.g. `USD`, `EUR`). |
| `APP_TIMEZONE` | `UTC` | Decides which day an arrival confirmation is dated. |
| `APP_DATE_FORMAT` | `d/m/Y` | How dates are shown and typed (`m/d/Y` for the US). |
| `OWNER_LOCATION` | _(unset)_ | Where your cards are sent from; shown on the arrival page and home page. |

Display formats per currency (symbol, decimals, separators) are in `config/currency.php`; the base currency's separators are also used for distances.

Each postcard stores the `base_currency` it was recorded in. Changing `APP_BASE_CURRENCY` later does not convert old cards; totals only include cards in the current base currency. Rows that existed before this column was added are marked `IDR`. If you previously followed the old advice here and entered amounts in your own currency while the labels said IDR, correct them once after migrating, e.g. `UPDATE postcards SET base_currency = 'EUR';`.

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
| `base_currency` | Base Currency of `biaya_prangko` | |
| `exchange_rate` | Exchange Rate (to Base) | Formerly `kurs_idr` |
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
- **Validation**: When registering or importing, the app matches the English name (`nama_inggris`), the Indonesian name (`nama_indonesia`) or the ISO code (`kode_iso`), case-insensitively.
- **Display**: The UI shows `nama_inggris` (English), falling back to `nama_indonesia`.

---

## � CSV Import Structure

The Mass Data Import feature uses a CSV file with Indonesian header names. Here is the translation:

| CSV Column | English Context | Notes |
|------------|-----------------|-------|
| `type` | Type | "sent" or "received" |
| `postcard_id` | Postcrossing ID | e.g., ID-123456 |
| `tgl_kirim` | Send Date | DD/MM/YYYY or YYYY-MM-DD |
| `tgl_terima` | Receive Date | DD/MM/YYYY or YYYY-MM-DD (optional) |
| `deskripsi` | Description | Subject or picture description |
| `nama` | Contact Name | |
| `alamat` | Address | Full address for geocoding |
| `negara` | Country | English or Indonesian name, or ISO code (e.g. `Germany`, `Jerman`, `DE`) |
| `telepon` | Phone Number | **ONLY for "sent" type (11 cols)** |
| `biaya_asal` | Original Cost | Digits only (e.g., 10000 or 1.50) |
| `mata_uang` | Currency Code | ISO 4217 code (USD, EUR, CNY, etc.) |

> ⚠️ **Important**: The semicolon (`;`) is used as the column separator, not a comma.

---

## 🖼️ UI / User-Facing Layer

### Home Page Language Switcher
The **Public Home Page** (`resources/views/livewire/home.blade.php`) includes a built-in language switcher supporting:
- **ID** (Bahasa Indonesia)
- **EN** (English)
- **CN** (Chinese / 中文)

The default language follows `APP_LOCALE` (`id`, `en` or `zh`; anything else falls back to `en`), and a visitor's choice is stored in `localStorage`. The greeting uses `OWNER_LOCATION`. The rest of the page text is the original author's personal introduction, so edit it in `home.blade.php` for your own site.

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
