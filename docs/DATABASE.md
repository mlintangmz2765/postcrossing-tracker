# Database Schema

## Entity Relationship Diagram

```
┌──────────────┐       ┌──────────────────┐       ┌──────────────────┐
│    users     │       │    postcards     │       │ postcard_stamps  │
├──────────────┤       ├──────────────────┤       ├──────────────────┤
│ id (PK)      │◄──────│ user_id (FK)     │       │ id (PK)          │
│ username     │       │ id (PK)          │◄──────│ postcard_id (FK) │
│ password     │       │ uid              │       │ foto_prangko     │
│ created_at   │       │ type             │       └──────────────────┘
│ updated_at   │       │ contact_id (FK)──┼───────────┐
└──────────────┘       │ country_id (FK)──┼────────┐  │
                       │ postcard_id      │        │  │
                       │ tanggal_kirim    │        │  │
                       │ tanggal_terima   │        │  │
                       │ deskripsi_gambar │        │  │
                       │ pesan_penerima   │        │  │
                       │ biaya_prangko    │        │  │ ┌──────────────────┐
                       │ nilai_asal       │        │  │ │    contacts      │
                       │ mata_uang        │        │  │ ├──────────────────┤
                       │ exchange_rate    │        │  └►│ id (PK)          │
                       │ foto_depan       │        │    │ user_id (FK)     │
                       │ foto_belakang    │        │    │ nama_kontak 🔒   │
                       │ notif_read       │        │    │ alamat 🔒        │
                       └──────────────────┘        │    │ nomor_telepon 🔒 │
                                                   │    │ lat 🔒           │
                       ┌──────────────────┐        │    │ lng 🔒           │
                       │   countries      │        │    │ country_id (FK)──┤
                       ├──────────────────┤        │    │ updated_at       │
                       │ id (PK)          │◄───────┘    └──────────────────┘
                       │ nama_indonesia   │
                       │ nama_inggris     │
                       │ kode_iso         │
                       │ benua            │
                       │ subbenua         │
                       └──────────────────┘
```

## Tables

### users
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment ID |
| username | VARCHAR(50) | Unique username |
| password | VARCHAR(255) | Bcrypt hashed password |
| created_at | TIMESTAMP | Account creation time |
| updated_at | TIMESTAMP | Last update time |

### postcards
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment ID |
| uid | VARCHAR(50) | Unique identifier for arrival confirmation |
| user_id | INT (FK) | References users.id |
| contact_id | INT (FK) | References contacts.id |
| country_id | INT (FK) | References countries.id |
| type | ENUM | 'sent' or 'received' |
| postcard_id | VARCHAR(50) | Postcrossing ID (e.g., ID-447230) |
| tanggal_kirim | DATE | Send date |
| tanggal_terima | DATE | Receive date (null if not arrived) |
| deskripsi_gambar | TEXT | Image description |
| pesan_penerima | TEXT | Message from recipient |
| biaya_prangko | DECIMAL(15,2) | Stamp cost in the card's base currency |
| nilai_asal | DECIMAL(15,2) | Original value in foreign currency |
| mata_uang | VARCHAR(5) | Currency code |
| base_currency | VARCHAR(3) | Base currency `biaya_prangko` is in (APP_BASE_CURRENCY when recorded; IDR for rows created before it existed) |
| exchange_rate | DECIMAL(20,10) | Exchange rate from `mata_uang` to `base_currency` (formerly `kurs_idr`) |
| foto_depan | VARCHAR(255) | Front image path |
| foto_belakang | VARCHAR(255) | Back image path |
| notif_read | TINYINT(1) | Notification read status |

### postcard_stamps
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment ID |
| postcard_id | INT (FK) | References postcards.id (CASCADE DELETE) |
| foto_prangko | VARCHAR(255) | Stamp image path |

### contacts
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment ID |
| user_id | INT (FK) | References users.id |
| country_id | INT (FK) | References countries.id |
| nama_kontak | VARCHAR(100) | Contact name |
| alamat | TEXT | Address **🔒 Encrypted at rest** |
| nomor_telepon | VARCHAR(20) | Phone number **🔒 Encrypted at rest** |
| lat | TEXT | Latitude coordinate **🔒 Encrypted at rest** |
| lng | TEXT | Longitude coordinate **🔒 Encrypted at rest** |
| updated_at | TIMESTAMP | Last update time |

### Encryption & Privacy
Sensitive data like addresses, phone numbers, and coordinates are encrypted at rest using AES-256. We centralized the location data in the `contacts` table to keep things consistent across the app.

### countries
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment ID |
| nama_indonesia | VARCHAR(100) | Country name in Indonesian |
| nama_inggris | VARCHAR(100) | Country name in English |
| kode_iso | CHAR(2) | ISO 3166-1 alpha-2 code |
| benua | VARCHAR(50) | Continent name |
| subbenua | VARCHAR(50) | Subregion name |
