# International Usage Guide 🌍

This project was originally developed with an Indonesian context. While the UI is primarily in English, some internal components (database schema, currency logic) use Indonesian conventions. This guide helps international users adapt the system.

## 💱 Currency System (IDR)

The application uses **IDR (Indonesian Rupiah)** as the internal base currency for cost tracking.

### How to use with your own currency:
You don't necessarily need to change the code to use your local currency. You can treat the "IDR" fields as your "Home Currency":
1. When the app asks for an "Exchange Rate to IDR", enter the rate from the foreign currency to **your local currency**.
2. All calculations (`Original Value` × `Rate`) will result in your local currency value, even if the label says IDR.

### Hardcoded Logic:
- `app/Services/CurrencyService.php`: Hardcoded to fetch rates against IDR.
- `app/Models/Postcard.php`: Casts column `kurs_idr`.

---

## 🗺️ Database Translation Map

If you are modifying the database or reading the source code, here is a translation of the Indonesian column names:

| Indonesian Column | English Context |
|-------------------|-----------------|
| `tanggal_kirim` | Send Date |
| `tanggal_terima` | Receive Date |
| `nama_kontak` | Contact Name |
| `biaya_prangko` | Stamp Cost (Base Currency) |
| `nilai_asal` | Original Value (Foreign Currency) |
| `mata_uang` | Currency Code |
| `kurs_idr` | Exchange Rate (to Base) |
| `foto_depan` | Front Photo |
| `foto_belakang` | Back Photo |
| `nama_indonesia` | Country Name (Indonesian) |

---

## 🌍 Country Mapping

The app maps postcards to countries using the `nama_indonesia` field in the `countries` table. 

- **Internal Lookup**: If you import a CSV, ensure the "negara" (country) column matches the `nama_indonesia` list in your database.
- **Display**: The UI prioritizes `nama_inggris` (English Name) where possible, but internal relations rely on the Indonesian name.

---

## 🛠️ Contribution
We are working towards making the database schema and currency service fully dynamic. If you'd like to help translate the remaining Indonesian comments or refactor the currency logic, pull requests are welcome!
