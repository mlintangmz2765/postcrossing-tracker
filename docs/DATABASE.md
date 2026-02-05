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
│ updated_at   │       │ postcard_id      │
└──────────────┘       │ tanggal_kirim    │       ┌──────────────────┐
                       │ tanggal_terima   │       │    contacts      │
                       │ deskripsi_gambar │       ├──────────────────┤
                       │ pesan_penerima   │       │ id (PK)          │
                       │ nama_kontak      │       │ user_id (FK)     │
                       │ alamat           │       │ nama_kontak      │
                       │ negara           │       │ alamat           │
                       │ nomor_telepon    │       │ negara           │
                       │ biaya_prangko    │       │ nomor_telepon    │
                       │ nilai_asal       │       │ updated_at       │
                       │ mata_uang        │       └──────────────────┘
                       │ kurs_idr         │
                       │ lat              │       ┌──────────────────┐
                       │ lng              │       │   countries      │
                       │ foto_depan       │       ├──────────────────┤
                       │ foto_belakang    │       │ id (PK)          │
                       │ notif_read       │       │ nama_indonesia   │
                       └──────────────────┘       │ nama_inggris     │
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
| type | ENUM | 'sent' or 'received' |
| postcard_id | VARCHAR(50) | Postcrossing ID (e.g., ID-447230) |
| tanggal_kirim | DATE | Send date |
| tanggal_terima | DATE | Receive date (null if not arrived) |
| deskripsi_gambar | TEXT | Image description |
| pesan_penerima | TEXT | Message from recipient |
| nama_kontak | VARCHAR(100) | Contact name |
| alamat | TEXT | Address |
| negara | VARCHAR(100) | Country name |
| nomor_telepon | VARCHAR(20) | Phone number |
| biaya_prangko | DECIMAL(15,2) | Stamp cost in IDR |
| nilai_asal | DECIMAL(15,2) | Original value in foreign currency |
| mata_uang | VARCHAR(5) | Currency code |
| kurs_idr | DECIMAL(15,2) | Exchange rate to IDR |
| lat | VARCHAR(50) | Latitude coordinate |
| lng | VARCHAR(50) | Longitude coordinate |
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
| nama_kontak | VARCHAR(100) | Contact name |
| alamat | TEXT | Address |
| negara | VARCHAR(100) | Country |
| nomor_telepon | VARCHAR(20) | Phone number |
| updated_at | TIMESTAMP | Last update time |

**Unique constraint:** (user_id, nama_kontak)

### countries
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | Auto-increment ID |
| nama_indonesia | VARCHAR(100) | Country name in Indonesian |
| nama_inggris | VARCHAR(100) | Country name in English |
| kode_iso | CHAR(2) | ISO 3166-1 alpha-2 code |
| benua | VARCHAR(50) | Continent name |
| subbenua | VARCHAR(50) | Subregion name |
