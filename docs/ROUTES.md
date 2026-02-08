# Routes Documentation

## Public Routes (No Authentication Required)

| Method | URI | Component | Description |
|--------|-----|-----------|-------------|
| GET | `/` | `Home` | Landing page with language switcher |
| GET | `/login` | `Login` | User login page |
| GET | `/gallery` | `PublicGallery` | Public postcard gallery |
| GET | `/receive/{uid}` | `ReceiveConfirm` | Postcard arrival confirmation page |

## Protected Routes (Authentication Required)

| Method | URI | Component | Description |
|--------|-----|-----------|-------------|
| GET | `/dashboard` | `Dashboard` | Main dashboard with statistics |
| GET | `/new` | `RegisterPostcard` | Register new postcard |
| GET | `/import` | `ImportPostcards` | Bulk import postcards from CSV |
| GET | `/view/{id}` | `PostcardView` | View postcard details |
| GET | `/edit/{id}` | `EditPostcard` | Edit existing postcard |
| GET | `/stats` | `Statistics` | Detailed statistics view |
| GET | `/postcard-gallery` | `PostcardGallery` | Private postcard gallery |
| GET | `/stamps` | `StampGallery` | Stamp collection gallery |
| POST | `/logout` | - | Logout action |

## Route Middleware

- `auth` - Requires authenticated session
- `guest` - Only accessible when not logged in

## Livewire Components

All pages are built with Livewire components located in `app/Livewire/`:

```
app/Livewire/
├── Dashboard.php          # Main dashboard
├── EditPostcard.php       # Edit postcard form
├── Home.php               # Landing page
├── ImportPostcards.php    # CSV bulk import
├── Login.php              # Authentication
├── PostcardGallery.php    # Private gallery
├── PostcardView.php       # Single postcard view
├── PublicGallery.php      # Public gallery
├── ReceiveConfirm.php     # Arrival confirmation
├── RegisterPostcard.php   # New postcard form
├── StampGallery.php       # Stamp collection
└── Statistics.php         # Statistics page
```
